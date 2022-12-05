<?php

namespace App\Exports;

use App\Models\Platform;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PlatformsExport implements FromCollection, WithMapping, WithHeadings,WithStyles
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
        return Platform::where('creator_lab', auth()->user()->laboratory_id)->latest()->get();
    }

    public function map($platform): array
    {
        $this->count++;

        return [
            $this->count,
            $platform->name,
            $platform->range ?? 'N/A',
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'Platform',
            'Range',
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
