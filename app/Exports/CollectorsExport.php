<?php

namespace App\Exports;

use App\Models\Collector;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CollectorsExport implements FromCollection, WithMapping, WithHeadings
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
        return Collector::whereIn('facility_id', auth()->user()->laboratory->associated_facilities)->with('facility', 'study')->latest()->get();
    }

    public function map($collector): array
    {
        $this->count++;

        return [
            $this->count,
            $collector->name,
            $collector->facility->name,
            $collector->study->name ?? 'N/A',
            $collector->contact,
            $collector->email,
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'Name',
            'Facility',
            'Study',
            'Contact',
            'Email',
        ];
    }
}
