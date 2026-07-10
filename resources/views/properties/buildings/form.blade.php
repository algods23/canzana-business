@csrf
<div class="grid gap-4 md:grid-cols-2">
    <div class="md:col-span-1">
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="type">Type</label>
        <select id="type" name="type" class="input-field w-full" required>
            <option value="building" @selected(old('type', $building->type ?? 'building') === 'building')>Building</option>
            <option value="house" @selected(old('type', $building->type ?? 'building') === 'house')>House</option>
        </select>
        @error('type')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div class="md:col-span-1">
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="name">Name</label>
        <input id="name" name="name" type="text" value="{{ old('name', $building->name) }}" class="input-field w-full" required>
        @error('name')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="floors">Floors</label>
        <input id="floors" name="floors" type="number" min="1" max="200" value="{{ old('floors', $building->floors ?: 1) }}" class="input-field w-full" required>
        @error('floors')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="status">Status</label>
        <select id="status" name="status" class="input-field w-full" required>
            @foreach(['active', 'maintenance'] as $status)
                <option value="{{ $status }}" @selected(old('status', $building->status ?: 'active') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        @error('status')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
</div>

@if(!$building->exists)
<div class="mt-6 border-t border-slate-100 pt-6">
    <h4 class="mb-4 font-medium text-slate-900">Rental Configuration</h4>
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700" for="rental_mode">How will this be rented?</label>
            <select id="rental_mode" name="rental_mode" class="input-field w-full" onchange="toggleRentInput()">
                <option value="rooms">By individual rooms/units (Add prices later)</option>
                <option value="whole" @selected(old('rental_mode') === 'whole')>As a whole property (Single price)</option>
            </select>
        </div>
        <div id="whole_rent_container" style="display: none;">
            <label class="mb-1.5 block text-sm font-medium text-slate-700" for="rent">Monthly Rent (₱)</label>
            <input id="rent" name="rent" type="number" step="0.01" min="0" value="{{ old('rent') }}" class="input-field w-full" placeholder="e.g. 15000">
            @error('rent')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
        </div>
    </div>
</div>

<script>
    function toggleRentInput() {
        const mode = document.getElementById('rental_mode');
        const container = document.getElementById('whole_rent_container');
        if (mode && container) {
            container.style.display = mode.value === 'whole' ? 'block' : 'none';
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('type');
        const modeSelect = document.getElementById('rental_mode');
        
        if (typeSelect && modeSelect) {
            typeSelect.addEventListener('change', function() {
                if (this.value === 'house') {
                    modeSelect.value = 'whole';
                }
                toggleRentInput();
            });
        }
        
        toggleRentInput();
    });
</script>
@endif
