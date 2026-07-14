<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Expense;
use App\Models\Property;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    /**
     * List all expenses with filters.
     */
    public function index(Request $request): View
    {
        $query = Expense::query()
            ->with(['buildingModel.propertyModel', 'roomModel'])
            ->latest('expense_date');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search): void {
                $q->where('description', 'like', '%'.$search.'%')
                  ->orWhere('category', 'like', '%'.$search.'%')
                  ->orWhere('notes', 'like', '%'.$search.'%');
            });
        }

        if ($category = $request->get('category')) {
            $query->where('category', $category);
        }

        if ($buildingId = $request->get('building_id')) {
            $query->where('building_id', $buildingId);
        }

        $expenses = $query->get();

        // Stats
        $totalExpenses = Expense::sum('amount');
        $thisMonthExpenses = Expense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');
        $buildingExpenses = Expense::whereNull('room_id')->sum('amount');
        $roomExpenses = Expense::whereNotNull('room_id')->sum('amount');

        $categories = Expense::select('category')
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values();

        $buildings = Building::with('propertyModel')
            ->orderBy('name')
            ->get();

        return view('expenses.index', [
            'expenses' => $expenses,
            'filters' => $request->only(['search', 'category', 'building_id']),
            'stats' => [
                'total' => (float) $totalExpenses,
                'this_month' => (float) $thisMonthExpenses,
                'building_level' => (float) $buildingExpenses,
                'room_level' => (float) $roomExpenses,
            ],
            'categories' => $categories,
            'buildings' => $buildings,
        ]);
    }

    /**
     * Show the create expense form.
     */
    public function create(Request $request): View
    {
        $buildings = Building::with(['propertyModel', 'rooms'])
            ->orderBy('name')
            ->get();

        return view('expenses.create', [
            'expense' => new Expense([
                'expense_date' => now()->toDateString(),
                'building_id' => $request->get('building_id'),
                'room_id' => $request->get('room_id'),
            ]),
            'buildings' => $buildings,
        ]);
    }

    /**
     * Store a new expense.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'building_id' => ['required', 'exists:buildings,id'],
            'room_id' => ['nullable', 'exists:rooms,id'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:255'],
            'recipient_name' => ['nullable', 'string', 'max:255'],
            'pcv_number' => ['required', 'string', 'max:50'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'expense_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        Expense::create($validated);

        $redirect = $request->get('redirect_to');
        if ($redirect) {
            return redirect($redirect)->with('success', 'Expense recorded.');
        }

        return redirect()->route('monitoring.rental')->with('success', 'Expense recorded.');
    }

    /**
     * Show the edit expense form.
     */
    public function edit(Expense $expense): View
    {
        $buildings = Building::with(['propertyModel', 'rooms'])
            ->orderBy('name')
            ->get();

        return view('expenses.edit', [
            'expense' => $expense,
            'buildings' => $buildings,
        ]);
    }

    /**
     * Update an existing expense.
     */
    public function update(Request $request, Expense $expense): RedirectResponse
    {
        $validated = $request->validate([
            'building_id' => ['required', 'exists:buildings,id'],
            'room_id' => ['nullable', 'exists:rooms,id'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:255'],
            'recipient_name' => ['nullable', 'string', 'max:255'],
            'pcv_number' => ['required', 'string', 'max:50'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'expense_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $expense->update($validated);

        return redirect()->route('expenses.index')->with('success', 'Expense updated.');
    }

    /**
     * Delete an expense.
     */
    public function destroy(Expense $expense): RedirectResponse
    {
        $expense->delete();

        return redirect()->route('monitoring.rental')->with('success', 'Expense deleted.');
    }
}
