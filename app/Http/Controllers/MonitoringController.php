<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\Expense;
use App\Models\Payment;
use App\Support\Analytics;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonitoringController extends Controller
{
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
                ];
            });

        $recentTransactions = collect($rentalTransactions->all())
            ->merge($paymentTransactions->all())
            ->sortByDesc(fn ($transaction) => $transaction->transaction_date)
            ->take(10)
            ->values();

        $query = Expense::with(['buildingModel', 'roomModel']);
        if ($request->filled('date_from')) {
            $query->where('expense_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('expense_date', '<=', $request->date_to);
        }
        $expenses = $query->latest('expense_date')->get();

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
        $salesTransactions = $query->latest('transaction_date')->take(10)->get();

        $query = Transaction::byAccount('agriculture')->where('module_type', 'expense');
        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }
        $expenseTransactions = $query->latest('transaction_date')->take(10)->get();

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
        $salesTransactions = $query->latest('transaction_date')->take(10)->get();

        $query = Transaction::byAccount('tilapia')->where('module_type', 'expense');
        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }
        $expenseTransactions = $query->latest('transaction_date')->take(10)->get();

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
        ]);
    }

    /**
     * Conel Monitoring Dashboard (Account Tracking)
     */
    public function conel(): View
    {
        $totalIncome = Transaction::getTotalIncome('conel');
        $totalExpenses = Transaction::getTotalExpenses('conel');
        $balance = Transaction::getBalance('conel');

        $recentTransactions = Transaction::byAccount('conel')
            ->latest('transaction_date')
            ->take(10)
            ->get();

        return view('monitoring.conel', [
            'stats' => [
                'total_income' => $totalIncome,
                'total_expenses' => $totalExpenses,
                'balance' => $balance,
            ],
            'recentTransactions' => $recentTransactions,
        ]);
    }

    /**
     * 128 Monitoring Dashboard (Account Tracking)
     */
    public function oneTwoEight(): View
    {
        $totalIncome = Transaction::getTotalIncome('128');
        $totalExpenses = Transaction::getTotalExpenses('128');
        $balance = Transaction::getBalance('128');

        $recentTransactions = Transaction::byAccount('128')
            ->latest('transaction_date')
            ->take(10)
            ->get();

        return view('monitoring.128', [
            'stats' => [
                'total_income' => $totalIncome,
                'total_expenses' => $totalExpenses,
                'balance' => $balance,
            ],
            'recentTransactions' => $recentTransactions,
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
            'pcv_number' => ['required', 'string', 'max:50'],
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
            'pcv_number' => ['required', 'string', 'max:50'],
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
}
