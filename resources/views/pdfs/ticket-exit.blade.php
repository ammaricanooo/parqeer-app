<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tiket Keluar - #{{ $transaction->id }}</title>
    <style>
        @page { margin: 0; }
        body {
            font-family: 'Courier', monospace;
            width: 80mm;
            margin: 0 auto;
            padding: 5mm;
            color: #000;
            background: #fff;
            line-height: 1.2;
        }

        /* Header */
        .header {
            text-align: center;
            border-bottom: 2px dashed #000;
            padding-bottom: 3mm;
            margin-bottom: 3mm;
        }

        .header h1 {
            font-size: 22pt;
            font-weight: bold;
            margin: 0;
            letter-spacing: 2px;
        }

        .header p {
            font-size: 10pt;
            margin: 1mm 0;
            text-transform: uppercase;
        }

        /* Plate Number Section */
        .plate-box {
            border: 2pt solid #000;
            padding: 4mm 2mm;
            margin: 2mm 0;
            text-align: center;
        }

        .plate-label {
            font-size: 9pt;
            margin-bottom: 2mm;
            display: block;
        }

        .plate-number {
            font-size: 28pt;
            font-weight: bold;
            display: block;
        }

        /* Detail Table */
        .details-table {
            width: 100%;
            margin: 2mm 0;
            border-bottom: 1px dashed #000;
            padding-bottom: 2mm;
        }

        .details-table td {
            font-size: 9pt;
            padding: 0.8mm 0;
        }

        .label { text-align: left; text-transform: uppercase; color: #444; }
        .value { text-align: right; font-weight: bold; }

        /* Total Section */
        .total-box {
            margin: 3mm 0;
            padding: 3mm;
            background: #f0f0f0;
            border: 1px solid #000;
            text-align: center;
        }

        .total-label {
            font-size: 10pt;
            margin-bottom: 1mm;
        }

        .total-amount {
            font-size: 20pt;
            font-weight: bold;
        }

        /* Status Badge */
        .status-badge {
            text-align: center;
            border: 1.5pt solid #000;
            padding: 2mm;
            margin: 3mm 0;
            font-weight: bold;
            font-size: 10pt;
            text-transform: uppercase;
        }

        .paid { background: #000; color: #fff; }
        .pending { background: #fff; color: #000; border-style: double; }

        /* Footer */
        .footer {
            text-align: center;
            font-size: 8pt;
            margin-top: 4mm;
            border-top: 1px dashed #000;
            padding-top: 3mm;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>PARQEER</h1>
        <p>TIKET KELUAR</p>
        <small>ID: #{{ str_pad($transaction->id, 8, '0', STR_PAD_LEFT) }}</small>
    </div>

    <div class="plate-box">
        <span class="plate-label">NOMOR KENDARAAN</span>
        <span class="plate-number">{{ strtoupper($transaction->plate_number) }}</span>
    </div>

    <table class="details-table">
        <tr>
            <td class="label">Area</td>
            <td class="value">{{ $transaction->area->name }}</td>
        </tr>
        <tr>
            <td class="label">Waktu Masuk</td>
            <td class="value">{{ $transaction->entry_time->format('d/m/y H:i') }}</td>
        </tr>
        <tr>
            <td class="label">Waktu Keluar</td>
            <td class="value">{{ $transaction->exit_time->format('d/m/y H:i') }}</td>
        </tr>
        <tr>
            <td class="label">Durasi</td>
            <td class="value">{{ $transaction->duration_minutes }} Menit</td>
        </tr>
        <tr>
            <td class="label">Skema Tarif</td>
            <td class="value">Rp{{ number_format($transaction->rate->amount, 0, ',', '.') }}/jam</td>
        </tr>
    </table>

    <div class="total-box">
        <div class="total-label">ESTIMASI BIAYA</div>
        <div class="total-amount">Rp{{ number_format($transaction->amount, 0, ',', '.') }}</div>
    </div>

    @if($transaction->status === 'paid')
        <div class="status-badge paid">
            SUDAH DIBAYAR / PAID
        </div>
    @else
        <div class="status-badge pending">
            MENUNGGU PEMBAYARAN
        </div>
    @endif

    <div class="footer">
        <p>Simpan tiket ini untuk bukti keluar</p>
        <p>Operator: {{ auth()->user()->name ?? 'System' }}</p>
        <p style="margin-top: 2mm;">{{ $generatedAt->format('d/m/Y H:i:s') }}</p>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $pdf->page_text(0, 0, "", null, 0, array(0,0,0));
            $pdf->javascript("print();");
        }
    </script>
</body>
</html>