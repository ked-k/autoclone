<?php

namespace App\Exports;

use App\Models\Requester;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RequestersExport implements FromCollection, WithMapping, WithHeadings
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
        return Requester::whereIn('study_id', auth()->user()->laboratory->associated_studies)->with('facility', 'study')->latest()->get();
    }

    public function map($requester): array
    {
        $this->count++;

        return [
            $this->count,
            $requester->name,
            $requester->facility->name,
            $requester->study->name,
            $requester->contact,
            $requester->email,
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
