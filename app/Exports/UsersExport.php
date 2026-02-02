<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class UsersExport implements FromCollection, WithEvents, WithMapping, WithHeadings, ShouldAutoSize, WithDrawings
{
    protected $user;
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return User::all();
    }

    /**
     * @param  mixed $user
     * @return array
     */
    public function map($user): array
    {
        $this->user = $user;
        return [
            null,
            $user->name,
            $user->username,
            $user->role,
            $user->status,
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Foto',
            'Nama',
            'Username',
            'Role',
            'Status',
        ];
    }
    public function drawings()
    {
        $drawings = [];
        $row = 2; // Mulai dari baris kedua

        foreach ($this->collection() as $user) {
            if ($user->photo) {
                $drawing = new Drawing();
                $drawing->setName('Foto ' . $user->name);
                $drawing->setDescription('Foto ' . $user->name);
                $drawing->setPath(public_path('storage/users/' . $user->photo)); // Path ke Foto
                $drawing->setHeight(50); // Atur tinggi Foto
                $drawing->setCoordinates('A' . $row); // Atur koordinat sel untuk Foto
                $drawings[] = $drawing;
            }
            $row++;
        }

        return $drawings;
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                // Tentukan range heading (A1 sampai E1)
                $cellRange = 'A1:E1';

                // Style heading
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'], // Putih
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => '107C41', // Hijau (sesuai theme kamu)
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Atur tinggi baris data
                $highestRow = $event->sheet->getDelegate()->getHighestRow();
                for ($row = 2; $row <= $highestRow; $row++) {
                    $event->sheet->getDelegate()
                        ->getRowDimension($row)
                        ->setRowHeight(50);
                }
            },
        ];
    }
}
