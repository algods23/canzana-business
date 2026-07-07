@csrf
<div class="grid gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="name">Tenant name</label>
        <input id="name" name="name" type="text" value="{{ old('name', $tenant->name) }}" class="input-field w-full" required>
        @error('name')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email', $tenant->email) }}" class="input-field w-full" required>
        @error('email')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="phone">Phone</label>
        <input id="phone" name="phone" type="text" value="{{ old('phone', $tenant->phone) }}" class="input-field w-full">
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="property_id">Property</label>
        <select id="property_id" name="property_id" class="input-field w-full" required>
            @foreach($properties as $property)
                <option value="{{ $property->id }}" @selected((string) old('property_id', $tenant->property_id) === (string) $property->id)>{{ $property->name }}</option>
            @endforeach
        </select>
        @error('property_id')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="room_id">Room</label>
        <select id="room_id" name="room_id" class="input-field w-full">
            <option value="">Unassigned</option>
            @foreach($rooms as $room)
                <option value="{{ $room->id }}" data-property-id="{{ $room->buildingModel?->property_id }}" data-rent="{{ $room->rent }}" @selected((string) old('room_id', $tenant->room_id) === (string) $room->id)>{{ $room->unit }} — {{ $room->buildingModel?->name }}{{ $room->buildingModel?->propertyModel ? ' / ' . $room->buildingModel->propertyModel->name : '' }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="company">Company</label>
        <input id="company" name="company" type="text" value="{{ old('company', $tenant->company) }}" class="input-field w-full">
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="rent">Monthly rent</label>
        <input id="rent" name="rent" type="number" step="0.01" value="{{ old('rent', $tenant->rent) }}" class="input-field w-full bg-slate-50" readonly required>
        @error('rent')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div class="hidden">
        <input id="balance" name="balance" type="hidden" value="{{ old('balance', $tenant->balance ?? 0) }}">
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="lease_start">Lease start</label>
        <input id="lease_start" name="lease_start" type="date" value="{{ old('lease_start', optional($tenant->lease_start)->format('Y-m-d') ?: date('Y-m-d')) }}" class="input-field w-full">
    </div>
    <div class="hidden">
        <input id="lease_end" name="lease_end" type="hidden" value="{{ old('lease_end', optional($tenant->lease_end)->format('Y-m-d')) }}">
    </div>
    <div class="md:col-span-2">
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="status">Status</label>
        <select id="status" name="status" class="input-field w-full" required>
            @foreach(['active', 'overdue', 'inactive'] as $status)
                <option value="{{ $status }}" @selected(old('status', $tenant->status ?: 'active') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        @error('status')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const propertySelect = document.getElementById('property_id');
        const roomSelect = document.getElementById('room_id');
        const rentInput = document.getElementById('rent');
        
        function updateRooms() {
            const propertyId = propertySelect.value;
            const options = roomSelect.querySelectorAll('option:not([value=""])');
            let hasValidOption = false;
            
            options.forEach(option => {
                if (option.dataset.propertyId === propertyId) {
                    option.style.display = '';
                    hasValidOption = true;
                } else {
                    option.style.display = 'none';
                }
            });
            
            const selectedOption = roomSelect.options[roomSelect.selectedIndex];
            if (selectedOption && selectedOption.value !== "" && selectedOption.dataset.propertyId !== propertyId) {
                roomSelect.value = "";
                rentInput.value = "";
            }
        }
        
        function updateRent() {
            const selectedOption = roomSelect.options[roomSelect.selectedIndex];
            if (selectedOption && selectedOption.value !== "") {
                rentInput.value = selectedOption.dataset.rent || 0;
            }
        }
        
        if (propertySelect && roomSelect && rentInput) {
            propertySelect.addEventListener('change', updateRooms);
            roomSelect.addEventListener('change', updateRent);
            
            updateRooms();
            if (rentInput.value === "") {
                updateRent();
            }
        }
    });
</script>
@endpush
