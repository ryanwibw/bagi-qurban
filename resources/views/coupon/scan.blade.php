<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 leading-tight">
            {{ __('Scan Kupon Qurban') }}
        </h2>
    </x-slot>

    <div class="max-w-xl mx-auto space-y-6">
        <!-- Scanner Container -->
        <div class="bg-white overflow-hidden shadow-xl rounded-3xl border border-slate-200 p-4">
            <div id="reader" class="rounded-2xl overflow-hidden border-none"></div>
            
            <div id="result-container" class="mt-6 hidden animate-in fade-in zoom-in duration-300">
                <div id="result-alert" class="p-6 rounded-2xl border flex flex-col items-center text-center space-y-3">
                    <div id="result-icon" class="w-16 h-16 rounded-full flex items-center justify-center mb-2">
                        <!-- Icon injected via JS -->
                    </div>
                    <h3 id="result-title" class="text-xl font-bold"></h3>
                    <p id="result-message" class="text-slate-600 font-medium"></p>
                    <div id="result-details" class="w-full mt-4 pt-4 border-t border-slate-100 grid grid-cols-2 gap-4 text-left">
                        <!-- Details injected via JS -->
                    </div>
                    <button onclick="resetScanner()" class="mt-6 w-full bg-slate-800 text-white py-3 rounded-xl font-bold hover:bg-slate-900 transition">
                        Scan Lagi
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-emerald-50 border border-emerald-100 p-6 rounded-2xl flex items-start gap-4">
            <div class="bg-emerald-100 p-2 rounded-lg text-emerald-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1 v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <h4 class="font-bold text-emerald-900">Petunjuk Pemindaian</h4>
                <p class="text-sm text-emerald-700 leading-relaxed mt-1">
                    Pastikan QR Code berada di area terang dan terlihat jelas di kamera. Sistem akan otomatis memvalidasi status kupon setelah terdeteksi.
                </p>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        const html5QrCode = new Html5Qrcode("reader");
        const resultContainer = document.getElementById('result-container');
        const resultAlert = document.getElementById('result-alert');
        const resultIcon = document.getElementById('result-icon');
        const resultTitle = document.getElementById('result-title');
        const resultMessage = document.getElementById('result-message');
        const resultDetails = document.getElementById('result-details');
        const readerElement = document.getElementById('reader');

        let isScanning = true;

        const config = { fps: 10, qrbox: { width: 250, height: 250 } };

        const startScanner = () => {
            html5QrCode.start(
                { facingMode: "environment" }, 
                config, 
                onScanSuccess
            ).catch(err => {
                console.error("Gagal memulai scanner:", err);
            });
        };

        async function onScanSuccess(decodedText, decodedResult) {
            if (!isScanning) return;
            isScanning = false;

            // Vibrate if supported
            if (navigator.vibrate) navigator.vibrate(100);

            // Audio Feedback
            const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');
            audio.play().catch(() => {});

            html5QrCode.stop();
            readerElement.classList.add('hidden');

            try {
                const response = await fetch('{{ route('coupon.claim') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ qr_code: decodedText })
                });

                const result = await response.json();
                showResult(result);

            } catch (error) {
                showResult({
                    success: false,
                    message: 'Terjadi kesalahan sistem. Silakan coba lagi.'
                });
            }
        }

        function showResult(result) {
            resultContainer.classList.remove('hidden');
            
            if (result.success) {
                resultAlert.className = "p-6 rounded-2xl border-2 border-emerald-200 bg-emerald-50 flex flex-col items-center text-center space-y-3";
                resultIcon.className = "w-16 h-16 rounded-full flex items-center justify-center mb-2 bg-emerald-100 text-emerald-600 shadow-sm";
                resultIcon.innerHTML = `<svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>`;
                resultTitle.innerText = "Kupon Valid!";
                resultTitle.className = "text-2xl font-black text-emerald-900";
                resultMessage.innerText = result.message;
                
                resultDetails.innerHTML = `
                    <div>
                        <div class="text-[10px] text-emerald-600 font-bold uppercase">Penerima</div>
                        <div class="text-sm font-bold text-slate-800">${result.data.recipient}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-[10px] text-emerald-600 font-bold uppercase">Berat Daging</div>
                        <div class="text-sm font-bold text-slate-800">${result.data.quantity} Kg</div>
                    </div>
                `;
            } else {
                resultAlert.className = "p-6 rounded-2xl border-2 border-red-200 bg-red-50 flex flex-col items-center text-center space-y-3";
                resultIcon.className = "w-16 h-16 rounded-full flex items-center justify-center mb-2 bg-red-100 text-red-600 shadow-sm";
                resultIcon.innerHTML = `<svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>`;
                resultTitle.innerText = "Klaim Gagal";
                resultTitle.className = "text-2xl font-black text-red-900";
                resultMessage.innerText = result.message;
                resultDetails.innerHTML = '';
            }
        }

        function resetScanner() {
            isScanning = true;
            resultContainer.classList.add('hidden');
            readerElement.classList.remove('hidden');
            startScanner();
        }

        document.addEventListener('DOMContentLoaded', startScanner);
    </script>
    @endpush
</x-app-layout>
