<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\Expense;
use App\Models\Payment;
use App\Support\Analytics;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\Response;

class MonitoringController extends Controller
{
    /**
     * Paginate a collection
     */
    private function paginateCollection($collection, $perPage = 10)
    {
        $page = request()->get('page', 1);
        $offset = ($page - 1) * $perPage;
        
        $items = $collection->slice($offset, $perPage)->values();
        
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $collection->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    /**
     * Rental Monitoring Dashboard
     */
    public function rental(Request $request): View
    {
        $tenants = Tenant::with(['propertyModel', 'roomModel', 'payments'])
            ->orderBy('name')
            ->get();

        // Calculate total payable (only positive balances)
        $totalPayable = (float) Tenant::where('balance', '>', 0)->sum('balance');
        
        // Calculate total sales from collected payments
        $query = Payment::where('status', 'paid');
        if ($request->filled('date_from')) {
            $query->where('paid_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('paid_date', '<=', $request->date_to);
        }
        $totalSales = (float) $query->sum('amount');

        // Calculate sales by payment method
        $query = Payment::where('status', 'paid');
        if ($request->filled('date_from')) {
            $query->where('paid_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('paid_date', '<=', $request->date_to);
        }
        $salesByMethod = $query->get(['amount', 'method'])
            ->groupBy(fn (Payment $payment) => $payment->method ?: 'unspecified')
            ->map(fn ($payments) => (float) $payments->sum('amount'))
            ->filter(fn (float $amount) => $amount > 0)
            ->all();

        // Calculate total expenses from expenses table
        $query = Expense::query();
        if ($request->filled('date_from')) {
            $query->where('expense_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('expense_date', '<=', $request->date_to);
        }
        $totalExpenses = (float) $query->sum('amount');

        $netIncome = $totalSales - $totalExpenses;

        $query = Transaction::byAccount('rental')->whereIn('module_type', ['income', 'payment']);
        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }
        $rentalTransactions = $query->get()
            ->map(fn (Transaction $transaction) => (object) [
                'date' => $transaction->transaction_date,
                'property_unit' => $transaction->notes,
                'tenant' => $transaction->description,
                'received' => $transaction->amount,
                'description' => $transaction->description,
                'transaction_date' => $transaction->transaction_date,
                'amount' => $transaction->amount,
                'module_type' => $transaction->module_type,
                'transaction_id' => $transaction->id,
                'payment_id' => null,
            ]);

        $query = Payment::with(['tenantModel', 'propertyModel', 'roomModel'])
            ->where('status', 'paid')
            ->whereNotNull('paid_date');
        if ($request->filled('date_from')) {
            $query->where('paid_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('paid_date', '<=', $request->date_to);
        }
        $paymentTransactions = $query->get()
            ->map(function (Payment $payment) {
                $tenant = $payment->tenantModel?->name;
                $unit = collect([
                    $payment->propertyModel?->name,
                    $payment->roomModel?->unit,
                ])->filter()->implode(' / ');

                return (object) [
                    'date' => $payment->paid_date,
                    'property_unit' => $unit,
                    'tenant' => $tenant ?: 'N/A',
                    'received' => $payment->amount,
                    'description' => trim('Payment received' . ($tenant ? ' - ' . $tenant : '')),
                    'transaction_date' => $payment->paid_date,
                    'amount' => $payment->amount,
                    'module_type' => 'payment',
                    'notes' => $unit,
                    'transaction_id' => null,
                    'payment_id' => $payment->id,
                ];
            });

        $recentTransactions = collect($rentalTransactions->all())
            ->merge($paymentTransactions->all())
            ->sortByDesc(fn ($transaction) => $transaction->transaction_date)
            ->values();

        $recentTransactions = $this->paginateCollection($recentTransactions, 10);

        $query = Expense::with(['buildingModel', 'roomModel']);
        if ($request->filled('date_from')) {
            $query->where('expense_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('expense_date', '<=', $request->date_to);
        }
        $expenses = $query->latest('expense_date')->paginate(10);

        return view('monitoring.rental', [
            'tenants' => $tenants,
            'stats' => [
                'total_payable' => $totalPayable,
                'total_sales' => $totalSales,
                'total_expenses' => $totalExpenses,
                'net_income' => $netIncome,
            ],
            'salesByMethod' => $salesByMethod,
            'recentTransactions' => $recentTransactions,
            'expenses' => $expenses,
            'filters' => $request->only(['date_from', 'date_to']),
            'revenueChart' => Analytics::revenueChart(),
        ]);
    }

    /**
     * Generate Rental Report as JPG
     */
    public function rentalReport(Request $request): Response
    {
        $month = $request->get('month');
        if (!$month) {
            return response('Month parameter is required', 400);
        }

        $date = \Carbon\Carbon::parse($month . '-01');
        $startDate = $date->copy()->startOfMonth();
        $endDate = $date->copy()->endOfMonth();

        // Get sales by payment method for the month
        $salesByMethod = Payment::where('status', 'paid')
            ->whereBetween('paid_date', [$startDate, $endDate])
            ->get(['amount', 'method'])
            ->groupBy(fn (Payment $payment) => $payment->method ?: 'unspecified')
            ->map(fn ($payments) => (float) $payments->sum('amount'))
            ->filter(fn (float $amount) => $amount > 0)
            ->all();

        // Get expenses by category for the month
        $expensesByCategory = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->get(['amount', 'category'])
            ->groupBy('category')
            ->map(fn ($expenses) => (float) $expenses->sum('amount'))
            ->all();

        $totalSales = array_sum($salesByMethod);
        $totalExpenses = array_sum($expensesByCategory);
        $netIncome = $totalSales - $totalExpenses;

        // Generate JPG image
        $image = imagecreatetruecolor(800, 600);
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        $gray = imagecolorallocate($image, 128, 128, 128);
        $red = imagecolorallocate($image, 255, 0, 0);

        imagefill($image, 0, 0, $white);

        // Try to use system font for better character support
        $fontFile = 'C:\Windows\Fonts\arial.ttf';
        if (!file_exists($fontFile)) {
            $fontFile = 'C:\Windows\Fonts\arialbd.ttf';
        }

        // Title
        $monthName = $date->format('F Y');
        if (file_exists($fontFile)) {
            $titleBox = imagettfbbox(24, 0, $fontFile, 'Rental Monitoring');
            $titleWidth = abs($titleBox[4] - $titleBox[0]);
            $titleX = (800 - $titleWidth) / 2;
            imagettftext($image, 24, 0, $titleX, 50, $black, $fontFile, 'Rental Monitoring');
            
            $subtitleBox = imagettfbbox(18, 0, $fontFile, 'For the month of ' . $monthName);
            $subtitleWidth = abs($subtitleBox[4] - $subtitleBox[0]);
            $subtitleX = (800 - $subtitleWidth) / 2;
            imagettftext($image, 18, 0, $subtitleX, 80, $gray, $fontFile, 'For the month of ' . $monthName);
        } else {
            imagestring($image, 5, 300, 30, 'Rental Monitoring', $black);
            imagestring($image, 4, 280, 60, 'For the month of ' . $monthName, $gray);
        }

        // Separator
        imageline($image, 50, 100, 750, 100, $black);

        // Sales section
        $y = 140;
        if (file_exists($fontFile)) {
            imagettftext($image, 16, 0, 50, $y, $black, $fontFile, 'Total Sales');
        } else {
            imagestring($image, 4, 50, $y, 'Total Sales', $black);
        }
        $y += 30;

        foreach ($salesByMethod as $method => $amount) {
            $methodName = ucfirst($method);
            $amountStr = number_format($amount, 2);
            if (file_exists($fontFile)) {
                imagettftext($image, 14, 0, 50, $y, $black, $fontFile, $methodName . ':');
                imagettftext($image, 14, 0, 600, $y, $black, $fontFile, '₱' . $amountStr);
            } else {
                imagestring($image, 3, 50, $y, $methodName . ':', $black);
                imagestring($image, 3, 600, $y, 'PHP ' . $amountStr, $black);
            }
            $y += 25;
        }

        $totalSalesStr = number_format($totalSales, 2);
        if (file_exists($fontFile)) {
            imagettftext($image, 14, 0, 50, $y, $black, $fontFile, 'Total:');
            imagettftext($image, 14, 0, 600, $y, $black, $fontFile, '₱' . $totalSalesStr);
        } else {
            imagestring($image, 3, 50, $y, 'Total:', $black);
            imagestring($image, 3, 600, $y, 'PHP ' . $totalSalesStr, $black);
        }
        $y += 40;

        // Separator
        imageline($image, 50, $y, 750, $y, $black);
        $y += 30;

        // Expenses section
        if (file_exists($fontFile)) {
            imagettftext($image, 16, 0, 50, $y, $black, $fontFile, 'Total Expenses');
        } else {
            imagestring($image, 4, 50, $y, 'Total Expenses', $black);
        }
        $y += 30;

        foreach ($expensesByCategory as $category => $amount) {
            $categoryName = ucfirst($category);
            $amountStr = number_format($amount, 2);
            if (file_exists($fontFile)) {
                imagettftext($image, 14, 0, 50, $y, $black, $fontFile, $categoryName . ':');
                imagettftext($image, 14, 0, 600, $y, $black, $fontFile, '₱' . $amountStr);
            } else {
                imagestring($image, 3, 50, $y, $categoryName . ':', $black);
                imagestring($image, 3, 600, $y, 'PHP ' . $amountStr, $black);
            }
            $y += 25;
        }

        $totalExpensesStr = number_format($totalExpenses, 2);
        if (file_exists($fontFile)) {
            imagettftext($image, 14, 0, 50, $y, $black, $fontFile, 'Total:');
            imagettftext($image, 14, 0, 600, $y, $black, $fontFile, '₱' . $totalExpensesStr);
        } else {
            imagestring($image, 3, 50, $y, 'Total:', $black);
            imagestring($image, 3, 600, $y, 'PHP ' . $totalExpensesStr, $black);
        }
        $y += 40;

        // Separator
        imageline($image, 50, $y, 750, $y, $black);
        $y += 30;

        // Net Income
        $netIncomeStr = number_format($netIncome, 2);
        $netIncomeColor = $netIncome >= 0 ? $black : $red;
        if (file_exists($fontFile)) {
            imagettftext($image, 18, 0, 50, $y, $netIncomeColor, $fontFile, 'Net Income:');
            imagettftext($image, 18, 0, 600, $y, $netIncomeColor, $fontFile, '₱' . $netIncomeStr);
        } else {
            imagestring($image, 5, 50, $y, 'Net Income:', $netIncomeColor);
            imagestring($image, 5, 600, $y, 'PHP ' . $netIncomeStr, $netIncomeColor);
        }

        // Output image
        ob_start();
        imagejpeg($image);
        $imageData = ob_get_clean();
        imagedestroy($image);

        return response($imageData)
            ->header('Content-Type', 'image/jpeg')
            ->header('Content-Disposition', 'attachment; filename="rental-report-' . $month . '.jpg"');
    }

    /**
     * Agriculture Monitoring Dashboard
     */
    public function agriculture(Request $request): View
    {
        $query = Transaction::byAccount('agriculture')->where('module_type', 'income')->where('status', 'completed');
        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }
        $totalSales = $query->sum('amount');

        $query = Transaction::byAccount('agriculture')->where('module_type', 'expense')->where('status', 'completed');
        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }
        $totalExpenses = $query->sum('amount');

        $netIncome = $totalSales - $totalExpenses;
        $balance = $totalSales - $totalExpenses;

        // Separate sales and expenses transactions
        $query = Transaction::byAccount('agriculture')->where('module_type', 'income');
        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }
        $salesTransactions = $query->latest('transaction_date')->paginate(10);

        $query = Transaction::byAccount('agriculture')->where('module_type', 'expense');
        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }
        $expenseTransactions = $query->latest('transaction_date')->paginate(10);

        return view('monitoring.agriculture', [
            'stats' => [
                'total_sales' => $totalSales,
                'total_expenses' => $totalExpenses,
                'net_income' => $netIncome,
                'balance' => $balance,
            ],
            'salesTransactions' => $salesTransactions,
            'expenseTransactions' => $expenseTransactions,
            'filters' => $request->only(['date_from', 'date_to']),
            'revenueChart' => Analytics::agricultureRevenueChart(),
        ]);
    }

    /**
     * Tilapia Monitoring Dashboard
     */
    public function tilapia(Request $request): View
    {
        $query = Transaction::byAccount('tilapia')->where('module_type', 'income')->where('status', 'completed');
        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }
        $totalSales = $query->sum('amount');

        $query = Transaction::byAccount('tilapia')->where('module_type', 'expense')->where('status', 'completed');
        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }
        $totalExpenses = $query->sum('amount');

