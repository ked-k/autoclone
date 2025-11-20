<?php

namespace App\Exports;

use App\Models\Freezer;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FreezersExport implements FromCollection, WithMapping, WithHeadings, WithStyles
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
        return Freezer::with('location')->get();
    }

    public function map($freezer): array
    {
        $this->count++;

        return [
            $this->count,
            $freezer->name,
            $freezer->location->name ?? 'N/A',
            $freezer->type ?? 'N/A',
            $freezer->temp ?? 'N/A',
            $freezer->description ?? 'N/A',
            $freezer->is_active == 1 ? 'Active' : 'Suspended',
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'Name',
            'Location',
            'Type',
            'Temp',
            'Description',
            'Status',
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
