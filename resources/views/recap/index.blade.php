<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                {{ __('Rekapitulasi Kupon') }}
            </h2>
            <button onclick="window.print()" class="bg-slate-800 text-white px-4 py-2 rounded-xl font-bold hover:bg-slate-900 transition shadow-sm text-sm no-print">
                Cetak Laporan
            </button>
        </div>
    </x-slot>

    <div class="no-print mb-8 bg-white/60 backdrop-blur-sm p-6 rounded-2xl shadow-sm border border-slate-200/60">
        <form method="GET" action="{{ route('recap.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @if(Auth::user()->isAdmin())
            <div>
                <x-input-label for="organization_id" :value="__('Organisasi')" />
                <select name="organization_id" id="organization_id" onchange="this.form.submit()" class="block mt-1 w-full border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl bg-white/50">
                    @foreach($organizations as $org)
                        <option value="{{ $org->id }}" {{ request('organization_id') == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div>
                <x-input-label for="status" :value="__('Status Kupon')" />
                <select name="status" id="status" onchange="this.form.submit()" class="block mt-1 w-full border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl bg-white/50">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Kupon</option>
                    <option value="claimed" {{ request('status') == 'claimed' ? 'selected' : '' }}>Sudah Scan (Claimed)</option>
                    <option value="unclaimed" {{ request('status') == 'unclaimed' ? 'selected' : '' }}>Belum Scan (Pending/Approved)</option>
                </select>
            </div>
        </form>
    </div>

    <div class="bg-white/60 backdrop-blur-sm p-8 rounded-3xl shadow-sm border border-slate-200/60 print-content">
        <!-- Header Laporan (Only for Print) -->
        <div class="hidden print:block mb-8 text-center border-b-2 border-slate-800 pb-6">
            <h1 class="text-3xl font-black uppercase tracking-tighter">{{ $selectedOrganization->name }}</h1>
            <p class="text-slate-600 mt-1">{{ $selectedOrganization->address }}, {{ $selectedOrganization->city }}</p>
            <h2 class="text-xl font-bold mt-4 underline">LAPORAN REKAPITULASI KUPON QURBAN</h2>
            <p class="text-sm text-slate-500 mt-1">Dicetak pada: {{ now()->format('d F Y H:i') }}</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <p class="text-xs font-bold text-slate-500 uppercase">Total Kupon</p>
                <p class="text-2xl font-black text-slate-900">{{ $summary['total_count'] }}</p>
            </div>
            <div class="bg-emerald-50 p-4 rounded-2xl border border-emerald-100">
                <p class="text-xs font-bold text-emerald-600 uppercase">Total Berat</p>
                <p class="text-2xl font-black text-emerald-700">{{ number_format($summary['total_kg'], 2) }} Kg</p>
            </div>
            <div class="bg-blue-50 p-4 rounded-2xl border border-blue-100">
                <p class="text-xs font-bold text-blue-600 uppercase">Sudah Scan</p>
                <p class="text-2xl font-black text-blue-700">{{ $summary['claimed_count'] }}</p>
            </div>
            <div class="bg-amber-50 p-4 rounded-2xl border border-amber-100">
                <p class="text-xs font-bold text-amber-600 uppercase">Belum Scan</p>
                <p class="text-2xl font-black text-amber-700">{{ $summary['unclaimed_count'] }}</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse border border-slate-200">
                <thead>
                    <tr class="bg-slate-100 print:bg-slate-200">
                        <th class="border border-slate-200 px-4 py-3 text-sm font-bold">No.</th>
                        <th class="border border-slate-200 px-4 py-3 text-sm font-bold">ID Kupon</th>
                        <th class="border border-slate-200 px-4 py-3 text-sm font-bold">Penerima</th>
                        <th class="border border-slate-200 px-4 py-3 text-sm font-bold text-center">Paket</th>
                        <th class="border border-slate-200 px-4 py-3 text-sm font-bold text-right">Berat (Kg)</th>
                        <th class="border border-slate-200 px-4 py-3 text-sm font-bold">Status</th>
                        <th class="border border-slate-200 px-4 py-3 text-sm font-bold">Waktu Scan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coupons as $index => $coupon)
                    <tr>
                        <td class="border border-slate-200 px-4 py-2 text-sm">{{ $index + 1 }}</td>
                        <td class="border border-slate-200 px-4 py-2 text-sm font-mono">KPN-{{ str_pad($coupon->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="border border-slate-200 px-4 py-2 text-sm">{{ $coupon->recipient_name ?? 'Umum' }}</td>
                        <td class="border border-slate-200 px-4 py-2 text-sm text-center">{{ $coupon->quantity }}</td>
                        <td class="border border-slate-200 px-4 py-2 text-sm text-right font-bold">{{ number_format($coupon->quantity * $coupon->weight_kg, 2) }}</td>
                        <td class="border border-slate-200 px-4 py-2 text-sm uppercase font-bold text-[10px]">
                            {{ $coupon->status }}
                        </td>
                        <td class="border border-slate-200 px-4 py-2 text-sm italic">
                            {{ $coupon->claimed_at ? $coupon->claimed_at->format('d/m/y H:i') : '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="border border-slate-200 px-4 py-10 text-center text-slate-400 italic">Tidak ada data ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Signature for Print -->
        <div class="hidden print:grid grid-cols-2 mt-12 gap-8 text-center">
            <div></div>
            <div>
                <p class="mb-20">Dicetak Oleh,</p>
                <p class="font-bold underline">{{ Auth::user()->name }}</p>
                <p class="text-xs text-slate-500 uppercase">{{ Auth::user()->role }}</p>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .print-content { 
                box-shadow: none !important; 
                border: none !important; 
                padding: 0 !important;
                margin: 0 !important;
            }
            header, nav { display: none !important; }
        }
    </style>
</x-app-layout>
