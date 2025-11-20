<?php

namespace App\Exports;

use App\Models\Admin\Test;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TestsExport implements FromCollection, WithMapping, WithHeadings, WithStyles
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
        return Test::with('category')->where('creator_lab', auth()->user()->laboratory_id)->latest()->get();
    }

    public function map($test): array
    {
        $this->count++;

        return [
            $this->count,

            $test->name,
            $test->category->category_name,
            $test->short_code,
            $test->price ?? 'N/A',
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'Name',
            'Category',
            'Short Code',
            'Price',
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
