<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <style>
        /* Base Reset */
        body {
            margin: 0;
            padding: 0;
            background-color: #f1f5f9;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        /* SINKRONISASI TOTAL DENGAN DETAIL VIEW (Show) */
        :root {
            /* Koordinat Persentase - HARUS SAMA DENGAN SHOW.BLADE */
            --qr-top: 50%;
            --qr-left: 15%;
            --qr-size: 0.85in; /* Disesuaikan agar proporsional dengan 3.5in */

            --sn-top: 67%;
            --sn-left: 64%;

            --qty-top: 43.5%;
            --qty-left: 42%;

            --org-top: 43%;
            --org-left: 84%;
        }

        /* Toolbar Styles */
        .no-print {
            background-color: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem;
            position: sticky;
            top: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            z-index: 50;
        }

        .btn-print {
            background-color: #059669;
            color: white;
            padding: 0.6rem 2rem;
            border-radius: 0.75rem;
            font-weight: bold;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.2s;
        }

        .btn-print:hover {
            background-color: #047857;
        }

        /* Page Layout for A4 */
        .page-container {
            width: 210mm;
            margin: 20px auto;
            background-color: white;
            padding: 10mm;
            display: flex;
            flex-wrap: wrap;
            gap: 10mm 5mm; /* Ruang antar kupon */
            box-sizing: border-box;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            justify-content: center;
        }

        /* Coupon Card Design */
        .coupon-canvas {
            width: 3.5in !important;
            height: 1.5in !important;
            min-width: 3.5in;
            min-height: 1.5in;
            position: relative;
            overflow: hidden;
            border: 1px solid black;
            background-image: url('{{ asset('coupon-bg.png') }}');
            background-size: 100% 100%; /* SAMA DENGAN SHOW.BLADE */
            background-repeat: no-repeat;
            background-position: center;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            break-inside: avoid;
            box-sizing: border-box;
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

        /* Content Styling - Menggunakan ukuran yang lebih kecil agar proporsional */
        .org-name {
            width: 140px;
            text-align: center;
            line-height: 1.1;
        }
        .org-name h4 {
            font-size: 6.5pt; /* Diperkecil agar pas */
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            color: #1e293b;
            margin: 0;
            text-shadow: 0 1px 1px rgba(255,255,255,0.3);
        }
        .org-address {
            font-size: 4.5pt;
            font-weight: 500;
            color: #64748b;
            margin-top: 1px;
            display: block;
        }

        .qr-box {
            background: white;
            padding: 3px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .qr-img {
            width: var(--qr-size);
            height: var(--qr-size);
            display: block;
        }

        .sn-text {
            font-size: 10pt; /* Diperkecil dari 12pt */
            font-weight: 900;
            color: #f1f5f9;
            letter-spacing: -0.01em;
            font-family: monospace;
            text-shadow: 0 1px 2px rgba(0,0,0,0.6);
        }

        .qty-box {
            display: flex;
            align-items: baseline;
            gap: 1px;
        }
        .qty-val {
            font-size: 16pt; /* Diperkecil dari 20pt agar tidak "kebesaran" */
            font-weight: 900;
            color: #0f172a;
            line-height: 1;
        }
        .qty-unit {
            font-size: 5.5pt;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
        }

        /* Print Specifics */
        @media print {
            @page {
                size: A4;
                margin: 0; /* Menghilangkan Header/Footer Browser (Tanggal, URL, dll) */
            }
            body { background-color: white; }
            .no-print { display: none; }
            .page-container {
                margin: 0;
                padding: 10mm; /* Memberi jarak aman dari tepi kertas fisik */
                box-shadow: none;
                width: 210mm;
                display: flex;
                flex-wrap: wrap;
                justify-content: flex-start;
            }
            .coupon-canvas {
                border: 1px solid black;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <div style="background-color: #059669; width: 2.5rem; height: 2.5rem; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.25rem;">B</div>
            <h1 style="font-weight: 800; font-size: 1.25rem; margin: 0; color: #064e3b;">Preview Cetak Kupon</h1>
        </div>
        <div style="display: flex; gap: 1.5rem; align-items: center;">
            <div style="text-align: right;">
                <p style="font-size: 0.875rem; color: #334155; font-weight: 600; margin: 0;">Penting!</p>
                <p style="font-size: 0.75rem; color: #64748b; margin: 0;">Centang "Background Graphics" agar desain muncul.</p>
            </div>
            <button onclick="window.print()" class="btn-print">
                Cetak {{ count($coupons) }} Kupon
            </button>
        </div>
    </div>

    <div class="page-container">
        @foreach($coupons as $coupon)
            <div class="coupon-canvas">
                <!-- 1. Organization -->
                <div class="dynamic-element org-overlay">
                    <div class="org-name">
                        <h4>{{ $coupon->organization->name }}</h4>
                        <span class="org-address">{{ Str::limit($coupon->organization->address, 40) }}</span>
                    </div>
                </div>

                <!-- 2. QR Code -->
                <div class="dynamic-element qr-overlay">
                    <div class="qr-box">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $coupon->qr_code }}" class="qr-img" alt="QR">
                    </div>
                </div>

                <!-- 3. Serial Number -->
                <div class="dynamic-element sn-overlay">
                    <span class="sn-text">{{ str_pad($coupon->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>

                <!-- 4. Quantity -->
                <div class="dynamic-element qty-overlay">
                    <div class="qty-box">
                        <span class="qty-val">{{ $coupon->quantity }}</span>
                        <span class="qty-unit">KG</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</body>
</html>
