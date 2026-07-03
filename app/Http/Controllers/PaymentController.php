<?php

namespace App\Http\Controllers;

use App\Data\MockData;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = MockData::payments();

        if ($status = $request->get('status')) {
            $payments = array_filter($payments, fn ($p) => $p['status'] === $status);
        }

        if ($search = $request->get('search')) {
            $payments = array_filter($payments, fn ($p) =>
                str_contains(strtolower($p['tenant']), strtolower($search)) ||
                str_contains(strtolower($p['unit']), strtolower($search))
            );
        }

        $stats = [
            'total' => count(MockData::payments()),
            'paid' => count(array_filter(MockData::payments(), fn ($p) => $p['status'] === 'paid')),
            'pending' => count(array_filter(MockData::payments(), fn ($p) => $p['status'] === 'pending')),
            'overdue' => count(array_filter(MockData::payments(), fn ($p) => $p['status'] === 'overdue')),
            'collected' => array_sum(array_column(array_filter(MockData::payments(), fn ($p) => $p['status'] === 'paid'), 'amount')),
            'outstanding' => array_sum(array_column(array_filter(MockData::payments(), fn ($p) => in_array($p['status'], ['pending', 'overdue'])), 'amount')),
        ];

        return view('payments.index', [
            'payments' => array_values($payments),
            'stats' => $stats,
            'filters' => $request->only(['search', 'status']),
        ]);
    }
}
