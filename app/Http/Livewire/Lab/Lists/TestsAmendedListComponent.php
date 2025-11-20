<?php

namespace App\Http\Livewire\Lab\Lists;

use App\Exports\TestPerformedExport;
use App\Models\Admin\Test;
use App\Models\Facility;
use App\Models\Lab\SampleManagement\TestResultAmendment;
use App\Models\Sample;
use App\Models\SampleType;
use App\Models\Study;
use App\Models\TestResult;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Livewire\Component;
use Livewire\WithPagination;

class TestsAmendedListComponent extends Component
{
    use WithPagination;

    public $facility_id = 0;

    public $study_id = 0;

    public $sampleType;

    public $test_id;

    public $amended_by = 0;

    public $reviewed_by = 0;

    public $approved_by = 0;

    public $from_date = '';

    public $to_date = '';

    public $perPage = 10;

    public $search = '';

    public $orderBy = 'approved_at';

    public $orderAsc = 0;

    public $export;

    public $studies;

    public $status;
    public $result_tracker;

    public $amendedResults;

    public $resultIds = [];

    public $combinedResultsList = [];

    protected $paginationTheme = 'bootstrap';

    public $type;

    public function mount($type)
    {
        $this->type = $type;
        if ($type == 'all' && Auth::user()->hasPermission(['review-results'])) {
        } elseif ($type == 'my' && Auth::user()->hasPermission(['enter-results'])) {
            $this->amended_by = auth()->user()->id;
        } else {
            abort(403, 'Unauthorized access or action.');
        }
        $this->studies = collect([]);
        $this->amendedResults = collect([]);
    }

    public function export()
    {
        if (count($this->resultIds) > 0) {
            return (new TestPerformedExport($this->resultIds))->download('Tests_Performed_' . date('Y-m-d') . '_' . now()->toTimeString() . '.xlsx');
        } else {
            $this->dispatchBrowserEvent('not-found', ['type' => 'error', 'message' => 'Oops! No performed Tests selected for export!']);
        }
    }

    public function updatedFacilityId()
    {
        if ($this->facility_id != 0) {
            $this->studies = Study::whereIn('id', auth()->user()->laboratory->associated_studies ?? [])->where('facility_id', $this->facility_id)->get();
        }
    }

    public function viewAmended($id)
    {
        $this->amendedResults = TestResultAmendment::where('test_result_id', $id)->with('amendedBy', 'testResult')->get();
        // dd($this->amendedResults);
    }

    public function amendResult()
    {

        $this->validate(['result_tracker' => 'required|numeric']);
        return to_route('result-amendment', $this->result_tracker);
    }

    public function close()
    {
        $this->amendedResults = collect([]);
    }

    public function combinedTestResultsReport()
    {
        $resultIds = '';
        if (count($this->combinedResultsList) >= 2) {

            $testResults = TestResult::whereIn('id', array_unique($this->combinedResultsList))->get();
            $sampleIds = $testResults->pluck('sample_id')->toArray();
            $samples = Sample::where('id', array_unique($sampleIds))->get();

            $sameStudyCheck = $samples->pluck('study_id')->toArray();
            $sameSampleTypeCheck = $samples->pluck('sample_type_id')->toArray();
            $sameTestCheck = $testResults->pluck('test_id')->toArray();
            $samePerformerCheck = $testResults->pluck('performed_by')->toArray();

            if (count(array_unique($sameStudyCheck)) == 1 && count(array_unique($sameSampleTypeCheck)) == 1 && count(array_unique($sameTestCheck)) == 1 && count(array_unique($samePerformerCheck)) == 1) {

                shuffle($this->combinedResultsList);
                $resultIds = implode('-', array_unique($this->combinedResultsList));
                $this->dispatchBrowserEvent('loadCombinedTestResultsReport', ['url' => URL::signedRoute('combo-report', ['resultIds' => $resultIds])]);
                $this->combinedResultsList = [];

            } else {
                $this->dispatchBrowserEvent('mismatch', ['type' => 'error', 'message' => 'Combined Result Report is only possible for results of the same study, sample, test, and performer!']);
            }
        }
    }

    public function filterTests()
    {
        $results = TestResult::resultSearch($this->search, 'Approved')->select('*')->where(['creator_lab' => auth()->user()->laboratory_id, 'amended_state' => 1])->with(['amendedBy', 'test', 'sample', 'sample.participant', 'sample.sampleType:id,type', 'sample.study:id,name', 'sample.requester:id,name', 'sample.collector:id,name', 'sample.sampleReception'])
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
            ->when($this->amended_by != 0, function ($query) {
                $query->where('amended_by', $this->amended_by);
            }, function ($query) {
                return $query;
            })
            ->when($this->reviewed_by != 0, function ($query) {
                $query->where('reviewed_by', $this->reviewed_by);
            }, function ($query) {
                return $query;
            })
            ->when($this->status != 0, function ($query) {
                $query->where('status', $this->status);
            }, function ($query) {
                return $query;
            })
            ->when($this->approved_by != 0, function ($query) {
                $query->where('approved_by', $this->approved_by);
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
        $data['users'] = User::where(['is_active' => 1, 'laboratory_id' => auth()->user()->laboratory_id])->when($this->type == 'my', function ($query) {
            $query->where('id', $this->amended_by);
        })->latest()->get();
        $data['facilities'] = Facility::whereIn('id', auth()->user()->laboratory->associated_facilities ?? [])->get();
        $data['sampleTypes'] = SampleType::where('creator_lab', auth()->user()->laboratory_id)->orderBy('type', 'asc')->get();
        $data['tests'] = Test::where('creator_lab', auth()->user()->laboratory_id)->orderBy('name', 'asc')->get();
        $data['testResults'] = $this->filterTests()->orderBy($this->orderBy, $this->orderAsc ? 'desc' : 'asc')
            ->paginate($this->perPage);
        return view('livewire.lab.lists.tests-amended-list-component', $data);
    }
}
