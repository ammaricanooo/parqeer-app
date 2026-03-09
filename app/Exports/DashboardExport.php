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

        // hitung total pendapatan
        $this->totalAmount = $transactions->sum('amount');
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->transactions;
    }

    /**
     * @param  mixed $transaction
     * @return array
     */
    public function map($transaction): array
    {
        return [
            $transaction->vehicle->plate_number ?? '-',
            ucfirst($transaction->vehicle->type ?? '-'),
            $transaction->vehicle->color ?? '-',
            $transaction->area->name ?? '-',
            $transaction->entry_time
                ? \Carbon\Carbon::parse($transaction->entry_time)->format('Y-m-d H:i:s')
                : '-',
            $transaction->exit_time
                ? \Carbon\Carbon::parse($transaction->exit_time)->format('Y-m-d H:i:s')
                : '-',
            $transaction->duration_minutes
                ? round($transaction->duration_minutes / 60, 2) . ' jam'
                : '-',
            'Rp ' . number_format($transaction->amount ?? 0, 0, ',', '.'),
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Plat Nomor',
            'Tipe Kendaraan',
            'Warna',
            'Area Parkir',
            'Waktu Masuk',
            'Waktu Keluar',
            'Durasi',
            'Tarif Parkir',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                // ================= HEADER STYLE =================
                $sheet->getStyle('A1:H1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '107C41'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // ================= TOTAL ROW =================
                $lastRow = $sheet->getHighestRow() + 1;

                // Merge label
                $sheet->mergeCells("A{$lastRow}:G{$lastRow}");
                $sheet->setCellValue("A{$lastRow}", 'TOTAL PENDAPATAN');
                $sheet->setCellValue(
                    "H{$lastRow}",
                    'Rp ' . number_format($this->totalAmount, 0, ',', '.')
                );

                // Style total row
                $sheet->getStyle("A{$lastRow}:H{$lastRow}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E5E7EB'], // abu-abu lembut
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_RIGHT,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Align label kiri
                $sheet->getStyle("A{$lastRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);
            },
        ];
    }
}
