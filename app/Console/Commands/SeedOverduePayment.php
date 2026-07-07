<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Console\Command;

class SeedOverduePayment extends Command
{
    protected $signature = 'seed:overdue-payment';
    protected $description = 'Seed an overdue payment record for testing';

    public function handle(): void
    {
        // Find the first tenant that has rooms
        $tenant = Tenant::with('rooms.buildingModel')->whereHas('rooms')->first();

        if (! $tenant) {
            $this->error('No tenant with assigned rooms found.');
            return;
        }

        $room = $tenant->rooms->first();

        // Insert an overdue payment: due 2 months ago, not yet paid
        $payment = Payment::create([
            'tenant_id'   => $tenant->id,
            'room_id'     => $room->id,
            'property_id' => $room->buildingModel->property_id,
            'amount'      => $room->rent,
            'due_date'    => now()->subMonths(2)->toDateString(),
            'paid_date'   => null,
            'status'      => 'overdue',
            'method'      => null,
            'reference'   => null,
        ]);

        // Bump tenant balance to reflect the outstanding amount
        $tenant->increment('balance', $room->rent);

        $this->info("✅ Overdue payment seeded!");
        $this->table(
            ['Tenant', 'Room', 'Amount', 'Due Date', 'Status'],
            [[
                $tenant->name,
                'Unit ' . $room->unit,
                '₱' . number_format($payment->amount),
                $payment->due_date,
                $payment->status,
            ]]
        );
    }
}
