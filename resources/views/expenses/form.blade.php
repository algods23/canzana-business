@csrf
<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="building_id">Building</label>
        <select id="building_id" name="building_id" class="input-field w-full" required onchange="updateRooms(this.value)">
            <option value="">Select Building</option>
            @foreach($buildings as $b)
                <option value="{{ $b->id }}" @selected(old('building_id', $expense->building_id) == $b->id)>
                    {{ $b->propertyModel?->name }} — {{ $b->name }}
                </option>
            @endforeach
        </select>
        @error('building_id')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="room_id">Room (optional)</label>
        <select id="room_id" name="room_id" class="input-field w-full">
            <option value="">Whole Building (General)</option>
            @foreach($buildings as $b)
                @foreach($b->rooms as $r)
                    <option value="{{ $r->id }}" data-building="{{ $b->id }}" @selected(old('room_id', $expense->room_id) == $r->id)
                        class="room-option" style="{{ old('building_id', $expense->building_id) == $b->id ? '' : 'display:none' }}">
                        Unit {{ $r->unit }} — Floor {{ $r->floor }}
                    </option>
                @endforeach
            @endforeach
        </select>
        <p class="mt-1 text-xs text-slate-500">Leave empty for building-level expenses</p>
        @error('room_id')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="category">Category</label>
        @php
            $defaultCategories = ['Maintenance', 'Utilities', 'Repairs', 'Supplies', 'Cleaning', 'Insurance', 'Tax', 'Other'];
        @endphp
        <select id="category" name="category" class="input-field w-full" required onchange="if(this.value==='__custom'){document.getElementById('custom_category_wrapper').style.display='block';document.getElementById('custom_category').required=true;this.name=''}else{document.getElementById('custom_category_wrapper').style.display='none';document.getElementById('custom_category').required=false;this.name='category'}">
            @foreach($defaultCategories as $cat)
                <option value="{{ $cat }}" @selected(old('category', $expense->category) === $cat)>{{ $cat }}</option>
            @endforeach
            <option value="__custom">Other...</option>
        </select>
        <div id="custom_category_wrapper" class="mt-2" style="display: none;">
            <input id="custom_category" name="category" type="text" class="input-field w-full" placeholder="Enter custom category" disabled>
        </div>
        @error('category')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="expense_date">Date</label>
        <input id="expense_date" name="expense_date" type="date" value="{{ old('expense_date', $expense->expense_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" class="input-field w-full" required>
        @error('expense_date')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div class="md:col-span-2">
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="description">Description</label>
        <input id="description" name="description" type="text" value="{{ old('description', $expense->description ?? '') }}" class="input-field w-full" required placeholder="e.g., Plumbing repair in bathroom">
        @error('description')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="amount">Amount (₱)</label>
        <input id="amount" name="amount" type="number" step="0.01" min="0.01" max="9999999.99" value="{{ old('amount', $expense->amount ?? '') }}" class="input-field w-full" required placeholder="e.g., 5000.00">
        @error('amount')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="notes">Notes (optional)</label>
        <input id="notes" name="notes" type="text" value="{{ old('notes', $expense->notes ?? '') }}" class="input-field w-full" placeholder="Additional notes...">
        @error('notes')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
</div>

<script>
    function updateRooms(buildingId) {
        const roomSelect = document.getElementById('room_id');
        const options = roomSelect.querySelectorAll('.room-option');
        roomSelect.value = '';
        options.forEach(opt => {
            opt.style.display = opt.dataset.building === buildingId ? '' : 'none';
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const categorySelect = document.getElementById('category');
        const customCategory = document.getElementById('custom_category');

        categorySelect.addEventListener('change', function() {
            if (this.value === '__custom') {
                customCategory.disabled = false;
            } else {
                customCategory.disabled = true;
            }
        });

        // Initialize rooms on load
        const buildingSelect = document.getElementById('building_id');
        if (buildingSelect.value) {
            updateRooms(buildingSelect.value);
        }
    });
</script>
