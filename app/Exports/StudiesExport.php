<?php

namespace App\Exports;

use App\Models\Study;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudiesExport implements FromCollection, WithMapping, WithHeadings
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
        return Study::with('facility')->whereIn('facility_id', auth()->user()->laboratory->associated_facilities)->where('is_active', 1)->latest()->get();
    }

    public function map($study): array
    {
        $this->count++;

        return [
            $this->count,
            $study->name,
            $study->facility->name,
            $study->description ?? 'N/A',
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'Study/Project',
            'Facility',
            'Description',
        ];
    }
}
