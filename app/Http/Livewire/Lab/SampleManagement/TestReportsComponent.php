<?php

namespace App\Http\Livewire\Lab\SampleManagement;

use App\Models\Sample;
use App\Models\TestResult;
use Illuminate\Support\Facades\URL;
use Livewire\Component;
use Livewire\WithPagination;

class TestReportsComponent extends Component
{
    use WithPagination;

    public $perPage = 10;

    public $search = '';

    public $orderBy = 'approved_at';

    public $orderAsc = true;

    public $combinedList = [];

    protected $paginationTheme = 'bootstrap';

    public function combinedTestReport()
    {
        $sampleIds = '';
        if (count($this->combinedList) >= 2) {
            $sameStudyCheck = Sample::whereIn('id', array_unique($this->combinedList))->get()->pluck('study_id')->toArray();

            if (count(array_unique($sameStudyCheck)) == 1) {
                $sampleIds = implode('-', array_unique($this->combinedList));
                $this->dispatchBrowserEvent('loadCombinedReport', ['url' => URL::signedRoute('combined-test-report', ['sampleIds' => $sampleIds])]);
                $this->combinedList = [];
            } else {
                $this->dispatchBrowserEvent('mismatch', ['type' => 'error',  'message' => 'Combined Test Report is only possible for samples of the same study!']);
            }
        }
    }

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
