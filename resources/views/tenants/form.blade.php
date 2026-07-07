@csrf
<input type="hidden" name="existing_tenant_id" id="existing_tenant_id" value="">

{{-- Existing Tenant Selector --}}
@if(isset($existingTenants) && $existingTenants->count())
<div class="mb-5 rounded-xl border-2 border-dashed border-brand-200 bg-brand-50 p-4">
    <label class="mb-1.5 block text-sm font-semibold text-brand-700" for="existing_tenant_selector">Load from Existing Tenant <span class="font-normal text-brand-500">(optional — auto-fills details below)</span></label>
    <select id="existing_tenant_selector" class="input-field w-full">
        <option value="">— Create a new tenant —</option>
        @foreach($existingTenants as $et)
            <option value="{{ $et->id }}"
                data-name="{{ $et->name }}"
                data-email="{{ $et->email }}"
                data-phone="{{ $et->phone }}"
                data-company="{{ $et->company }}"
                data-lease-start="{{ optional($et->lease_start)->format('Y-m-d') }}"
                data-lease-end="{{ optional($et->lease_end)->format('Y-m-d') }}"
                data-rent="{{ $et->rent }}"
                data-status="{{ $et->status }}"
                data-property-id="{{ $et->property_id }}"
                data-room-id="{{ $et->room_id }}">
                {{ $et->name }}{{ $et->email ? ' — ' . $et->email : '' }}
            </option>
        @endforeach
    </select>
    <p class="mt-1.5 text-xs text-brand-600">Selecting an existing tenant will pre-fill the form. The <strong>selected room will be assigned to that tenant</strong> (their other rooms are kept — one tenant can rent multiple rooms).</p>
</div>
@endif

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
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700" for="lease_end">Lease end <span class="text-xs font-normal text-slate-400">(optional)</span></label>
        <input id="lease_end" name="lease_end" type="date" value="{{ old('lease_end') }}" class="input-field w-full" placeholder="No end date">
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
        const propertySelect  = document.getElementById('property_id');
        const roomSelect      = document.getElementById('room_id');
        const rentInput       = document.getElementById('rent');
        const existingSelect  = document.getElementById('existing_tenant_selector');
        const hiddenTenantId  = document.getElementById('existing_tenant_id');

        // Detect if we were opened from a specific room (URL has room_id param)
        const urlParams  = new URLSearchParams(window.location.search);
        const lockedRoom = urlParams.get('room_id');     // room pre-selected from unit page
        const lockedProp = urlParams.get('property_id'); // property pre-selected from unit page

        // ── Existing tenant auto-fill ──────────────────────────────────
        if (existingSelect) {
            existingSelect.addEventListener('change', function () {
                const opt = this.options[this.selectedIndex];
                if (!opt || opt.value === '') {
                    hiddenTenantId.value = '';
                    return;
                }

                hiddenTenantId.value = opt.value;

                // Always fill personal details
                document.getElementById('name').value    = opt.dataset.name    || '';
                document.getElementById('email').value   = opt.dataset.email   || '';
                document.getElementById('phone').value   = opt.dataset.phone   || '';
                document.getElementById('company').value = opt.dataset.company  || '';

                // Fill dates — only when NOT a room-specific assignment
                // (a new room means a new contract, so lease dates should be entered fresh)
                if (!lockedRoom) {
                    document.getElementById('lease_start').value = opt.dataset.leaseStart || '';
                    document.getElementById('lease_end').value   = opt.dataset.leaseEnd   || '';
                } else {
                    // Reset to blank for a fresh room contract
                    document.getElementById('lease_start').value = '';
                    document.getElementById('lease_end').value   = '';
                }

                // Fill status
                const statusSel = document.getElementById('status');
                if (opt.dataset.status) {
                    for (let i = 0; i < statusSel.options.length; i++) {
                        if (statusSel.options[i].value === opt.dataset.status) {
                            statusSel.selectedIndex = i;
                            break;
                        }
                    }
                }

                // ── Property & Room: only change if NOT locked from URL ──
                if (!lockedRoom) {
                    // Free-standing form: allow filling property/room from tenant data
                    if (opt.dataset.propertyId && propertySelect) {
                        propertySelect.value = opt.dataset.propertyId;
                        updateRooms();
                    }
                    if (opt.dataset.roomId && roomSelect) {
                        roomSelect.value = opt.dataset.roomId;
                        updateRent();
                    }
                    if (!opt.dataset.roomId && opt.dataset.rent) {
                        rentInput.value = opt.dataset.rent;
                    }
                } else {
                    // Opened from a room page — keep the locked property & room,
                    // just refresh the rent from that room's option
                    updateRent();
                }
            });
        }

        // ── Room / property filtering ──────────────────────────────────
        function updateRooms() {
            const propertyId = propertySelect.value;
            const options    = roomSelect.querySelectorAll('option:not([value=""])');
            options.forEach(option => {
                option.style.display = option.dataset.propertyId === propertyId ? '' : 'none';
            });

            const sel = roomSelect.options[roomSelect.selectedIndex];
            if (sel && sel.value !== '' && sel.dataset.propertyId !== propertyId) {
                roomSelect.value = '';
                rentInput.value  = '';
            }
        }

        function updateRent() {
            const sel = roomSelect.options[roomSelect.selectedIndex];
            if (sel && sel.value !== '') {
                rentInput.value = sel.dataset.rent || 0;
            }
        }

        if (propertySelect && roomSelect && rentInput) {
            propertySelect.addEventListener('change', updateRooms);
            roomSelect.addEventListener('change', updateRent);
            updateRooms();
            if (rentInput.value === '') updateRent();
        }
    });
</script>
@endpush
