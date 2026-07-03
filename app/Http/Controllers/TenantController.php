<?php

namespace App\Http\Controllers;

use App\Data\MockData;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $tenants = MockData::tenants();

        if ($search = $request->get('search')) {
            $tenants = array_filter($tenants, fn ($t) =>
                str_contains(strtolower($t['name']), strtolower($search)) ||
                str_contains(strtolower($t['unit']), strtolower($search))
            );
        }

        if ($status = $request->get('status')) {
            $tenants = array_filter($tenants, fn ($t) => $t['status'] === $status);
        }

        return view('tenants.index', [
            'tenants' => array_values($tenants),
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function show(int $id)
    {
        $tenant = MockData::findTenant($id);

        abort_unless($tenant, 404);

        return view('tenants.show', [
            'tenant' => $tenant,
            'payments' => array_filter(MockData::payments(), fn ($p) => str_contains($p['tenant'], explode(' ', $tenant['name'])[0])),
            'activities' => array_slice(MockData::activities(), 0, 4),
        ]);
    }
}