        $netIncome = $totalSales - $totalExpenses;
        $balance = $totalSales - $totalExpenses;

        // Separate sales and expenses transactions
        $query = Transaction::byAccount('tilapia')->where('module_type', 'income');
        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }
        $salesTransactions = $query->latest('transaction_date')->paginate(10);

        $query = Transaction::byAccount('tilapia')->where('module_type', 'expense');
        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }
        $expenseTransactions = $query->latest('transaction_date')->paginate(10);

        return view('monitoring.tilapia', [
            'stats' => [
                'total_sales' => $totalSales,
                'total_expenses' => $totalExpenses,
                'net_income' => $netIncome,
                'balance' => $balance,
            ],
            'salesTransactions' => $salesTransactions,
            'expenseTransactions' => $expenseTransactions,
            'filters' => $request->only(['date_from', 'date_to']),
            'revenueChart' => Analytics::tilapiaRevenueChart(),
        ]);
    }

    /**
     * Conel Monitoring Dashboard (Account Tracking)
     */
    public function conel(Request $request): View
    {
        $totalIncome = Transaction::getTotalIncome('conel');
        $totalExpenses = Transaction::getTotalExpenses('conel');
        $balance = Transaction::getBalance('conel');

        // Separate deposit and withdraw transactions
        $query = Transaction::byAccount('conel')->where('module_type', 'income');
        $depositTransactions = $query->latest('transaction_date')->paginate(10);

        $query = Transaction::byAccount('conel')->where('module_type', 'expense');
        $withdrawTransactions = $query->latest('transaction_date')->paginate(10);

        return view('monitoring.conel', [
            'stats' => [
                'total_income' => $totalIncome,
                'total_expenses' => $totalExpenses,
                'balance' => $balance,
            ],
            'depositTransactions' => $depositTransactions,
            'withdrawTransactions' => $withdrawTransactions,
        ]);
    }

    /**
     * 128 Monitoring Dashboard (Account Tracking)
     */
    public function oneTwoEight(Request $request): View
    {
        $totalIncome = Transaction::getTotalIncome('128');
        $totalExpenses = Transaction::getTotalExpenses('128');
        $balance = Transaction::getBalance('128');

        // Separate deposit and withdraw transactions
        $query = Transaction::byAccount('128')->where('module_type', 'income');
        $depositTransactions = $query->latest('transaction_date')->paginate(10);

        $query = Transaction::byAccount('128')->where('module_type', 'expense');
        $withdrawTransactions = $query->latest('transaction_date')->paginate(10);

        return view('monitoring.128', [
            'stats' => [
                'total_income' => $totalIncome,
                'total_expenses' => $totalExpenses,
                'balance' => $balance,
            ],
            'depositTransactions' => $depositTransactions,
            'withdrawTransactions' => $withdrawTransactions,
        ]);
    }

    /**
     * Add transaction for any monitoring type
     */
    public function storeTransaction(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'account_type' => ['required', 'in:conel,128,rental,agriculture,tilapia'],
            'module_type' => ['required', 'in:income,expense,payment,balance_adjustment'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'description' => ['required', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        Transaction::create($validated);

        return redirect()->back()->with('success', 'Transaction recorded.');
    }

    /**
     * Show agriculture sales creation form
     */
    public function createAgricultureSales(): View
    {
        return view('monitoring.agriculture-sales-create', [
            'transaction' => new Transaction([
                'account_type' => 'agriculture',
                'module_type' => 'income',
                'transaction_date' => now()->format('Y-m-d'),
            ]),
        ]);
    }

    /**
     * Store agriculture sales
     */
    public function storeAgricultureSales(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'description' => ['nullable', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        Transaction::create([
            'account_type' => 'agriculture',
            'module_type' => 'income',
            ...$validated,
        ]);

        return redirect()->route('monitoring.agriculture')->with('success', 'Sales recorded.');
    }

    /**
     * Show agriculture sales edit form
     */
    public function editAgricultureSales(Transaction $transaction): View
    {
        if ($transaction->account_type !== 'agriculture' || $transaction->module_type !== 'income') {
            abort(404);
        }

        return view('monitoring.agriculture-sales-edit', [
            'transaction' => $transaction,
        ]);
    }

    /**
     * Update agriculture sales
     */
    public function updateAgricultureSales(Request $request, Transaction $transaction): \Illuminate\Http\RedirectResponse
    {
        if ($transaction->account_type !== 'agriculture' || $transaction->module_type !== 'income') {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'description' => ['nullable', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $transaction->update($validated);

        return redirect()->route('monitoring.agriculture')->with('success', 'Sales updated.');
    }

    /**
     * Delete agriculture sales
     */
    public function destroyAgricultureSales(Request $request, Transaction $transaction): \Illuminate\Http\RedirectResponse
    {
        if ($transaction->account_type !== 'agriculture' || $transaction->module_type !== 'income') {
            abort(404);
        }

        // Verify password
        if (!\Hash::check($request->password, $request->user()->password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        $transaction->delete();

        return redirect()->route('monitoring.agriculture')->with('success', 'Sales deleted.');
    }

    /**
     * Show agriculture expenses creation form
     */
    public function createAgricultureExpenses(): View
    {
        // Get existing categories from database
        $existingCategories = Transaction::where('account_type', 'agriculture')
            ->where('module_type', 'expense')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->pluck('category')
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        // Default categories
        $defaultCategories = ['Feed', 'Fertilizer', 'Labor', 'Equipment', 'Transportation', 'Utilities', 'Maintenance'];

        // Merge and deduplicate
        $allCategories = array_unique(array_merge($defaultCategories, $existingCategories));
        sort($allCategories);

        return view('monitoring.agriculture-expenses-create', [
            'transaction' => new Transaction([
                'account_type' => 'agriculture',
                'module_type' => 'expense',
                'transaction_date' => now()->format('Y-m-d'),
            ]),
            'categories' => $allCategories,
        ]);
    }

    /**
     * Store agriculture expenses
     */
    public function storeAgricultureExpenses(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'pcv_number' => ['required', 'string', 'max:50'],
            'category' => ['required', 'string', 'max:100'],
            'other_category' => ['nullable', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'description' => ['nullable', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Use custom category if "Other" is selected
        if ($validated['category'] === 'Other' && !empty($validated['other_category'])) {
            $validated['category'] = $validated['other_category'];
        }

        Transaction::create([
            'account_type' => 'agriculture',
            'module_type' => 'expense',
            ...$validated,
        ]);

        return redirect()->route('monitoring.agriculture')->with('success', 'Expense recorded.');
    }

    /**
     * Show agriculture expenses edit form
     */
    public function editAgricultureExpenses(Transaction $transaction): View
    {
        if ($transaction->account_type !== 'agriculture' || $transaction->module_type !== 'expense') {
            abort(404);
        }

        // Get existing categories from database
        $existingCategories = Transaction::where('account_type', 'agriculture')
            ->where('module_type', 'expense')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->pluck('category')
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        // Default categories
        $defaultCategories = ['Seeds', 'Fertilizer', 'Labor', 'Equipment', 'Transportation', 'Utilities', 'Maintenance', 'Pesticides'];

        // Merge and deduplicate
        $allCategories = array_unique(array_merge($defaultCategories, $existingCategories));
        sort($allCategories);

        return view('monitoring.agriculture-expenses-edit', [
            'transaction' => $transaction,
            'categories' => $allCategories,
        ]);
    }

    /**
     * Update agriculture expenses
     */
    public function updateAgricultureExpenses(Request $request, Transaction $transaction): \Illuminate\Http\RedirectResponse
    {
        if ($transaction->account_type !== 'agriculture' || $transaction->module_type !== 'expense') {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'pcv_number' => ['required', 'string', 'max:50'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $transaction->update($validated);

        return redirect()->route('monitoring.agriculture')->with('success', 'Expense updated.');
    }

    /**
     * Delete agriculture expenses
     */
    public function destroyAgricultureExpenses(Request $request, Transaction $transaction): \Illuminate\Http\RedirectResponse
    {
        if ($transaction->account_type !== 'agriculture' || $transaction->module_type !== 'expense') {
            abort(404);
        }

        // Verify password
        if (!\Hash::check($request->password, $request->user()->password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        $transaction->delete();

        return redirect()->route('monitoring.agriculture')->with('success', 'Expense deleted.');
    }

    /**
     * Show tilapia sales creation form
     */
    public function createTilapiaSales(): View
    {
        return view('monitoring.tilapia-sales-create', [
            'transaction' => new Transaction([
                'account_type' => 'tilapia',
                'module_type' => 'income',
                'transaction_date' => now()->format('Y-m-d'),
            ]),
        ]);
    }

    /**
     * Store tilapia sales
     */
    public function storeTilapiaSales(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'description' => ['nullable', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        Transaction::create([
            'account_type' => 'tilapia',
            'module_type' => 'income',
            ...$validated,
        ]);

        return redirect()->route('monitoring.tilapia')->with('success', 'Sales recorded.');
    }

    /**
     * Show tilapia sales edit form
     */
    public function editTilapiaSales(Transaction $transaction): View
    {
        if ($transaction->account_type !== 'tilapia' || $transaction->module_type !== 'income') {
            abort(404);
        }

        return view('monitoring.tilapia-sales-edit', [
            'transaction' => $transaction,
        ]);
    }

    /**
     * Update tilapia sales
     */
    public function updateTilapiaSales(Request $request, Transaction $transaction): \Illuminate\Http\RedirectResponse
    {
        if ($transaction->account_type !== 'tilapia' || $transaction->module_type !== 'income') {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'description' => ['nullable', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $transaction->update($validated);

        return redirect()->route('monitoring.tilapia')->with('success', 'Sales updated.');
    }

    /**
     * Delete tilapia sales
     */
    public function destroyTilapiaSales(Request $request, Transaction $transaction): \Illuminate\Http\RedirectResponse
    {
        if ($transaction->account_type !== 'tilapia' || $transaction->module_type !== 'income') {
            abort(404);
        }

        // Verify password
        if (!\Hash::check($request->password, $request->user()->password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        $transaction->delete();

        return redirect()->route('monitoring.tilapia')->with('success', 'Sales deleted.');
    }

    /**
     * Show tilapia expenses creation form
     */
    public function createTilapiaExpenses(): View
    {
        // Get existing categories from database
        $existingCategories = Transaction::where('account_type', 'tilapia')
            ->where('module_type', 'expense')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->pluck('category')
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        // Default categories
        $defaultCategories = ['Feed', 'Fingerlings', 'Labor', 'Equipment', 'Transportation', 'Utilities', 'Maintenance', 'Medicine'];

        // Merge and deduplicate
        $allCategories = array_unique(array_merge($defaultCategories, $existingCategories));
        sort($allCategories);

        return view('monitoring.tilapia-expenses-create', [
            'transaction' => new Transaction([
                'account_type' => 'tilapia',
                'module_type' => 'expense',
                'transaction_date' => now()->format('Y-m-d'),
            ]),
            'categories' => $allCategories,
        ]);
    }

    /**
     * Store tilapia expenses
     */
    public function storeTilapiaExpenses(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'pcv_number' => ['required', 'string', 'max:50'],
            'category' => ['required', 'string', 'max:100'],
            'other_category' => ['nullable', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'description' => ['nullable', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Use custom category if "Other" is selected
        if ($validated['category'] === 'Other' && !empty($validated['other_category'])) {
            $validated['category'] = $validated['other_category'];
        }

        Transaction::create([
            'account_type' => 'tilapia',
            'module_type' => 'expense',
            ...$validated,
        ]);

        return redirect()->route('monitoring.tilapia')->with('success', 'Expense recorded.');
    }

    /**
     * Show tilapia expenses edit form
     */
    public function editTilapiaExpenses(Transaction $transaction): View
    {
        if ($transaction->account_type !== 'tilapia' || $transaction->module_type !== 'expense') {
            abort(404);
        }

        // Get existing categories from database
        $existingCategories = Transaction::where('account_type', 'tilapia')
            ->where('module_type', 'expense')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->pluck('category')
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        // Default categories
        $defaultCategories = ['Feed', 'Fingerlings', 'Labor', 'Equipment', 'Transportation', 'Utilities', 'Maintenance', 'Medicine'];

        // Merge and deduplicate
        $allCategories = array_unique(array_merge($defaultCategories, $existingCategories));
        sort($allCategories);

        return view('monitoring.tilapia-expenses-edit', [
            'transaction' => $transaction,
            'categories' => $allCategories,
        ]);
    }

    /**
     * Update tilapia expenses
     */
    public function updateTilapiaExpenses(Request $request, Transaction $transaction): \Illuminate\Http\RedirectResponse
    {
        if ($transaction->account_type !== 'tilapia' || $transaction->module_type !== 'expense') {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'pcv_number' => ['required', 'string', 'max:50'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $transaction->update($validated);

        return redirect()->route('monitoring.tilapia')->with('success', 'Expense updated.');
    }

    /**
     * Delete tilapia expenses
     */
    public function destroyTilapiaExpenses(Request $request, Transaction $transaction): \Illuminate\Http\RedirectResponse
    {
        if ($transaction->account_type !== 'tilapia' || $transaction->module_type !== 'expense') {
            abort(404);
        }

        // Verify password
        if (!\Hash::check($request->password, $request->user()->password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        $transaction->delete();

        return redirect()->route('monitoring.tilapia')->with('success', 'Expense deleted.');
    }

    /**
     * Show conel deposit creation form
     */
    public function createConelDeposit(): View
    {
        return view('monitoring.conel-deposit-create', [
            'transaction' => new Transaction([
                'account_type' => 'conel',
                'module_type' => 'income',
                'transaction_date' => now()->format('Y-m-d'),
            ]),
        ]);
    }

    /**
     * Store conel deposit
     */
    public function storeConelDeposit(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'description' => ['required', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        Transaction::create([
            'account_type' => 'conel',
            'module_type' => 'income',
            ...$validated,
        ]);

        return redirect()->route('monitoring.conel')->with('success', 'Deposit recorded.');
    }

    /**
     * Show conel deposit edit form
     */
    public function editConelDeposit(Transaction $transaction): View
    {
        if ($transaction->account_type !== 'conel' || $transaction->module_type !== 'income') {
            abort(404);
        }

        return view('monitoring.conel-deposit-edit', [
            'transaction' => $transaction,
        ]);
    }

    /**
     * Update conel deposit
     */
    public function updateConelDeposit(Request $request, Transaction $transaction): \Illuminate\Http\RedirectResponse
    {
        if ($transaction->account_type !== 'conel' || $transaction->module_type !== 'income') {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'description' => ['required', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $transaction->update($validated);

        return redirect()->route('monitoring.conel')->with('success', 'Deposit updated.');
    }

    /**
     * Delete conel deposit
     */
    public function destroyConelDeposit(Request $request, Transaction $transaction): \Illuminate\Http\RedirectResponse
    {
        if ($transaction->account_type !== 'conel' || $transaction->module_type !== 'income') {
            abort(404);
        }

        // Verify password
        if (!\Hash::check($request->password, $request->user()->password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        $transaction->delete();

        return redirect()->route('monitoring.conel')->with('success', 'Deposit deleted.');
    }

    /**
     * Show conel withdraw creation form
     */
    public function createConelWithdraw(): View
    {
        return view('monitoring.conel-withdraw-create', [
            'transaction' => new Transaction([
                'account_type' => 'conel',
                'module_type' => 'expense',
                'transaction_date' => now()->format('Y-m-d'),
            ]),
        ]);
    }

    /**
     * Store conel withdraw
     */
    public function storeConelWithdraw(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'description' => ['required', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        Transaction::create([
            'account_type' => 'conel',
            'module_type' => 'expense',
            ...$validated,
        ]);

        return redirect()->route('monitoring.conel')->with('success', 'Withdrawal recorded.');
    }

    /**
     * Show conel withdraw edit form
     */
    public function editConelWithdraw(Transaction $transaction): View
    {
        if ($transaction->account_type !== 'conel' || $transaction->module_type !== 'expense') {
            abort(404);
        }

        return view('monitoring.conel-withdraw-edit', [
            'transaction' => $transaction,
        ]);
    }

    /**
     * Update conel withdraw
     */
    public function updateConelWithdraw(Request $request, Transaction $transaction): \Illuminate\Http\RedirectResponse
    {
        if ($transaction->account_type !== 'conel' || $transaction->module_type !== 'expense') {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'description' => ['required', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $transaction->update($validated);

        return redirect()->route('monitoring.conel')->with('success', 'Withdrawal updated.');
    }

    /**
     * Delete conel withdraw
     */
    public function destroyConelWithdraw(Request $request, Transaction $transaction): \Illuminate\Http\RedirectResponse
    {
        if ($transaction->account_type !== 'conel' || $transaction->module_type !== 'expense') {
            abort(404);
        }

        // Verify password
        if (!\Hash::check($request->password, $request->user()->password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        $transaction->delete();

        return redirect()->route('monitoring.conel')->with('success', 'Withdrawal deleted.');
    }

    /**
     * Show 128 deposit creation form
     */
    public function create128Deposit(): View
    {
        return view('monitoring.128-deposit-create', [
            'transaction' => new Transaction([
                'account_type' => '128',
                'module_type' => 'income',
                'transaction_date' => now()->format('Y-m-d'),
            ]),
        ]);
    }

    /**
     * Store 128 deposit
     */
    public function store128Deposit(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'description' => ['required', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        Transaction::create([
            'account_type' => '128',
            'module_type' => 'income',
            ...$validated,
        ]);

        return redirect()->route('monitoring.128')->with('success', 'Deposit recorded.');
    }

    /**
     * Show 128 deposit edit form
     */
    public function edit128Deposit(Transaction $transaction): View
    {
        if ($transaction->account_type !== '128' || $transaction->module_type !== 'income') {
            abort(404);
        }

        return view('monitoring.128-deposit-edit', [
            'transaction' => $transaction,
        ]);
    }

    /**
     * Update 128 deposit
     */
    public function update128Deposit(Request $request, Transaction $transaction): \Illuminate\Http\RedirectResponse
    {
        if ($transaction->account_type !== '128' || $transaction->module_type !== 'income') {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'description' => ['required', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $transaction->update($validated);

        return redirect()->route('monitoring.128')->with('success', 'Deposit updated.');
    }

    /**
     * Delete 128 deposit
     */
    public function destroy128Deposit(Request $request, Transaction $transaction): \Illuminate\Http\RedirectResponse
    {
        if ($transaction->account_type !== '128' || $transaction->module_type !== 'income') {
            abort(404);
        }

        // Verify password
        if (!\Hash::check($request->password, $request->user()->password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        $transaction->delete();

        return redirect()->route('monitoring.128')->with('success', 'Deposit deleted.');
    }

    /**
     * Show 128 withdraw creation form
     */
    public function create128Withdraw(): View
    {
        return view('monitoring.128-withdraw-create', [
            'transaction' => new Transaction([
                'account_type' => '128',
                'module_type' => 'expense',
                'transaction_date' => now()->format('Y-m-d'),
            ]),
        ]);
    }

    /**
     * Store 128 withdraw
     */
    public function store128Withdraw(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'description' => ['required', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        Transaction::create([
            'account_type' => '128',
            'module_type' => 'expense',
            ...$validated,
        ]);

        return redirect()->route('monitoring.128')->with('success', 'Withdrawal recorded.');
    }

    /**
     * Show 128 withdraw edit form
     */
    public function edit128Withdraw(Transaction $transaction): View
    {
        if ($transaction->account_type !== '128' || $transaction->module_type !== 'expense') {
            abort(404);
        }

        return view('monitoring.128-withdraw-edit', [
            'transaction' => $transaction,
        ]);
    }

    /**
     * Update 128 withdraw
     */
    public function update128Withdraw(Request $request, Transaction $transaction): \Illuminate\Http\RedirectResponse
    {
        if ($transaction->account_type !== '128' || $transaction->module_type !== 'expense') {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'description' => ['required', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $transaction->update($validated);

        return redirect()->route('monitoring.128')->with('success', 'Withdrawal updated.');
    }

    /**
     * Delete 128 withdraw
     */
    public function destroy128Withdraw(Request $request, Transaction $transaction): \Illuminate\Http\RedirectResponse
    {
        if ($transaction->account_type !== '128' || $transaction->module_type !== 'expense') {
            abort(404);
        }

        // Verify password
        if (!\Hash::check($request->password, $request->user()->password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        $transaction->delete();

        return redirect()->route('monitoring.128')->with('success', 'Withdrawal deleted.');
    }
}
