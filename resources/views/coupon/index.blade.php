<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                {{ __('Manajemen Kupon') }}
            </h2>
            
            <!-- Action Bar & Add Button -->
            <div class="flex items-center gap-4">
                <!-- Action Bar -->
                <div id="action-bar" class="flex flex-wrap gap-2 items-center bg-white p-2 rounded-2xl shadow-sm border border-slate-200 w-full md:w-auto">
                    <span id="selected-count" class="text-xs font-bold text-slate-500 px-3 whitespace-nowrap">0 dipilih</span>

                    <form id="bulk-action-form" method="POST" class="flex flex-wrap gap-2 w-full md:w-auto">
                        @csrf
                        <button type="button" onclick="submitBulkAction('POST', '{{ route('coupon.approve-all') }}')" class="bg-blue-600 text-white px-4 py-2 rounded-xl font-bold hover:bg-blue-700 transition shadow-sm text-sm">
                            Approve
                        </button>
                        <button type="button" onclick="submitBulkAction('DELETE', '{{ route('coupon.destroy', ['coupon' => 0]) }}')" class="flex-1 md:flex-none bg-red-600 text-white px-4 py-2 rounded-xl font-bold hover:bg-red-700 transition shadow-sm text-sm">
                            Delete
                        </button>
                        <button type="button" id="print-btn" onclick="printSelected()" class="flex-1 md:flex-none bg-indigo-600 text-white px-4 py-2 rounded-xl font-bold hover:bg-indigo-700 transition shadow-sm text-sm">
                            Print
                        </button>
                    </form>
                </div>
                <a href="{{ route('coupon.create') }}" class="bg-emerald-600 text-white px-4 py-2 rounded-xl font-bold hover:bg-emerald-700 transition shadow-sm text-sm whitespace-nowrap">
                    + Ajukan Kupon
                </a>
            </div>
        </div>
    </x-slot>

    <div class="mb-6 flex flex-col md:flex-row gap-4 items-center justify-between">
        <div class="flex gap-2 overflow-x-auto pb-2">
            <a href="{{ route('coupon.index') }}" class="px-4 py-2 rounded-full text-sm font-semibold {{ !request('status') ? 'bg-emerald-600 text-white' : 'bg-white text-slate-600 border border-slate-200' }}">Semua</a>
            <a href="{{ route('coupon.index', ['status' => 'pending']) }}" class="px-4 py-2 rounded-full text-sm font-semibold {{ request('status') == 'pending' ? 'bg-amber-500 text-white' : 'bg-white text-slate-600 border border-slate-200' }}">Pending</a>
            <a href="{{ route('coupon.index', ['status' => 'approved']) }}" class="px-4 py-2 rounded-full text-sm font-semibold {{ request('status') == 'approved' ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 border border-slate-200' }}">Approved</a>
            <a href="{{ route('coupon.index', ['status' => 'claimed']) }}" class="px-4 py-2 rounded-full text-sm font-semibold {{ request('status') == 'claimed' ? 'bg-emerald-600 text-white' : 'bg-white text-slate-600 border border-slate-200' }}">Claimed</a>
        </div>

        <form action="{{ route('coupon.index') }}" method="GET" class="w-full md:w-64">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. Urut (ex: 12)..." class="w-full border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl bg-white shadow-sm text-sm">
        </form>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-100 font-medium">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-xl border border-red-100 font-medium">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white/60 backdrop-blur-sm overflow-hidden shadow-sm rounded-2xl border border-slate-200/60">
        <!-- Select All Alert -->
        <div id="select-all-alert" class="hidden p-4 bg-indigo-50 border-b border-indigo-100 text-indigo-700 text-sm">
            <span id="select-all-msg"></span>
            <button type="button" onclick="selectAllAcrossPages()" class="font-bold underline ml-2">Pilih semua {{ $coupons->total() }} kupon?</button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-6 py-4 w-10">
                            <input type="checkbox" id="select-all" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        </th>
                        <th class="px-6 py-4 text-sm font-semibold text-slate-600">No. Urut</th>
                        <th class="px-6 py-4 text-sm font-semibold text-slate-600">Oleh</th>
                        <th class="px-6 py-4 text-sm font-semibold text-slate-600">Berat</th>
                        <th class="px-6 py-4 text-sm font-semibold text-slate-600">Status</th>
                        <th class="px-6 py-4 text-sm font-semibold text-slate-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($coupons as $coupon)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4">
                            <input type="checkbox" name="selected_coupons[]" value="{{ $coupon->id }}" data-status="{{ $coupon->status }}" class="coupon-checkbox rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('coupon.show', $coupon) }}" class="group block">
                                <div class="text-sm font-bold text-slate-900 group-hover:text-emerald-700 transition underline decoration-slate-200 underline-offset-4">KPN-{{ str_pad($coupon->serial_number, 5, '0', STR_PAD_LEFT) }}</div>
                                <div class="text-[10px] font-mono text-slate-400 mt-1 group-hover:text-emerald-600 transition truncate max-w-[100px]">{{ $coupon->qr_code }}</div>
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-slate-900 font-medium">{{ $coupon->creator->name }}</div>
                            <div class="text-xs text-slate-500">{{ $coupon->created_at->format('d/m/y H:i') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                {{ $coupon->weight_kg + 0 }} Kg
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($coupon->status == 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Pending</span>
                            @elseif($coupon->status == 'approved')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">Approved</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Claimed</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex gap-3">
                                @if($coupon->status == 'pending' && Auth::user()->isAdmin())
                                    <form action="{{ route('coupon.approve', $coupon) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-blue-600 font-bold hover:text-blue-800">Approve</button>
                                    </form>
                                @endif
                                <form action="{{ route('coupon.destroy', $coupon) }}" method="POST" onsubmit="return confirm('Hapus kupon ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 font-bold hover:text-red-800">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-slate-500 italic">
                            Belum ada kupon yang diajukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-6">
        {{ $coupons->links() }}
    </div>
    @push('scripts')
    <script>
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.coupon-checkbox');
        const actionBar = document.getElementById('action-bar');
        const selectedCount = document.getElementById('selected-count');
        const selectAllAlert = document.getElementById('select-all-alert');
        const selectAllMsg = document.getElementById('select-all-msg');
        
        let allSelected = false;

        function updateActionBar() {
            const checked = document.querySelectorAll('.coupon-checkbox:checked');
            let count = checked.length;
            
            if (allSelected) {
                count = {{ $coupons->total() }};
                selectAllAlert.classList.add('hidden');
            } else if (checked.length === checkboxes.length && checkboxes.length < {{ $coupons->total() }}) {
                selectAllAlert.classList.remove('hidden');
                selectAllMsg.innerText = `Semua ${checkboxes.length} kupon di halaman ini terpilih. `;
            } else {
                selectAllAlert.classList.add('hidden');
            }
            
            selectedCount.innerText = `${count} dipilih`;
        }

        function selectAllAcrossPages() {
            allSelected = true;
            selectAll.checked = true;
            updateActionBar();
        }

        selectAll.addEventListener('change', function() {
            if (!this.checked) allSelected = false;
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateActionBar();
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', () => {
                allSelected = false;
                updateActionBar();
            });
        });

        function submitBulkAction(method, route) {
            const form = document.getElementById('bulk-action-form');
            form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
            form.innerHTML += `<input type="hidden" name="_method" value="${method}">`;
            
            if (allSelected) {
                form.innerHTML += '<input type="hidden" name="all_selected" value="1">';
            } else {
                const ids = Array.from(document.querySelectorAll('.coupon-checkbox:checked')).map(cb => cb.value);
                if (ids.length === 0) return;
                ids.forEach(id => {
                    form.innerHTML += `<input type="hidden" name="ids[]" value="${id}">`;
                });
            }
            form.action = route;
            form.submit();
        }

        function printSelected() {
            const form = document.getElementById('bulk-action-form');
            const ids = Array.from(document.querySelectorAll('.coupon-checkbox:checked')).map(cb => cb.value);
            
            if (!allSelected && ids.length === 0) { alert('Pilih kupon terlebih dahulu!'); return; }

            // Clone form to submit
            const newForm = document.createElement('form');
            newForm.method = 'GET';
            newForm.action = '{{ route('coupon.print') }}';
            newForm.target = '_blank';

            if (allSelected) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'all_selected';
                input.value = '1';
                newForm.appendChild(input);
            } else {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids';
                input.value = ids.join(',');
                newForm.appendChild(input);
            }

            document.body.appendChild(newForm);
            newForm.submit();
            document.body.removeChild(newForm);
        }
    </script>
    @endpush
</x-app-layout>
