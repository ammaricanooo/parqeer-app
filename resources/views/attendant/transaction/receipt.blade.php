<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STRUK PARKIR - #{{ $transaction->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', monospace;
            background: #f5f5f5;
            padding: 20px;
        }
        .receipt-container {
            width: 100%;
            max-width: 400px;
            background: white;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .receipt-header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .receipt-header h1 {
            font-size: 20px;
            margin: 5px 0;
            letter-spacing: 2px;
        }
        .receipt-header p {
            font-size: 11px;
            margin: 2px 0;
        }
        .receipt-body {
            margin: 15px 0;
        }
        .receipt-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            font-size: 13px;
        }
        .receipt-row-full {
            width: 100%;
            margin: 8px 0;
            font-size: 13px;
        }
        .label {
            font-weight: bold;
            width: 40%;
        }
        .value {
            text-align: right;
            width: 60%;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        .divider-solid {
            border-top: 2px solid #000;
            margin: 10px 0;
        }
        .amount-section {
            font-size: 14px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px solid #000;
            font-size: 11px;
        }
        .plate-number {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            padding: 10px;
            background: #f0f0f0;
            border: 2px solid #000;
            margin: 10px 0;
            letter-spacing: 3px;
        }
        .status-paid {
            text-align: center;
            background: #90EE90;
            color: #000;
            padding: 10px;
            margin: 10px 0;
            font-weight: bold;
            border-radius: 3px;
        }
        .status-pending {
            text-align: center;
            background: #FFD700;
            color: #000;
            padding: 10px;
            margin: 10px 0;
            font-weight: bold;
            border-radius: 3px;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .receipt-container {
                box-shadow: none;
                border: none;
                max-width: 100%;
                width: 58mm;
                margin: 0;
                padding: 10px;
            }
            .no-print {
                display: none;
            }
        }
        .no-print {
            text-align: center;
            margin-top: 20px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            background: #007BFF;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-family: Arial, sans-serif;
        }
        .btn-print {
            background: #28a745;
        }
        .btn-back {
            background: #6c757d;
        }
        .btn:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Header -->
        <div class="receipt-header">
            <h1>🅿️ PARKIR</h1>
            <p>APLIKASI SISTEM PARKIR</p>
            <p>No. Struk: #{{ $transaction->id }}</p>
        </div>

        <!-- Plat Nomor -->
        <div class="plate-number">{{ $transaction->plate_number }}</div>

        <!-- Detail Transaksi -->
        <div class="receipt-body">
            <div class="receipt-row">
                <span class="label">Warna:</span>
                <span class="value">{{ $transaction->vehicle_color }}</span>
            </div>
            <div class="receipt-row">
                <span class="label">Area:</span>
                <span class="value">{{ $transaction->area->name }}</span>
            </div>
            <div class="divider"></div>
            
            <div class="receipt-row">
                <span class="label">Masuk:</span>
                <span class="value">{{ $transaction->entry_time->format('d/m/Y H:i') }}</span>
            </div>
            <div class="receipt-row">
                <span class="label">Keluar:</span>
                <span class="value">{{ $transaction->exit_time->format('d/m/Y H:i') }}</span>
            </div>
            <div class="divider"></div>

            <div class="receipt-row">
                <span class="label">Durasi:</span>
                <span class="value">{{ $transaction->duration_minutes }} menit</span>
            </div>
            @php
                $durationHours = ceil($transaction->duration_minutes / 60);
                $ratePerHour = $transaction->rate->amount;
            @endphp
            <div class="receipt-row">
                <span class="label">Jam:</span>
                <span class="value">{{ $durationHours }} jam</span>
            </div>
            <div class="receipt-row">
                <span class="label">Tarif/Jam:</span>
                <span class="value">Rp {{ number_format($ratePerHour, 0, ',', '.') }}</span>
            </div>
            
            <div class="divider-solid"></div>

            <div class="receipt-row amount-section">
                <span class="label">TOTAL ({{ $durationHours }}j x Rp {{ number_format($ratePerHour, 0, ',', '.') }}):</span>
                <span class="value">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
            </div>

            <div class="divider-solid"></div>

            @if ($transaction->status === 'paid')
                <div class="status-paid">✓ SUDAH DIBAYAR</div>
            @else
                <div class="status-pending">⚠ BELUM DIBAYAR</div>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Terima kasih telah menggunakan layanan kami</p>
            <p style="margin-top: 5px;">{{ now()->format('d/m/Y H:i:s') }}</p>
            <p style="margin-top: 5px; font-size: 10px;">Operator: {{ auth()->user()->name }}</p>
        </div>
    </div>

    <div class="no-print">
        <button onclick="window.print()" class="btn btn-print">🖨️ Cetak Struk</button>
        <a href="{{ route('attendant.transaction.index') }}" class="btn btn-back">← Kembali</a>

        @if ($transaction->status !== 'paid')
            <form action="{{ route('attendant.transaction.pay', $transaction->id) }}" method="POST" style="display: inline;">
                @csrf
                <input type="hidden" name="paid_amount" value="{{ $transaction->amount }}">
                <button type="submit" class="btn" style="background: #ffc107;">Konfirmasi Pembayaran</button>
            </form>
        @endif
    </div>
</body>
</html>
