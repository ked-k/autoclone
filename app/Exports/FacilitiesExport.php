<?php

namespace App\Exports;

use App\Models\Facility;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FacilitiesExport implements FromCollection, WithMapping, WithHeadings, WithStyles
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
        return Facility::with('parent')->latest()->get();
    }

    public function map($facility): array
    {
        $this->count++;

        return [
            $this->count,
            $facility->name,
            $facility->type,
            $facility->parent->name ?? 'N/A',
            $facility->is_active === 1 ? 'Active' : 'Suspended',
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'Name',
            'Type',
            'Parent Facility',
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
