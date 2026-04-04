<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { 
            margin: 1.5cm; 
            size: A4;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: "Times New Roman", Times, serif; /* Font standar laporan formal */
            color: #000; 
            line-height: 1.2;
            background: #fff;
            margin: 20px;
        }

        .container { width: 100%; }

        /* Header Formal */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .report-title {
            text-align: right;
            font-size: 16px;
            font-weight: bold;
        }
        .period-text {
            text-align: right;
            font-size: 11px;
            font-style: italic;
            margin-top: 5px;
        }

        /* Ringkasan Eksekutif */
        .summary-table {
            width: 40%; /* Lebih kecil, khas laporan keuangan */
            margin-bottom: 40px;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 8px 0;
            font-size: 12px;
        }
        .summary-label { font-weight: normal; }
        .summary-value { 
            font-weight: bold; 
            text-align: right; 
            border-bottom: 1px solid #000; 
        }

        /* Table Utama Laporan */
        .main-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px;
        }
        .main-table thead th {
            border-top: 1.5px solid #000;
            border-bottom: 1.5px solid #000;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 10px 5px;
            text-align: left;
        }
        .main-table tbody td {
            padding: 8px 5px;
            font-size: 10px;
            border-bottom: 0.5px solid #ccc;
        }
        
        /* Baris Total (Garis Ganda) */
        .total-row td {
            border-top: 1.5px solid #000;
            border-bottom: 3px double #000; /* Garis ganda khas akuntansi */
            font-weight: bold;
            padding: 12px 5px;
            font-size: 11px;
        }

        /* Helper Classes */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .uppercase { text-transform: uppercase; }
        
        .no-data {
            text-align: center;
            padding: 40px;
            border: 1px dashed #000;
            font-size: 11px;
        }

        .footer {
            margin-top: 60px;
            font-size: 9px;
            width: 100%;
        }
        .signature-section {
            margin-top: 50px;
            width: 100%;
        }
        .signature-box {
            float: right;
            width: 200px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <table class="header-table">
            <tr>
                <td class="company-name">
                    PARQEER SYSTEM<br>
                    <span style="font-size: 10px; font-weight: normal;">Management Operasional Parkir Modern</span>
                </td>
                <td>
                    <div class="report-title">LAPORAN PENDAPATAN OPERASIONAL</div>
                    <div class="period-text">
                        @if($mode === 'range')
                            Periode: {{ \Carbon\Carbon::parse($from)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($to)->format('d/m/Y') }}
                        @else
                            Tanggal: {{ \Carbon\Carbon::parse($date)->format('d F Y') }}
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <h3 style="font-size: 12px; margin-bottom: 10px; text-transform: uppercase;">I. Ringkasan Eksekutif</h3>
        <table class="summary-table">
            <tr>
                <td class="summary-label">Volume Transaksi Terproses</td>
                <td class="summary-value">{{ number_format($totalCount, 0, ',', '.') }} Unit</td>
            </tr>
            <tr>
                <td class="summary-label">Total Penerimaan Kotor</td>
                <td class="summary-value">IDR {{ number_format($totalRevenue, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="summary-label">Mata Pelayanan</td>
                <td class="summary-value">Rupiah (IDR)</td>
            </tr>
        </table>

        <h3 style="font-size: 12px; margin-bottom: 10px; text-transform: uppercase;">II. Rincian Transaksi Arus Parkir</h3>
        @if($transactions->count() > 0)
            <table class="main-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 40px;">No</th>
                        <th>No. Plat</th>
                        <th>Jenis</th>
                        <th>Area Lokasi</th>
                        <th>Waktu Masuk</th>
                        <th>Waktu Keluar</th>
                        <th class="text-center">Durasi</th>
                        <th class="text-right">Jumlah (IDR)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $index => $transaction)
                        @php
                            $durationText = '-';
                            if ($transaction->duration_minutes) {
                                $hours = floor($transaction->duration_minutes / 60);
                                $minutes = $transaction->duration_minutes % 60;
                                $durationText = "{$hours}h {$minutes}m";
                            }
                        @endphp
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td style="font-family: 'Courier', monospace; font-weight: bold;">{{ strtoupper($transaction->vehicle->plate_number ?? '-') }}</td>
                            <td class="uppercase">{{ $transaction->vehicle->type ?? '-' }}</td>
                            <td>{{ $transaction->area->name ?? '-' }}</td>
                            <td>{{ $transaction->entry_time ? \Carbon\Carbon::parse($transaction->entry_time)->format('d/m/y H:i') : '-' }}</td>
                            <td>{{ $transaction->exit_time ? \Carbon\Carbon::parse($transaction->exit_time)->format('d/m/y H:i') : '-' }}</td>
                            <td class="text-center">{{ $durationText }}</td>
                            <td class="text-right">{{ number_format($transaction->amount ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="7" class="text-right uppercase">Total Akumulasi Pendapatan</td>
                        <td class="text-right">IDR {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        @else
            <div class="no-data">
                BELUM ADA DATA TRANSAKSI UNTUK PERIODE YANG DITETAPKAN.
            </div>
        @endif

        <div class="signature-section">
            <div class="signature-box">
                <p style="font-size: 10px; margin-bottom: 60px;">Bogor, {{ now()->format('d F Y') }}<br>Dicetak Oleh Sistem,</p>
                <p style="font-size: 11px; font-weight: bold; text-decoration: underline;">ADMINISTRATOR SYSTEM</p>
                <p style="font-size: 9px; color: #666;">Generated via Parqeer Intelligence</p>
            </div>
        </div>

        <div class="footer" style="clear: both; padding-top: 20px;">
            <p>Catatan: Laporan ini dihasilkan secara otomatis oleh sistem operasional Parqeer. Segala bentuk perbedaan data harus diverifikasi melalui log aktivitas pusat data.</p>
        </div>
    </div>
</body>
</html>