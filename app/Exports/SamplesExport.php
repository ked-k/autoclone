<?php

namespace App\Exports;

use App\Models\Sample;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SamplesExport implements FromCollection, WithMapping, WithHeadings
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

    public function collection()
    {
        return Sample::whereIn('id', $this->sampleIds)->with(['participant', 'participant.facility', 'sampleType:id,type', 'study:id,name', 'sampleReception'])->latest()->get();
    }

    public function map($sample): array
    {
        $this->count++;

        return [
            $this->count,
            $sample->sampleReception->batch_no,
            $sample->participant->identity,
            $sample->sampleType->type,
            $sample->sample_identity ?? 'N/A',
            $sample->lab_no ?? 'N/A',
            $sample->participant->facility->name ?? 'N/A',
            $sample->study->name ?? 'N/A',
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'Batch No',
            'Participant ID',
            'Sample Type',
            'Sample ID',
            'Lab No',
            'Facility',
            'Study',
        ];
    }
}
