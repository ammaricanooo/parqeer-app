<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Collection;

class DashboardExport implements
    FromCollection,
    WithEvents,
    WithMapping,
    WithHeadings,
    ShouldAutoSize
{
    protected $transactions;
    protected $mode;
    protected $date;
    protected $from;
    protected $to;
    protected $totalAmount;

    public function __construct($transactions, $mode, $date, $from, $to)
    {
        $this->transactions = $transactions;
        $this->mode = $mode;
        $this->date = $date;
        $this->from = $from;
        $this->to = $to;
        $this->totalAmount = $transactions->sum('amount');
    }

    public function collection()
    {
        return $this->transactions;
    }

    public function map($transaction): array
    {
        $durationText = '-';
        if ($transaction->duration_minutes) {
            $hours = floor($transaction->duration_minutes / 60);
            $minutes = $transaction->duration_minutes % 60;
            $durationText = $hours > 0 ? "{$hours}j {$minutes}m" : "{$minutes}m";
        }

        return [
            strtoupper($transaction->vehicle->plate_number ?? '-'),
            strtoupper($transaction->vehicle->type ?? '-'),
            $transaction->area->name ?? '-',
            $transaction->entry_time ? \Carbon\Carbon::parse($transaction->entry_time)->format('d/m/Y H:i') : '-',
            $transaction->exit_time ? \Carbon\Carbon::parse($transaction->exit_time)->format('d/m/Y H:i') : '-',
            $durationText,
            // Kembalikan angka murni tanpa "Rp" agar Excel mengenalnya sebagai Number
            $transaction->amount ?? 0,
        ];
    }

    public function headings(): array
    {
        return [
            'NO. PLAT',
            'JENIS',
            'AREA LOKASI',
            'WAKTU MASUK',
            'WAKTU KELUAR',
            'DURASI',
            'NILAI TARIF (IDR)',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $fullRange = "A1:G{$lastRow}";

                // 1. STYLE SEMUA SEL (Font & Alignment)
                $sheet->getStyle($fullRange)->applyFromArray([
                    'font' => ['name' => 'Arial', 'size' => 10],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // 2. HEADER STYLE (Hitam Putih / Monochrome)
                $sheet->getStyle('A1:G1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '262626'], // Hitam Abu-abu (Elegant)
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                // 3. FORMAT KOLOM MATA UANG (Accounting Format)
                // Ini membuat angka tetap bisa di-SUM di Excel tapi terlihat ada Rp-nya
                $sheet->getStyle("G2:G{$lastRow}")
                    ->getNumberFormat()
                    ->setFormatCode('_("Rp"* #,##0_);_("Rp"* (#,##0);_("Rp"* "-"??_);_(@_)');

                // 4. TOTAL ROW STYLE
                $totalRow = $lastRow + 1;
                $sheet->mergeCells("A{$totalRow}:F{$totalRow}");
                $sheet->setCellValue("A{$totalRow}", 'TOTAL AKUMULASI PENDAPATAN');
                $sheet->setCellValue("G{$totalRow}", $this->totalAmount);

                $sheet->getStyle("A{$totalRow}:G{$totalRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F2F2F2'],
                    ],
                    'borders' => [
                        'top' => ['borderStyle' => Border::BORDER_THIN],
                        'bottom' => ['borderStyle' => Border::BORDER_DOUBLE], // Garis dua khas akuntansi
                    ],
                ]);

                $sheet->getStyle("G{$totalRow}")
                    ->getNumberFormat()
                    ->setFormatCode('_("Rp"* #,##0_);_("Rp"* (#,##0);_("Rp"* "-"??_);_(@_)');

                $sheet->getStyle("A{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // 5. BORDER UNTUK SELURUH TABEL
                $sheet->getStyle("A1:G{$totalRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }
}
