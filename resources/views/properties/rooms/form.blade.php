@csrf
<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="unit">Unit number</label>
        <input id="unit" name="unit" type="text" value="{{ old('unit', $room->unit ?? '') }}" class="input-field w-full" required placeholder="e.g., 101, A-101">
        @error('unit')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="floor">Floor</label>
        <select id="floor" name="floor" class="input-field w-full" required>
            @if(isset($building) && $building->floors)
                @for($i = 1; $i <= $building->floors; $i++)
                    <option value="{{ $i }}" @selected(old('floor', $room->floor ?? 1) == $i)>Floor {{ $i }}</option>
                @endfor
            @else
                <option value="1" selected>Floor 1</option>
            @endif
        </select>
        @error('floor')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="type">Room type</label>
        <select id="type" name="type" class="input-field w-full" required>
            @foreach(['Studio', '1-Bedroom', '2-Bedroom', '3-Bedroom'] as $type)
                <option value="{{ $type }}" @selected(old('type', $room->type ?? '') === $type)>{{ $type }}</option>
            @endforeach
        </select>
        @error('type')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="size_sqm">Size (sqm)</label>
        <input id="size_sqm" name="size_sqm" type="number" step="0.01" min="1" max="10000" value="{{ old('size_sqm', $room->size_sqm ?? '') }}" class="input-field w-full" required placeholder="e.g., 45.50">
        @error('size_sqm')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="rent">Monthly rent (₱)</label>
        <input id="rent" name="rent" type="number" step="0.01" min="0" max="999999.99" value="{{ old('rent', $room->rent ?? '') }}" class="input-field w-full" required placeholder="e.g., 25000.00">
        @error('rent')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <input type="hidden" id="status" name="status" value="{{ old('status', $room->status ?? 'vacant') }}">
    </div>
</div>
