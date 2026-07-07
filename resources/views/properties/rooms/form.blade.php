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
        @php
            $defaultTypes = ['Studio', '1-Bedroom', '2-Bedroom', '3-Bedroom'];
            $existingTypes = \App\Models\Room::select('type')->distinct()->pluck('type')->toArray();
            $allTypes = array_unique(array_merge($defaultTypes, $existingTypes));
            $currentType = old('type', $room->type ?? '');
        @endphp
        <select id="type" name="type" class="input-field w-full" required onchange="if(this.value==='Other'){document.getElementById('custom_type_wrapper').style.display='block';document.getElementById('custom_type').required=true;this.name=''}else{document.getElementById('custom_type_wrapper').style.display='none';document.getElementById('custom_type').required=false;this.name='type'}">
            @foreach($allTypes as $type)
                <option value="{{ $type }}" @selected($currentType === $type)>{{ $type }}</option>
            @endforeach
            <option value="Other">Other...</option>
        </select>
        <div id="custom_type_wrapper" class="mt-2" style="display: none;">
            <input id="custom_type" name="type" type="text" class="input-field w-full" placeholder="Enter custom room type" disabled>
        </div>
        @error('type')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const typeSelect = document.getElementById('type');
                const customType = document.getElementById('custom_type');
                
                typeSelect.addEventListener('change', function() {
                    if (this.value === 'Other') {
                        customType.disabled = false;
                        this.name = '';
                    } else {
                        customType.disabled = true;
                        this.name = 'type';
                    }
                });
            });
        </script>
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
