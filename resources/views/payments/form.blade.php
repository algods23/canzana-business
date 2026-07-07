@csrf
<input type="hidden" name="room_id" value="{{ request('room_id', $payment->room_id) }}">
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
        <input id="due_date" name="due_date" type="date" value="{{ old('due_date', optional($payment->due_date)->format('Y-m-d') ?: date('Y-m-d')) }}" class="input-field w-full" required readonly style="pointer-events: none; background-color: #f8fafc;">
        @error('due_date')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="paid_date">Paid date</label>
        <input id="paid_date" name="paid_date" type="date" value="{{ old('paid_date', optional($payment->paid_date)->format('Y-m-d') ?: date('Y-m-d')) }}" class="input-field w-full" placeholder="Leave blank if not yet paid">
    </div>
    <div style="display: none;">
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="status">Status</label>
        <select id="status" name="status" class="input-field w-full" required readonly style="pointer-events: none; background-color: #f8fafc;">
            @foreach(['pending', 'paid', 'overdue'] as $status)
                <option value="{{ $status }}" @selected(old('status', $payment->status ?: 'paid') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        @error('status')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="method">Method</label>
        <select id="method" name="method" class="input-field w-full">
            <option value="cash" @selected(old('method', $payment->method ?? 'cash') === 'cash')>Cash</option>
            <option value="gcash" @selected(old('method', $payment->method) === 'gcash')>GCash</option>
            <option value="bank transfer" @selected(old('method', $payment->method) === 'bank transfer')>Bank Transfer</option>
        </select>
    </div>
    <div id="reference_wrapper">
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="reference">Reference</label>
        <input id="reference" name="reference" type="text" value="{{ old('reference', $payment->reference) }}" class="input-field w-full">
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const methodSelect = document.getElementById('method');
        const referenceWrapper = document.getElementById('reference_wrapper');
        const referenceInput = document.getElementById('reference');
        
        const dueDateInput = document.getElementById('due_date');
        const paidDateInput = document.getElementById('paid_date');
        const statusSelect = document.getElementById('status');

        function updateMethodVisibility() {
            if (methodSelect.value === 'cash') {
                referenceWrapper.style.display = 'none';
                referenceInput.value = ''; // clear out reference if switching to cash
            } else {
                referenceWrapper.style.display = 'block';
            }
        }

        function updateStatus() {
            const paidDate = paidDateInput.value;
            const dueDate = dueDateInput.value;
            
            if (paidDate) {
                statusSelect.value = 'paid';
            } else {
                if (dueDate) {
                    const today = new Date().toISOString().split('T')[0];
                    if (dueDate < today) {
                        statusSelect.value = 'overdue';
                    } else {
                        statusSelect.value = 'pending';
                    }
                } else {
                    statusSelect.value = 'pending';
                }
            }
        }

        methodSelect.addEventListener('change', updateMethodVisibility);
        dueDateInput.addEventListener('change', updateStatus);
        paidDateInput.addEventListener('change', updateStatus);

        // Run on load
        updateMethodVisibility();
        updateStatus();
    });
</script>
