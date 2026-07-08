<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BusinessController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureStarterBusinesses($request);

        return view('businesses.index', [
            'businesses' => $request->user()->businesses()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('businesses.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $suffix = 2;

        while ($request->user()->businesses()->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        $request->user()->businesses()->create([
            'name' => $validated['name'],
            'slug' => $slug,
            'type' => Str::slug($validated['type']) ?: 'custom',
            'description' => $validated['description'] ?? null,
            'status' => 'active',
        ]);

        return redirect()->route('businesses.select')->with('success', 'Business added.');
    }

    public function open(Request $request, Business $business): RedirectResponse
    {
        abort_unless((int) $business->user_id === (int) $request->user()->id, 404);

        $request->session()->put('selected_business_id', $business->id);

        return redirect()->route('dashboard');
    }

    private function ensureStarterBusinesses(Request $request): void
    {
        $starterBusinesses = [
            ['name' => 'Rental', 'slug' => 'rental', 'type' => 'rental', 'description' => 'Properties, tenants, leases, and payments'],
            ['name' => 'Fishpond', 'slug' => 'fishpond', 'type' => 'fishpond', 'description' => 'Daily sales and disbursement expenses'],
            ['name' => 'Fruits', 'slug' => 'fruits', 'type' => 'fruits', 'description' => 'Daily sales and disbursement expenses'],
        ];

        foreach ($starterBusinesses as $business) {
            $request->user()->businesses()->updateOrCreate(
                ['slug' => $business['slug']],
                $business + ['status' => 'active']
            );
        }
    }
}
