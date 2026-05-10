<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                {{ __('Manajemen Kupon') }}
            </h2>
            <div class="flex flex-wrap gap-2">
                @if(Auth::user()->isAdmin())
                    <form action="{{ route('coupon.approve-all') }}" method="POST" onsubmit="return confirm('Setujui semua kupon pending?')">
                        @csrf
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-xl font-bold hover:bg-blue-700 transition shadow-sm text-sm">
                            Setujui Semua
                        </button>
                    </form>
                    <button onclick="printSelected()" class="bg-slate-800 text-white px-4 py-2 rounded-xl font-bold hover:bg-slate-900 transition shadow-sm text-sm">
                        Cetak Terpilih
                    </button>
                @endif
                <a href="{{ route('coupon.create') }}" class="bg-emerald-600 text-white px-4 py-2 rounded-xl font-bold hover:bg-emerald-700 transition shadow-sm text-sm">
                    + Ajukan Kupon
                </a>
            </div>
        </div>
    </x-slot>

    <div class="mb-6 flex gap-2 overflow-x-auto pb-2">
        <a href="{{ route('coupon.index') }}" class="px-4 py-2 rounded-full text-sm font-semibold {{ !request('status') ? 'bg-emerald-600 text-white' : 'bg-white text-slate-600 border border-slate-200' }}">Semua</a>
        <a href="{{ route('coupon.index', ['status' => 'pending']) }}" class="px-4 py-2 rounded-full text-sm font-semibold {{ request('status') == 'pending' ? 'bg-amber-500 text-white' : 'bg-white text-slate-600 border border-slate-200' }}">Pending</a>
        <a href="{{ route('coupon.index', ['status' => 'approved']) }}" class="px-4 py-2 rounded-full text-sm font-semibold {{ request('status') == 'approved' ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 border border-slate-200' }}">Approved</a>
        <a href="{{ route('coupon.index', ['status' => 'claimed']) }}" class="px-4 py-2 rounded-full text-sm font-semibold {{ request('status') == 'claimed' ? 'bg-emerald-600 text-white' : 'bg-white text-slate-600 border border-slate-200' }}">Claimed</a>
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
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-6 py-4 w-10">
                            <input type="checkbox" id="select-all" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        </th>
                        <th class="px-6 py-4 text-sm font-semibold text-slate-600">No. Kupon</th>
                        <th class="px-6 py-4 text-sm font-semibold text-slate-600">Oleh</th>
                        <th class="px-6 py-4 text-sm font-semibold text-slate-600">Paket</th>
                        <th class="px-6 py-4 text-sm font-semibold text-slate-600">Berat</th>
                        <th class="px-6 py-4 text-sm font-semibold text-slate-600">Status</th>
                        <th class="px-6 py-4 text-sm font-semibold text-slate-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($coupons as $coupon)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4">
                            @if($coupon->status == 'approved')
                                <input type="checkbox" name="selected_coupons[]" value="{{ $coupon->id }}" class="coupon-checkbox rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            @else
                                <div class="w-4 h-4 bg-slate-100 rounded border border-slate-200 opacity-50 cursor-not-allowed" title="Hanya kupon approved yang bisa dicetak"></div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('coupon.show', $coupon) }}" class="group block">
                                <div class="text-sm font-bold text-slate-900 group-hover:text-emerald-700 transition underline decoration-slate-200 underline-offset-4">KPN-{{ str_pad($coupon->id, 5, '0', STR_PAD_LEFT) }}</div>
                                <div class="text-[10px] font-mono text-slate-400 mt-1 group-hover:text-emerald-600 transition truncate max-w-[100px]">{{ $coupon->qr_code }}</div>
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-slate-900 font-medium">{{ $coupon->creator->name }}</div>
                            <div class="text-xs text-slate-500">{{ $coupon->created_at->format('d/m/y H:i') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-800">
                                {{ $coupon->quantity }} Paket
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                {{ number_format($coupon->quantity * $coupon->weight_kg, 2) }} Kg
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
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.coupon-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });

        function getSelectedIds() {
            const checkboxes = document.querySelectorAll('.coupon-checkbox:checked');
            return Array.from(checkboxes).map(cb => cb.value);
        }

        function printSelected() {
            const ids = getSelectedIds();
            if (ids.length === 0) { alert('Pilih kupon terlebih dahulu!'); return; }
            window.open("{{ route('coupon.print') }}?ids=" + ids.join(','), '_blank');
        }
    </script>
    @endpush
</x-app-layout>
