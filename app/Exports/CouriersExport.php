<?php

namespace App\Exports;

use App\Models\Courier;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CouriersExport implements FromCollection, WithMapping, WithHeadings,WithStyles
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
        return Courier::whereIn('facility_id', auth()->user()->laboratory->associated_facilities)->with('facility')->latest()->get();
    }

    public function map($courier): array
    {
        $this->count++;

        return [
            $this->count,
            $courier->name,
            $courier->facility->name,
            $courier->contact,
            $courier->email,
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'Name',
            'Facility',
            'Contact',
            'Email',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
        ];
    }
}
