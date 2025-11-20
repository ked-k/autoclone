<?php

namespace App\Exports;

use App\Models\Laboratory;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaboratoriesExport implements FromCollection, WithMapping, WithHeadings, WithStyles
{
    use Exportable;

    /**
     * @return \Illuminate\Support\Collection
     */
    public $count;

    public function __construct()
    {
        $this->count = 0;
    }

    public function collection()
    {
        return Laboratory::latest()->get();
    }

    public function map($laboratory): array
    {
        $this->count++;

        return [
            $this->count,
            $laboratory->laboratory_name,
            $laboratory->short_code ?? 'N/A',
            $laboratory->description ?? 'N/A',
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'Name',
            'Short Code',
            'Description',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],
        ];
    }
}
