<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STRUK MASUK - #{{ $transaction->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', monospace; background: #f5f5f5; padding: 20px; }
        .receipt-container { width: 100%; max-width: 400px; background: white; margin: 0 auto; padding: 20px; border: 1px solid #ddd; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .receipt-header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 15px; }
        .receipt-header h1 { font-size: 20px; margin: 5px 0; letter-spacing: 2px; }
        .receipt-header p { font-size: 11px; margin: 2px 0; }
        .receipt-body { margin: 15px 0; }
        .receipt-row { display: flex; justify-content: space-between; margin: 8px 0; font-size: 13px; }
        .label { font-weight: bold; width: 40%; }
        .value { text-align: right; width: 60%; }
        .divider { border-top: 1px dashed #000; margin: 10px 0; }
        .plate-number { font-size: 18px; font-weight: bold; text-align: center; padding: 10px; background: #f0f0f0; border: 2px solid #000; margin: 10px 0; letter-spacing: 3px; }
        .footer { text-align: center; margin-top: 15px; padding-top: 10px; border-top: 2px solid #000; font-size: 11px; }
        @media print { body { background: white; padding: 0; } .receipt-container { box-shadow: none; border: none; max-width: 100%; width: 58mm; margin: 0; padding: 10px; } .no-print { display: none; } }
        .no-print { text-align: center; margin-top: 20px; }
        .btn { display: inline-block; padding: 10px 20px; margin: 5px; background: #007BFF; color: white; text-decoration: none; border: none; border-radius: 3px; cursor: pointer; font-family: Arial, sans-serif; }
        .btn:hover { opacity: 0.8; }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <h1>🅿️ MASUK</h1>
            <p>SISTEM PARKIR</p>
            <p>No. Struk: #{{ $transaction->id }}</p>
        </div>

        <div class="plate-number">{{ $transaction->plate_number }}</div>

        <div class="receipt-body">
            <div class="receipt-row">
                <span class="label">Warna:</span>
                <span class="value">{{ $transaction->vehicle_color }}</span>
            </div>
            <div class="receipt-row">
                <span class="label">Area:</span>
                <span class="value">{{ $transaction->area->name }}</span>
            </div>
            <div class="receipt-row">
                <span class="label">Masuk:</span>
                <span class="value">{{ $transaction->entry_time->format('d/m/Y H:i') }}</span>
            </div>
            <div class="divider"></div>
            <p style="text-align: center; font-size: 11px; font-weight: bold;">Tunjukkan QR saat keluar</p>
        </div>

        <div class="footer">
            <p>Aplikasi Sistem Parkir</p>
            <p style="margin-top: 5px;">{{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>

    <div class="no-print">
        <button onclick="window.print()" class="btn">🖨️ Cetak</button>
        <button onclick="window.history.back()" class="btn" style="background: #6c757d;">← Kembali</button>
    </div>
</body>
</html>
