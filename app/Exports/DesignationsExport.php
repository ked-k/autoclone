<?php

namespace App\Exports;

use App\Models\Designation;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DesignationsExport implements FromCollection, WithMapping, WithHeadings
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
        return Designation::all();
    }

    public function map($designation): array
    {
        $this->count++;

        return [
            $this->count,
            $designation->name,
            $designation->description ?? 'N/A',
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'Designation Name',
            'Description',
        ];
    }
}
