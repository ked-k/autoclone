<?php

namespace App\Http\Livewire\Lab\Lists;

use App\Exports\TestResultsExport;
use App\Models\Admin\Test;
use App\Models\Facility;
use App\Models\Sample;
use App\Models\SampleType;
use App\Models\Study;
use App\Models\TestResult;
use Illuminate\Support\Facades\URL;
use Livewire\Component;
use Livewire\WithPagination;

class TestsPerformedListComponent extends Component
{
    use WithPagination;

    public $facility_id = 0;

    public $study_id = 0;

    public $sampleType;

    public $test_id;

    public $from_date = '';

    public $to_date = '';

    public $perPage = 10;

    public $search = '';

    public $orderBy = 'approved_at';

    public $orderAsc = 0;

    public $export;

    public $studies;

    public $resultIds = [];

    public $combinedResultsList = [];

    protected $paginationTheme = 'bootstrap';

    public function updatedFacilityId()
    {
        if ($this->facility_id != 0) {
            $this->studies = Study::whereIn('id', auth()->user()->laboratory->associated_studies ?? [])->where('facility_id', $this->facility_id)->get();
        }
    }

    public function combinedTestResultsReport()
    {
        $resultIds = '';
        if (count($this->combinedResultsList) >= 2) {
            $sameStudyCheck = Sample::whereNotNull('study_id')->whereHas('testResult', function ($query) {
                $query->whereIn('id', array_unique($this->combinedResultsList));
            })->get()->pluck('study_id')->toArray();

            if (count(array_unique($sameStudyCheck)) == 1) {
                shuffle($this->combinedResultsList);
                $resultIds = implode('-', array_unique($this->combinedResultsList));
                $this->dispatchBrowserEvent('loadCombinedTestResultsReport', ['url' => URL::signedRoute('combined-test-results-report', ['resultIds' => $resultIds])]);
                $this->combinedResultsList = [];
            } else {
                $this->dispatchBrowserEvent('mismatch', ['type' => 'error',  'message' => 'Combined Result Report is only possible for results of the same study!']);
            }
        }
    }

    public function mount()
    {
        $this->studies = collect([]);
    }

    public function export()
    {
        if (count($this->resultIds) > 0) {
            return (new TestResultsExport($this->resultIds))->download('Tests_Performed_'.date('Y-m-d').'_'.now()->toTimeString().'.xlsx');
        } else {
            $this->dispatchBrowserEvent('not-found', ['type' => 'error',  'message' => 'Oops! No performed Tests selected for export!']);
        }
    }

    public function filterTests()
    {
        $results = TestResult::select('*')->where(['creator_lab' => auth()->user()->laboratory_id, 'status' => 'Approved'])->with(['test', 'sample', 'sample.participant', 'sample.sampleType:id,type', 'sample.study:id,name', 'sample.requester:id,name', 'sample.collector:id,name', 'sample.sampleReception'])
                    ->when($this->facility_id != 0, function ($query) {
                        $query->whereHas('sample.sampleReception', function ($query) {
                            $query->where('facility_id', $this->facility_id);
                        });
                    }, function ($query) {
                        return $query;
                    })
                    ->when($this->study_id != 0, function ($query) {
                        $query->whereHas('sample', function ($query) {
                            $query->where('study_id', $this->study_id);
                        });
                    }, function ($query) {
                        return $query;
                    })
                    ->when($this->sampleType != 0, function ($query) {
                        $query->whereHas('sample.sampleType', function ($query) {
                            $query->where('id', $this->sampleType);
                        });
                    }, function ($query) {
                        return $query;
                    })
                    ->when($this->test_id != 0, function ($query) {
                        $query->where('test_id', $this->test_id);
                    }, function ($query) {
                        return $query;
                    })
                    ->when($this->from_date != '' && $this->to_date != '', function ($query) {
                        $query->whereBetween('created_at', [$this->from_date, $this->to_date]);
                    }, function ($query) {
                        return $query;
                    });

        $this->resultIds = $results->pluck('id')->toArray();

        return $results;
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
        $facilities = Facility::whereIn('id', auth()->user()->laboratory->associated_facilities ?? [])->get();
        $sampleTypes = SampleType::where('creator_lab', auth()->user()->laboratory_id)->orderBy('type', 'asc')->get();
        $tests = Test::where('creator_lab', auth()->user()->laboratory_id)->orderBy('name', 'asc')->get();
        $testResults = $this->filterTests()->orderBy($this->orderBy, $this->orderAsc ? 'asc' : 'desc')
        ->paginate($this->perPage);

        return view('livewire.lab.lists.tests-performed-list-component', compact('testResults', 'facilities', 'sampleTypes', 'tests'));
    }
}
