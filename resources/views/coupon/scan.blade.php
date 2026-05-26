<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 leading-tight">
            {{ __('Scan Kupon Cepat') }}
        </h2>
    </x-slot>

    <style>
        #reader video {
            object-fit: cover !important;
            border-radius: 1rem;
        }
        /* Style for the scanning square frame if the library uses CSS for it */
        #reader__scan_region {
            background: rgba(0,0,0,0.1);
        }
    </style>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Scanner (Larger) -->
            <div class="lg:col-span-2">
                <div class="bg-white overflow-hidden shadow-xl rounded-3xl border border-slate-200 p-4 sticky top-6">
                    <div id="reader" class="rounded-2xl overflow-hidden border-none bg-slate-100"></div>
                    
                    <div class="mt-6 bg-emerald-50 border border-emerald-100 p-4 rounded-2xl flex items-center justify-between">
                        <div class="flex items-start gap-3">
                            <div class="bg-emerald-100 p-1.5 rounded-lg text-emerald-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1 v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <p class="text-xs text-emerald-700 leading-relaxed font-medium">
                                Scanner Aktif. Arahkan QR Code ke kotak pemindai untuk klaim otomatis.
                            </p>
                        </div>
                        <div id="scanner-status">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-600">
                                <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2 animate-pulse"></span>
                                ON AIR
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: History List (Smaller) -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow-sm rounded-3xl border border-slate-200 overflow-hidden h-full min-h-[500px] flex flex-col">
                    <div class="p-6 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                        <div>
                            <h3 class="font-bold text-slate-800 text-lg leading-none">History</h3>
                            <p class="text-[10px] text-slate-500 uppercase mt-1 tracking-tighter">Sesi Scan Saat Ini</p>
                        </div>
                        <span id="history-count" class="bg-emerald-600 text-white text-[10px] font-black px-3 py-1 rounded-full">0 KUPON</span>
                    </div>
                    
                    <div id="scan-history" class="flex-1 overflow-y-auto p-4 space-y-3 max-h-[calc(100vh-300px)] bg-slate-50/30">
                        <!-- History items will be prepended here -->
                        <div id="empty-history" class="h-full flex flex-col items-center justify-center text-slate-400 py-20">
                            <svg class="w-12 h-12 mb-3 opacity-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            <p class="text-xs font-bold uppercase tracking-widest opacity-30">No Data</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        const html5QrCode = new Html5Qrcode("reader");
        const historyContainer = document.getElementById('scan-history');
        const emptyHistory = document.getElementById('empty-history');
        const historyCountLabel = document.getElementById('history-count');
        
        // Pre-load audio to ensure they play instantly
        const audioSuccess = new Audio('/audio/success.mp3'); // Replace with your success sound URL
        const audioError = new Audio('/audio/error.mp3'); // Replace with your error sound URL
        
        let scannedCount = 0;
        let lastScannedText = "";
        let lastScannedTime = 0;
        let isProcessing = false;

        const config = { fps: 20, qrbox: { width: 300, height: 300 } };

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
            const now = Date.now();
            
            // Reduced cooldown: 1.5 seconds
            if (decodedText === lastScannedText && (now - lastScannedTime) < 1500) {
                return;
            }

            if (isProcessing) return;

            lastScannedText = decodedText;
            lastScannedTime = now;
            isProcessing = true;

            if (navigator.vibrate) navigator.vibrate(50);

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
                
                // Play Sound based on result
                if (result.success) {
                    audioSuccess.currentTime = 0;
                    audioSuccess.play().catch(e => console.log("Audio play blocked", e));
                } else {
                    audioError.currentTime = 0;
                    audioError.play().catch(e => console.log("Audio play blocked", e));
                }

                addHistoryItem(result, decodedText);

            } catch (error) {
                audioError.currentTime = 0;
                audioError.play().catch(e => console.log("Audio play blocked", e));
                
                addHistoryItem({
                    success: false,
                    message: 'Koneksi terputus.'
                }, decodedText);
            } finally {
                isProcessing = false;
            }
        }

        function addHistoryItem(result, qrCode) {
            if (emptyHistory) emptyHistory.remove();
            
            const time = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            const item = document.createElement('div');
            
            // Use serial_number from response
            const serialNumber = result.data ? result.data.serial_number : (result.serial_number || '???');
            const couponNumber = `KPN-${String(serialNumber).padStart(5, '0')}`;

            if (result.success) {
                scannedCount++;
                historyCountLabel.innerText = `${scannedCount} KUPON`;
                
                item.className = "p-3 rounded-xl border border-emerald-100 bg-white flex items-start gap-3 shadow-sm animate-in slide-in-from-right duration-300";
                item.innerHTML = `
                    <div class="bg-emerald-500 text-white p-1.5 rounded-lg shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-center">
                            <h4 class="text-xs font-black text-emerald-900">${couponNumber}</h4>
                            <span class="text-[9px] font-bold text-slate-400 shrink-0">${time}</span>
                        </div>
                        <p class="text-[11px] font-bold text-slate-700 leading-tight mt-1 truncate">${result.data.recipient} (${result.data.weight_kg}kg)</p>
                        <p class="text-[9px] font-semibold text-emerald-600 mt-0.5">BERHASIL DIKLAIM</p>
                    </div>
                `;
            } else {
                item.className = "p-3 rounded-xl border border-red-100 bg-red-50 flex items-start gap-3 shadow-sm animate-in slide-in-from-right duration-300";
                item.innerHTML = `
                    <div class="bg-red-500 text-white p-1.5 rounded-lg shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-center">
                            <h4 class="text-xs font-black text-red-900">${serialNumber !== '???' ? couponNumber : 'QR INVALID'}</h4>
                            <span class="text-[9px] font-bold text-slate-400 shrink-0">${time}</span>
                        </div>
                        <p class="text-[10px] font-semibold text-red-700 mt-1 line-clamp-2">${result.message}</p>
                    </div>
                `;
            }

            historyContainer.prepend(item);
            historyContainer.scrollTop = 0;
        }

        document.addEventListener('DOMContentLoaded', startScanner);
    </script>
    @endpush
</x-app-layout>