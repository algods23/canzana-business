@csrf
<div class="grid gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="tenant_id">Tenant</label>
        <select id="tenant_id" name="tenant_id" class="input-field w-full" required>
            @foreach($tenants as $tenantOption)
                <option value="{{ $tenantOption->id }}" @selected((string) old('tenant_id', $payment->tenant_id) === (string) $tenantOption->id)>
                    {{ $tenantOption->name }} — {{ $tenantOption->property }}{{ $tenantOption->unit ? ' / ' . $tenantOption->unit : '' }}
                </option>
            @endforeach
        </select>
        @error('tenant_id')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="amount">Amount</label>
        <input id="amount" name="amount" type="number" step="0.01" value="{{ old('amount', $payment->amount) }}" class="input-field w-full" required>
        @error('amount')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="due_date">Due date</label>
        <input id="due_date" name="due_date" type="date" value="{{ old('due_date', optional($payment->due_date)->format('Y-m-d')) }}" class="input-field w-full" required>
        @error('due_date')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="paid_date">Paid date</label>
        <input id="paid_date" name="paid_date" type="date" value="{{ old('paid_date', optional($payment->paid_date)->format('Y-m-d')) }}" class="input-field w-full">
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="status">Status</label>
        <select id="status" name="status" class="input-field w-full" required>
            @foreach(['pending', 'paid', 'overdue'] as $status)
                <option value="{{ $status }}" @selected(old('status', $payment->status ?: 'pending') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        @error('status')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="method">Method</label>
        <input id="method" name="method" type="text" value="{{ old('method', $payment->method) }}" class="input-field w-full">
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="reference">Reference</label>
        <input id="reference" name="reference" type="text" value="{{ old('reference', $payment->reference) }}" class="input-field w-full">
    </div>
</div>
