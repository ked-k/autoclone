<?php

namespace App\Http\Livewire\Lab\SampleManagement;

use App\Models\Sample;
use Livewire\Component;
use App\Models\TestResult;
use Livewire\WithPagination;
use App\Exports\ReportExport;
use Illuminate\Support\Facades\URL;

class TestReportsComponent extends Component
{
    use WithPagination;

    public $perPage = 10;

    public $search = '';

    public $orderBy = 'approved_at';

    public $orderAsc = true;

    public $combinedSamplesList = [];

    protected $paginationTheme = 'bootstrap';

    public function combinedTestReport()
    {
        $sampleIds = '';
        if (count($this->combinedSamplesList) >= 1) {
            $sameStudyCheck = Sample::whereIn('id', array_unique($this->combinedSamplesList))->get()->pluck('study_id')->toArray();

            if (count(array_unique($sameStudyCheck)) == 1) {
                shuffle($this->combinedSamplesList);
                $sampleIds = implode('-', array_unique($this->combinedSamplesList));
                $this->dispatchBrowserEvent('loadCombinedSampleTestReport', ['url' => URL::signedRoute('combined-sample-test-report', ['sampleIds' => $sampleIds])]);
                $this->combinedSamplesList = [];
            } else {
                $this->dispatchBrowserEvent('mismatch', ['type' => 'error',  'message' => 'Combined Test Report is only possible for samples of the same study!']);
            }
        }
    }

    // public function export($id)
    // {
    //     return (new ReportExport($id))->download('report_' . date('Y-m-d') . '_' . now()->toTimeString() . '.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
    // }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function incrementDownloadCount(TestResult $testResult)
    {
        if ($testResult->status == 'Approved') {
            $testResult->increment('download_count', 1);
        }
    }

    public function refresh()
    {
        return redirect(request()->header('Referer'));
    }

    public function render()
    {
        $testResults = TestResult::resultSearch($this->search, 'Approved')
            ->where('status', 'Approved')
            ->where('creator_lab', auth()->user()->laboratory_id)
            ->with(['test', 'sample', 'sample.participant', 'sample.sampleType:id,type', 'sample.study:id,name', 'sample.requester:id,name', 'sample.collector:id,name', 'sample.sampleReception'])
            ->orderBy($this->orderBy, $this->orderAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);

        return view('livewire.lab.sample-management.test-reports-component', compact('testResults'));
    }
}
