<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STRUK KELUAR - #{{ $transaction->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Courier New', Courier, monospace; 
            background: #f3f4f6; 
            padding: 20px; 
            color: #333;
        }

        .receipt-container { 
            width: 100%; 
            max-width: 320px; 
            background: white; 
            margin: 0 auto; 
            padding: 20px; 
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        /* Header */
        .receipt-header { text-align: center; margin-bottom: 15px; }
        .receipt-header h1 { 
            font-family: Arial, sans-serif; 
            font-size: 24px; 
            font-weight: 900; 
            letter-spacing: -1px;
            color: #2563eb;
        }
        .receipt-header p { font-size: 10px; text-transform: uppercase; color: #666; }

        .plate-number { 
            font-size: 22px; 
            font-weight: bold; 
            text-align: center; 
            padding: 10px; 
            background: #f8fafc; 
            border: 2px dashed #333; 
            margin: 15px 0; 
            letter-spacing: 2px;
            font-style: italic;
        }

        /* Body Details */
        .receipt-body { margin-bottom: 15px; }
        .receipt-row { 
            display: flex; 
            justify-content: space-between; 
            margin: 6px 0; 
            font-size: 12px; 
        }
        .label { color: #64748b; }
        .value { font-weight: bold; text-align: right; }

        .divider { border-top: 1px dashed #cbd5e1; margin: 10px 0; }

        /* Total Section */
        .total-section {
            margin-top: 15px;
            padding: 10px 0;
            border-top: 2px solid #333;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .total-label { font-size: 13px; font-weight: bold; }
        .total-amount { font-size: 18px; font-weight: 900; font-family: Arial, sans-serif; }

        /* Status Badges */
        .status-badge {
            text-align: center;
            padding: 8px;
            margin: 15px 0;
            font-weight: bold;
            font-size: 12px;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .status-paid { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .status-pending { background: #fef9c3; color: #854d0e; border: 1px solid #fef08a; }

        /* Footer */
        .footer { 
            text-align: center; 
            font-size: 10px; 
            border-top: 1px solid #f1f5f9;
            padding-top: 15px;
            color: #94a3b8;
        }

        /* Print Settings */
        @media print {
            @page { size: 58mm auto; margin: 0; }
            body { background: white; padding: 0; margin: 0; }
            .receipt-container { width: 58mm; max-width: 58mm; padding: 8px; border: none; box-shadow: none; }
            .no-print { display: none !important; }
        }

        /* Controls */
        .no-print { text-align: center; margin-top: 20px; max-width: 400px; margin-inline: auto; }
        .btn { 
            display: inline-block; padding: 10px 20px; border-radius: 6px; 
            font-weight: bold; cursor: pointer; font-family: sans-serif; font-size: 13px;
            border: none; transition: 0.2s;
        }
        .btn-print { background: #059669; color: white; margin-bottom: 10px; }
        .btn-back { background: #64748b; color: white; text-decoration: none; }
        
        /* Payment Card */
        .payment-card {
            background: white; padding: 20px; border-radius: 8px; margin-top: 20px;
            border: 1px solid #e2e8f0; text-align: left;
        }
        .input-group { margin-bottom: 15px; }
        .input-group label { display: block; font-size: 12px; font-weight: bold; margin-bottom: 5px; color: #475569; }
        .input-field { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 16px; font-weight: bold; }
    </style>
</head>

<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <h1>PARQEER</h1>
            <p>Bukti Pembayaran Parkir</p>
            <p>ID: #{{ $transaction->id }}</p>
        </div>

        <div class="plate-number">{{ $transaction->plate_number }}</div>

        <div class="receipt-body">
            <div class="receipt-row">
                <span class="label">Area:</span>
                <span class="value">{{ $transaction->area->name }}</span>
            </div>
            <div class="receipt-row">
                <span class="label">Masuk:</span>
                <span class="value">{{ $transaction->entry_time->format('d/m/y H:i') }}</span>
            </div>
            <div class="receipt-row">
                <span class="label">Keluar:</span>
                <span class="value">{{ $transaction->exit_time->format('d/m/y H:i') }}</span>
            </div>
            
            <div class="divider"></div>

            <div class="receipt-row">
                <span class="label">Durasi:</span>
                <span class="value">{{ $transaction->duration_minutes }} Menit</span>
            </div>

            @php
                $durationHours = ceil($transaction->duration_minutes / 60);
                $ratePerHour = $transaction->rate->amount;
            @endphp

            <div class="receipt-row">
                <span class="label">Tarif ({{ $durationHours }} jam):</span>
                <span class="value">@Rp {{ number_format($ratePerHour, 0, ',', '.') }}</span>
            </div>

            <div class="total-section">
                <div class="total-row">
                    <span class="total-label">TOTAL</span>
                    <span class="total-amount">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                </div>
            </div>

            @if ($transaction->status === 'paid')
                <div class="receipt-row" style="margin-top: 10px;">
                    <span class="label">Bayar:</span>
                    <span class="value">Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</span>
                </div>
                <div class="receipt-row">
                    <span class="label">Kembali:</span>
                    <span class="value">Rp {{ number_format($transaction->change, 0, ',', '.') }}</span>
                </div>
                <div class="status-badge status-paid">✓ Lunas</div>
            @else
                <div class="status-badge status-pending">⚠ Menunggu Pembayaran</div>
            @endif
        </div>

        <div class="footer">
            <p>Terima kasih atas kunjungan Anda</p>
            <p style="margin-top: 4px;">{{ now()->format('d/m/Y H:i:s') }}</p>
            <p style="margin-top: 2px;">OP: {{ auth()->user()->name }}</p>
        </div>
    </div>

    <div class="no-print">
        <button onclick="window.print()" class="btn btn-print" style="width: 100%;">🖨️ Cetak Struk (58mm)</button>
        <a href="{{ route('attendant.transaction.index') }}" class="btn btn-back" style="width: 100%;">← Kembali ke Daftar</a>

        @if ($transaction->status !== 'paid')
            <div class="payment-card shadow-lg">
                <h4 style="font-weight: 900; font-size: 14px; margin-bottom: 15px; border-bottom: 2px solid #f3f4f6; padding-bottom: 10px;">PROSES PEMBAYARAN</h4>
                <form action="{{ route('attendant.transaction.pay', $transaction->id) }}" method="POST">
                    @csrf
                    <div class="input-group">
                        <label>JUMLAH UANG DITERIMA (RP)</label>
                        <input type="number" id="paidAmount" name="paid_amount" required class="input-field" placeholder="0" autofocus />
                    </div>
                    
                    <div id="changeInfo" style="margin-bottom: 15px; min-height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 6px;"></div>
                    
                    <button type="submit" id="payBtn" class="btn" style="background: #2563eb; color: white; width: 100%; height: 50px; font-size: 16px;" disabled>KONFIRMASI BAYAR</button>
                </form>
            </div>

            <script>
                const totalAmount = {{ $transaction->amount }};
                const paidInput = document.getElementById('paidAmount');
                const changeInfo = document.getElementById('changeInfo');
                const payBtn = document.getElementById('payBtn');

                paidInput.addEventListener('input', function() {
                    const paid = parseInt(this.value) || 0;
                    const change = paid - totalAmount;

                    if (paid >= totalAmount) {
                        changeInfo.style.background = '#dcfce7';
                        changeInfo.innerHTML = '<b style="color: #166534">Kembali: Rp ' + change.toLocaleString('id-ID') + '</b>';
                        payBtn.disabled = false;
                        payBtn.style.opacity = '1';
                    } else {
                        changeInfo.style.background = paid > 0 ? '#fee2e2' : 'transparent';
                        changeInfo.innerHTML = paid > 0 ? '<b style="color: #991b1b">Kurang: Rp ' + (totalAmount - paid).toLocaleString('id-ID') + '</b>' : '';
                        payBtn.disabled = true;
                        payBtn.style.opacity = '0.5';
                    }
                });
            </script>
        @endif
    </div>
</body>
</html>