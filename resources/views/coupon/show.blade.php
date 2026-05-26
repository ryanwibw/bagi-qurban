<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                {{ __('Detail Kupon') }}
            </h2>
            <a href="{{ route('coupon.index') }}" class="text-slate-500 hover:text-slate-700 font-semibold text-sm transition">
                &larr; Kembali ke Daftar
            </a>
        </div>
    </x-slot>

    <style>
        /* Koordinat Default - Disesuaikan untuk rasio 3.5 x 1.5 (Landscape) */
        :root {
            --qr-top: 50%;
            --qr-left: 14%;
            --qr-size: 120px;

            --sn-top: 67%;
            --sn-left: 64%;

            --qty-top: 43.5%;
            --qty-left: 42%;

            --org-top: 43%;
            --org-left: 84%;
        }

        .coupon-canvas {
            width: 100%;
            max-width: 500px;
            aspect-ratio: 3.5 / 1.5; /* Rasio Kupon Standar */
            background-image: url('{{ asset('coupon-bg.png') }}');
            background-size: 100% 100%; /* Sesuai dengan cetakan, tidak di-crop */
            background-position: center;
            position: relative;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border: 1px solid black; /* Border Hitam Tegas */
            overflow: hidden;
            margin: 0 auto;
        }

        .helper-grid {
            position: absolute;
            inset: 0;
            background-image: 
                linear-gradient(to right, rgba(0,255,0,0.1) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(0,255,0,0.1) 1px, transparent 1px);
            background-size: 10% 10%;
            display: none;
            z-index: 50;
        }

        .helper-label {
            position: absolute;
            font-size: 8px;
            color: rgba(0,255,0,0.5);
            pointer-events: none;
        }

        .dynamic-element {
            position: absolute;
            transform: translate(-50%, -50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 10;
        }

        .qr-overlay { top: var(--qr-top); left: var(--qr-left); }
        .sn-overlay { top: var(--sn-top); left: var(--sn-left); }
        .qty-overlay { top: var(--qty-top); left: var(--qty-left); }
        .org-overlay { top: var(--org-top); left: var(--org-left); }
    </style>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
        <!-- Visual Rendering (The Hybrid Canva Part) -->
        <div class="space-y-4">
            <div class="flex justify-between items-end ml-1">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest">Visual Preview</h3>
                <button onclick="toggleGrid()" class="text-[10px] bg-slate-200 hover:bg-slate-300 px-2 py-1 rounded font-bold transition uppercase">
                    Toggle Grid Helper
                </button>
            </div>
            
            <div class="relative group">
                <!-- The Actual Canvas -->
                <div class="coupon-canvas" id="coupon-container">
                    <!-- Helper Grid -->
                    <div id="grid-helper" class="helper-grid">
                        @for($i = 10; $i < 100; $i += 10)
                            <div class="helper-label" style="top: {{ $i }}%; left: 2px;">{{ $i }}%</div>
                            <div class="helper-label" style="left: {{ $i }}%; top: 2px;">{{ $i }}%</div>
                        @endfor
                    </div>

                    <!-- 1. Organization Name Overlay -->
                    <div class="dynamic-element org-overlay w-full text-center leading-none">
                        <h4 class="text-xs font-bold uppercase text-slate-800 drop-shadow-sm mb-0">
                            {{ $coupon->organization->name }}
                        </h4>
                        <div class="text-[7px] font-medium text-slate-500 tracking-tight mt-0.5 opacity-80 leading-tight px-10">
                            {{ $coupon->organization->address }}
                        </div>
                    </div>

                    <!-- 2. QR Code Overlay -->
                    <div class="dynamic-element qr-overlay">
                        <div class="bg-white p-2 rounded-xl shadow-lg border border-slate-100">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ $coupon->qr_code }}" 
                                 style="width: var(--qr-size); height: var(--qr-size);" alt="QR">
                        </div>
                    </div>

                    <!-- 3. Serial Number Overlay -->
                    <div class="dynamic-element sn-overlay">    
                            <span class="text-[15px] font-semibold font-black text-gray-100 tracking-tighter">
                                {{ str_pad($coupon->serial_number, 5, '0', STR_PAD_LEFT) }}
                            </span>
                    </div>

                    <!-- 4. Quantity Overlay -->
                    <div class="dynamic-element qty-overlay">
                        <div class="text-center flex flex-row items-baseline gap-1">
                            <div class="text-2xl font-black text-slate-900 leading-none">{{ $coupon->weight_kg + 0 }}</div>
                            <div class="text-[8px] font-bold text-slate-500">KG</div>
                        </div>
                    </div>
                </div>
            </div>
            <p class="text-center text-[10px] text-slate-400 font-medium px-4 leading-relaxed italic">
                Tip: Aktifkan "Grid Helper" untuk melihat koordinat % jika posisi elemen belum pas dengan desain Canva Anda.
            </p>
        </div>

        <!-- Data Details -->
        <div class="space-y-6">
            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest">Informasi Lengkap</h3>
            
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4 pb-4 border-b border-slate-50">
                        <div class="text-sm text-slate-500">Nomor Kupon</div>
                        <div class="text-sm font-bold text-slate-900 text-right">KPN-{{ str_pad($coupon->serial_number, 5, '0', STR_PAD_LEFT) }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 pb-4 border-b border-slate-50">
                        <div class="text-sm text-slate-500">Berat Daging</div>
                        <div class="text-sm font-bold text-emerald-600 text-right">{{ $coupon->weight_kg + 0 }} Kg</div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-3">
                @if($coupon->status == 'pending' && Auth::user()->isAdmin())
                    <form action="{{ route('coupon.approve', $coupon) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-2xl font-bold hover:bg-blue-700 transition shadow-lg flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Setujui Kupon Sekarang
                        </button>
                    </form>
                @endif
                
                @if($coupon->status == 'approved')
                    <a href="{{ route('coupon.print', ['ids' => $coupon->id]) }}" target="_blank" class="w-full bg-slate-800 text-white py-4 rounded-2xl font-bold hover:bg-slate-900 transition shadow-lg flex items-center justify-center gap-2 text-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Cetak Kupon Ini
                    </a>
                @endif

                <form action="{{ route('coupon.destroy', $coupon) }}" method="POST" onsubmit="return confirm('Hapus kupon ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full border-2 border-red-100 text-red-600 py-4 rounded-2xl font-bold hover:bg-red-50 transition">
                        Hapus Data Kupon
                    </button>
                </form>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        function toggleGrid() {
            const grid = document.getElementById('grid-helper');
            grid.style.display = grid.style.display === 'block' ? 'none' : 'block';
        }
    </script>
    @endpush
</x-app-layout>
