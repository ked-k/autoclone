<?php

namespace App\Exports;

use App\Models\TestResult;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithProperties;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TestResultsExport implements FromCollection, WithMapping, WithHeadings,WithStyles,WithProperties
{
    use Exportable;

    /**
     * @return \Illuminate\Support\Collection
     */
    public $count;

    public $resultIds;

    public function __construct($resultIds)
    {
        $this->count = 0;
        $this->resultIds = $resultIds;
    }

    public function properties(): array
    {
        return [
            'creator'        => auth()->user()->fullName,
            'lastModifiedBy' => 'Autolab',
            'title'          => 'Tests',
            'description'    => 'Tests Performed export',
            'subject'        => 'Tests Performed export',
            'keywords'       => 'Autolab exports',
            'category'       => 'Autolab Exports',
            'manager'        => 'MakBRC IT TEAM',
            'company'        => 'Makerere University Biomedical Research Centre',
        ];
    }

    public function collection()
    {
        return TestResult::whereIn('id', $this->resultIds)->where(['creator_lab' => auth()->user()->laboratory_id, 'status' => 'Approved'])->with(['test', 'sample', 'sample.participant', 'sample.sampleType:id,type', 'sample.study:id,name', 'sample.requester:id,name', 'sample.collector:id,name', 'sample.sampleReception', 'sample.sampleReception.facility'])->latest()->get();
    }

    public function map($result): array
    {
        $this->count++;

        return [
            $this->count,
            $result->sample->sampleReception->batch_no,
            $result->tracker,
            $result->sample->sampleReception->facility->name,
            $result->sample->study->name ?? 'N/A',
            $result->sample->participant->identity ?? 'N/A',
            $result->sample->sampleType->type ?? 'N/A',
            $result->sample->collector->name ?? 'N/A',
            $result->sample->sample_identity ?? 'N/A',
            $result->sample->lab_no ?? 'N/A',
            $result->test->name ?? 'N/A',
            $result->sample->requester->name ?? 'N/A',
            date('d-m-Y H:i', strtotime($result->approved_at)) ?? 'N/A',
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'Batch No',
            'Test Tracker',
            'Facility',
            'Study',
            'Participant ID',
            'Sample Type',
            'Phlebotomist',
            'Sample ID',
            'Lab No',
            'Test Performed',
            'Requested By',
            'Result Date',
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
