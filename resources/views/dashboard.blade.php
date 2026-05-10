<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Stats Card -->
        <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200 p-6 flex flex-col justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Total Kupon</p>
                <h3 class="mt-2 text-4xl font-bold text-slate-900">{{ $stats['total'] }}</h3>
            </div>
            <a href="{{ route('coupon.index') }}" class="mt-4 flex items-center text-sm text-emerald-600 font-semibold cursor-pointer hover:underline">
                <span>Lihat Detail</span>
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200 p-6 flex flex-col justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Kupon Approved</p>
                <h3 class="mt-2 text-4xl font-bold text-slate-900">{{ $stats['approved'] }}</h3>
            </div>
            <a href="{{ route('coupon.index', ['status' => 'approved']) }}" class="mt-4 flex items-center text-sm text-blue-600 font-semibold cursor-pointer hover:underline">
                <span>Siap Cetak</span>
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200 p-6 flex flex-col justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Kupon Claimed</p>
                <h3 class="mt-2 text-4xl font-bold text-slate-900">{{ $stats['claimed'] }}</h3>
            </div>
            <a href="{{ route('coupon.index', ['status' => 'claimed']) }}" class="mt-4 flex items-center text-sm text-amber-600 font-semibold cursor-pointer hover:underline">
                <span>Laporan Distribusi</span>
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200 p-6 flex flex-col justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Data Panitia</p>
                <h3 class="mt-2 text-4xl font-bold text-slate-900">{{ $stats['panitia'] }}</h3>
            </div>
            <a href="{{ route('panitia.index') }}" class="mt-4 flex items-center text-sm text-purple-600 font-semibold cursor-pointer hover:underline">
                <span>Kelola Panitia</span>
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-emerald-900 rounded-3xl p-8 text-white relative overflow-hidden shadow-xl h-full">
                <div class="relative z-10 max-w-2xl">
                    <h3 class="text-3xl font-bold mb-4">Selamat Datang di Bagi Qurban</h3>
                    <p class="text-emerald-100 text-lg mb-6 leading-relaxed">
                        Kelola distribusi hewan qurban dengan sistem kupon QR yang aman dan transparan. 
                        Mulai dengan membuat kupon baru atau memantau laporan real-time.
                    </p>
                    <div class="flex gap-4">
                        <a href="{{ route('coupon.create') }}" class="bg-white text-emerald-900 px-6 py-3 rounded-xl font-bold hover:bg-emerald-50 transition shadow-lg inline-block">
                            Buat Kupon Baru
                        </a>
                        <a href="{{ route('coupon.scan') }}" class="bg-emerald-800 text-white border border-emerald-700 px-6 py-3 rounded-xl font-bold hover:bg-emerald-700 transition inline-block">
                            Scan QR
                        </a>
                    </div>
                </div>
                <!-- Abstract Decoration -->
                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-emerald-800 rounded-full blur-3xl opacity-50"></div>
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-emerald-700 rounded-full blur-2xl opacity-30"></div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-3xl border border-slate-200 p-8 flex flex-col items-center justify-center">
            <h4 class="text-lg font-bold text-slate-800 mb-6 w-full text-center">Statistik Scan Kupon</h4>
            <div class="relative w-full aspect-square max-w-[200px]">
                <canvas id="couponChart"></canvas>
            </div>
            <div class="mt-6 grid grid-cols-2 gap-4 w-full text-center">
                <div>
                    <p class="text-xs text-slate-500 uppercase font-bold">Sudah Scan</p>
                    <p class="text-xl font-bold text-emerald-600">{{ $chartData['claimed'] }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 uppercase font-bold">Belum Scan</p>
                    <p class="text-xl font-bold text-slate-400">{{ $chartData['unclaimed'] }}</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('couponChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Sudah Scan', 'Belum Scan'],
                datasets: [{
                    data: [{{ $chartData['claimed'] }}, {{ $chartData['unclaimed'] }}],
                    backgroundColor: ['#10b981', '#e2e8f0'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
