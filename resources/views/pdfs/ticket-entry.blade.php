<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tiket Masuk - #{{ $transaction->id }}</title>
    <style>
        @page {
            margin: 0;
        }

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
            margin: 4mm 0;
            border-bottom: 1px dashed #000;
            padding-bottom: 3mm;
        }

        .details-table td {
            font-size: 10pt;
            padding: 1mm 0;
        }

        .label {
            text-align: left;
            text-transform: uppercase;
        }

        .value {
            text-align: right;
            font-weight: bold;
        }

        /* QR Section */
        .qr-section {
            text-align: center;
            margin-top: 4mm;
        }

        .qr-code img {
            width: 45mm;
            height: 45mm;
            /* Pastikan kontras tinggi untuk scanner gate */
        }

        .qr-text {
            font-size: 11pt;
            font-weight: bold;
            margin-top: 2mm;
            letter-spacing: 1px;
        }

        .qr-hint {
            font-size: 8pt;
            margin-top: 1mm;
        }

        /* Footer */
        .footer {
            text-align: center;
            font-size: 9pt;
            margin-top: 5mm;
            border-top: 1px dashed #000;
            padding-top: 3mm;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>PARQEER</h1>
        <p>SISTEM PARKIR OTOMATIS</p>
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
            <td class="label">Warna</td>
            <td class="value">{{ strtoupper($transaction->vehicle_color) }}</td>
        </tr>
        <tr>
            <td class="label">Masuk</td>
            <td class="value">{{ $transaction->entry_time->format('d/m/y H:i') }}</td>
        </tr>
        <tr>
            <td class="label">Tarif</td>
            <td class="value">Rp{{ number_format($transaction->rate->amount, 0, ',', '.') }}/jam</td>
        </tr>
    </table>

    <div class="qr-section">
        <div class="qr-code">
            <img src="{{ $qrCodeUrl }}" alt="QR Code">
        </div>
        <div class="qr-text">SIMPAN TIKET INI</div>
        <div class="qr-hint">
            Scan pada gate pembayaran
        </div>
    </div>

    <div class="footer">
        <p>Jangan meninggalkan barang berharga</p>
        <p>Kehilangan bukan tanggung jawab pengelola</p>
        <p style="margin-top: 2mm;">{{ $generatedAt->format('d/m/Y H:i:s') }}</p>
    </div>

    {{-- Script Auto-Print --}}
    <script type="text/php">
    if (isset($pdf)) {
        // Pada Dompdf versi terbaru, $pdf adalah objek canvas itu sendiri
        $pdf->page_script('
            $pdf->javascript("print();");
        ');
    }
</script>
</body>

</html>
