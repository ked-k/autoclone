<?php

namespace App\Exports;

use App\Models\Kit;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class KitsExport implements FromCollection, WithMapping, WithHeadings
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
        return Kit::where('creator_lab', auth()->user()->laboratory_id)->with('platform')->latest()->get();
    }

    public function map($kit): array
    {
        $this->count++;

        return [
            $this->count,
            $kit->name,
            $kit->platform->name ?? 'N/A',
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'Name',
            'Platform',
        ];
    }
}
