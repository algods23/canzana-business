<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\Expense;
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
        $query = \App\Models\Payment::where('status', 'paid');
        if ($request->filled('date_from')) {
            $query->where('paid_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('paid_date', '<=', $request->date_to);
        }
        $totalSales = (float) $query->sum('amount');

        // Calculate sales by payment method
        $salesByMethod = [];
        $methods = ['cash', 'gcash', 'bank', 'bpi', 'bdo', 'metrobank'];
        
        foreach ($methods as $method) {
            $query = \App\Models\Payment::where('status', 'paid')->where('method', $method);
            if ($request->filled('date_from')) {
                $query->where('paid_date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->where('paid_date', '<=', $request->date_to);
            }
            $amount = (float) $query->sum('amount');
            if ($amount > 0) {
                $salesByMethod[$method] = $amount;
            }
        }

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

        $query = Transaction::byAccount('rental');
        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }
        $recentTransactions = $query->latest('transaction_date')->take(10)->get();

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

        $query = Transaction::byAccount('agriculture');
        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }
        $recentTransactions = $query->latest('transaction_date')->take(10)->get();

        return view('monitoring.agriculture', [
            'stats' => [
                'total_sales' => $totalSales,
                'total_expenses' => $totalExpenses,
                'net_income' => $netIncome,
                'balance' => $balance,
            ],
            'recentTransactions' => $recentTransactions,
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

        $query = Transaction::byAccount('tilapia');
        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }
        $recentTransactions = $query->latest('transaction_date')->take(10)->get();

        return view('monitoring.tilapia', [
            'stats' => [
                'total_sales' => $totalSales,
                'total_expenses' => $totalExpenses,
                'net_income' => $netIncome,
                'balance' => $balance,
            ],
            'recentTransactions' => $recentTransactions,
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
}
