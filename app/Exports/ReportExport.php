<?php

namespace App\Exports;

use App\Models\TestResult;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class ReportExport implements FromView
{
    use Exportable;

    /**
     * @return \Illuminate\Support\Collection
     */
    public $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function view(): View
    {
        return view('reports.sample-management.test-report', [
            'testResult' => TestResult::with(['test', 'sample', 'sample.participant', 'sample.sampleReception', 'sample.sampleType:id,type', 'sample.study:id,name', 'sample.requester', 'sample.collector:id,name'])->where('id', $this->id)->first(),
        ]);
    }
}
