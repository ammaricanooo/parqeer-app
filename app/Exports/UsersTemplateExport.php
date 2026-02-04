<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class UsersTemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function array(): array
    {
        // contoh kosong (tidak perlu data), hanya untuk memberikan header/layout
        return [
            // you can include a sample row if desired, e.g. ['','Nama Contoh','username','attendant','active']
            // leaving empty array means only headings will be present
        ];
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Username',
            'Role',
            'Status',
        ];
    }
}
