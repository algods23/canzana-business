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
                <option value="{{ $room->id }}" @selected((string) old('room_id', $tenant->room_id) === (string) $room->id)>{{ $room->unit }} — {{ $room->buildingModel?->name }}{{ $room->buildingModel?->propertyModel ? ' / ' . $room->buildingModel->propertyModel->name : '' }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="company">Company</label>
        <input id="company" name="company" type="text" value="{{ old('company', $tenant->company) }}" class="input-field w-full">
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="rent">Monthly rent</label>
        <input id="rent" name="rent" type="number" step="0.01" value="{{ old('rent', $tenant->rent) }}" class="input-field w-full" required>
        @error('rent')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="balance">Balance</label>
        <input id="balance" name="balance" type="number" step="0.01" value="{{ old('balance', $tenant->balance) }}" class="input-field w-full" required>
        @error('balance')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="lease_start">Lease start</label>
        <input id="lease_start" name="lease_start" type="date" value="{{ old('lease_start', optional($tenant->lease_start)->format('Y-m-d')) }}" class="input-field w-full">
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="lease_end">Lease end</label>
        <input id="lease_end" name="lease_end" type="date" value="{{ old('lease_end', optional($tenant->lease_end)->format('Y-m-d')) }}" class="input-field w-full">
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
