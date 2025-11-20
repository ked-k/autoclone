<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Sample;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithProperties;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SamplesExport implements FromCollection, WithMapping, WithHeadings, WithStyles, WithProperties
{
    use Exportable;

    /**
     * @return \Illuminate\Support\Collection
     */
    public $count;

    public $sampleIds;

    public function __construct($sampleIds)
    {
        $this->count = 0;
        $this->sampleIds = $sampleIds;
    }

    public function properties(): array
    {
        return [
            'creator' => auth()->user()->fullName,
            'lastModifiedBy' => 'Autolab',
            'title' => 'Samples',
            'description' => 'Samples export',
            'subject' => 'Samples Export',
            'keywords' => 'Autolab exports',
            'category' => 'Autolab Exports',
            'manager' => 'MakBRC IT TEAM',
            'company' => 'Makerere University Biomedical Research Centre',
        ];
    }

    public function collection()
    {
        return Sample::whereIn('id', $this->sampleIds)->with(['participant', 'participant.facility', 'sampleType:id,type', 'study:id,name', 'sampleReception'])->latest('sample_is_for')->get();
    }

    public function map($sample): array
    {
        $this->count++;

        return [
            $this->count,
            Carbon::parse($sample?->sampleReception?->date_delivered)->format('d-m-Y'),
            $sample?->sampleReception?->batch_no?? 'N/A',
            $sample?->participant?->identity?? 'N/A',
            $sample->sampleType?->type?? 'N/A',
            $sample?->sample_identity ?? 'N/A',
            $sample->lab_no ?? 'N/A',
            $sample?->participant?->facility?->name ?? 'N/A',
            $sample?->study?->name ?? 'N/A',
            $sample?->sample_is_for ?? 'N/A',
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'Date Delivered',
            'Batch No',
            'PID',
            'Sample Type',
            'Sample ID',
            'Lab No',
            'Facility',
            'Study',
            'Sample Was For',
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
