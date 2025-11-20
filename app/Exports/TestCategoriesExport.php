<?php

namespace App\Exports;

use App\Models\TestCategory;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TestCategoriesExport implements FromCollection, WithMapping, WithHeadings, WithStyles
{
    use Exportable;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function __construct()
    {
        $this->count = 0;
    }

    public function collection()
    {
        return TestCategory::where('creator_lab', auth()->user()->laboratory_id)->latest()->get();
    }

    public function map($category): array
    {
        $this->count++;

        return [
            $this->count,
            $category->category_name,
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'Category',
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
