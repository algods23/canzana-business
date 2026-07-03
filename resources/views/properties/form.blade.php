@csrf
<div class="grid gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="name">Property name</label>
        <input id="name" name="name" type="text" value="{{ old('name', $property->name) }}" class="input-field w-full" required>
        @error('name')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="address">Address</label>
        <input id="address" name="address" type="text" value="{{ old('address', $property->address) }}" class="input-field w-full" required>
        @error('address')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="city">City</label>
        <input id="city" name="city" type="text" value="{{ old('city', $property->city) }}" class="input-field w-full" required>
        @error('city')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="type">Type</label>
        <select id="type" name="type" class="input-field w-full" required>
            @foreach(['Residential', 'Commercial', 'Mixed Use'] as $type)
                <option value="{{ $type }}" @selected(old('type', $property->type) === $type)>{{ $type }}</option>
            @endforeach
        </select>
        @error('type')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="status">Status</label>
        <select id="status" name="status" class="input-field w-full" required>
            @foreach(['active', 'maintenance'] as $status)
                <option value="{{ $status }}" @selected(old('status', $property->status ?: 'active') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        @error('status')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div class="md:col-span-2">
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="image">Image URL</label>
        <input id="image" name="image" type="text" value="{{ old('image', $property->image) }}" class="input-field w-full">
        @error('image')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
</div>
