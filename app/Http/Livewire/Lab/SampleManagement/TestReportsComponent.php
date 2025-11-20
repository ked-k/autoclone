<?php
namespace App\Http\Livewire\Lab\SampleManagement;

use App\Exports\TestPerformedExport;
use App\Models\Admin\Test;
use App\Models\Facility;
use App\Models\Lab\SampleManagement\TestResultAmendment;
use App\Models\Sample;
use App\Models\SampleType;
use App\Models\Study;
use App\Models\TestResult;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Livewire\Component;
use Livewire\WithPagination;

class TestReportsComponent extends Component
{
    use WithPagination;
    public $facility_id = 0;

    public $study_id = 0;

    public $sampleType;

    public $test_id;

    public $performed_by = 0;

    public $reviewed_by = 0;

    public $approved_by = 0;

    public $from_date = '';

    public $to_date = '';

    public $perPage = 50;

    public $search = '';

    public $orderBy = 'approved_at';

    public $orderAsc = true;

    public $combinedSamplesList = [];

    public $status     = 'Approved';
    public $downloaded = null;
    public $amendedResults;
    protected $paginationTheme  = 'bootstrap';
    public $combinedResultsList = [];
    public $studies;
    public $resultIds = [];

    public function mount()
    {
        $this->amendedResults = collect([]);
        $this->studies        = collect([]);
    }

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
                $this->dispatchBrowserEvent('mismatch', ['type' => 'error', 'message' => 'Combined Test Report is only possible for samples of the same study!']);
            }
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

    }

    public function close()
    {
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
    public function filterTests()
    {
        $results = TestResult::resultSearch($this->search, $this->status)
            ->when($this->downloaded == 1, function ($query) {
                $query->where('download_count', '<', 1);
            })
            ->when($this->downloaded > 1 && $this->downloaded <= 3, function ($query) {
                $query->where('download_count', $this->downloaded);
            })
            ->when($this->downloaded > 3, function ($query) {
                $query->where('download_count', '>', 3);
            })
            ->where('status', $this->status)
            ->when($this->facility_id != 0, function ($query) {
                $query->whereHas('sample.sampleReception', function ($query) {
                    $query->where('facility_id', $this->facility_id);
                });
            })
            ->when($this->study_id != 0, function ($query) {
                $query->whereHas('sample', function ($query) {
                    $query->where('study_id', $this->study_id);
                });
            })
            ->when($this->sampleType != 0, function ($query) {
                $query->whereHas('sample.sampleType', function ($query) {
                    $query->where('id', $this->sampleType);
                });
            })
            ->when($this->test_id != 0, function ($query) {
                $query->where('test_id', $this->test_id);
            })
            ->when($this->performed_by != 0, function ($query) {
                $query->where('performed_by', $this->performed_by);
            })
            ->when($this->reviewed_by != 0, function ($query) {
                $query->where('reviewed_by', $this->reviewed_by);
            })
            ->when($this->approved_by != 0, function ($query) {
                $query->where('approved_by', $this->approved_by);
            })
            ->when($this->from_date != '' && $this->to_date != '', function ($query) {
                $query->whereBetween('created_at', [$this->from_date, $this->to_date]);
            });

        $this->resultIds = $results->pluck('id')->toArray();

        return $results;
    }
    public function printMultiple()
    {
        if (count($this->combinedResultsList) >= 2) {
            // Store the IDs in the session
            session(['combinedResultsList' => $this->combinedResultsList]);
            return to_route('print-result-multi', ['session_id' => session()->getId()]);
        } elseif (count($this->resultIds) > 0) {
            session(['combinedResultsList' => $this->resultIds]);
            return to_route('print-result-multi', ['session_id' => session()->getId()]);
        }
    }
    public function render()
    {
        $data['users']       = User::where(['is_active' => 1, 'laboratory_id' => auth()->user()->laboratory_id])->latest()->get();
        $data['facilities']  = Facility::whereIn('id', auth()->user()->laboratory->associated_facilities ?? [])->get();
        $data['sampleTypes'] = SampleType::where('creator_lab', auth()->user()->laboratory_id)->orderBy('type', 'asc')->get();
        $data['tests']       = Test::where('creator_lab', auth()->user()->laboratory_id)->orderBy('name', 'asc')->get();
        $data['testResults'] = $this->filterTests()
            ->where('creator_lab', auth()->user()->laboratory_id)
            ->with(['test', 'sample', 'sample.participant', 'sample.sampleType:id,type', 'sample.study:id,name', 'sample.requester:id,name', 'sample.collector:id,name', 'sample.sampleReception'])
            ->orderBy($this->orderBy, $this->orderAsc ? 'desc' : 'asc')
            ->paginate($this->perPage);

        return view('livewire.lab.sample-management.test-reports-component', $data);
    }
}
