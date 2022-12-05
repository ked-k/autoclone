<?php

namespace App\Exports;

use App\Models\SampleType;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SampleTypesExport implements FromCollection, WithMapping, WithHeadings
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
        return SampleType::all();
    }

    public function map($sampleType): array
    {
        $this->count++;

        return [
            $this->count,
            $sampleType->type,
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'Sample Type',
        ];
    }
}
