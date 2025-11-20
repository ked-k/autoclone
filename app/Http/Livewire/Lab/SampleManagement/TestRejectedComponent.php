<?php
namespace App\Http\Livewire\Lab\SampleManagement;

use App\Exports\TestPerformedExport;
use App\Models\Admin\Test;
use App\Models\Facility;
use App\Models\SampleType;
use App\Models\Study;
use App\Models\TestResult;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class TestRejectedComponent extends Component
{
    use WithPagination;

    public $perPage = 10;

    public $search = '';

    public $orderBy = 'reviewed_at';

    public $orderAsc = true;

    public $viewReport = false;

    public $testResult;

    public $approver_comment;

    public $resultIds;
    public $facility_id = 0;

    public $study_id = 0;

    public $sampleType;

    public $test_id;

    public $performed_by = 0;

    public $reviewed_by = 0;

    public $approved_by = 0;

    public $from_date = '';

    public $to_date = '';
    public $status;

    protected $paginationTheme = 'bootstrap';

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

    public function markAsApproved(TestResult $testResult)
    {
        $this->validate([
            'approver_comment' => 'required',
        ]);
        $testResult->approved_by      = Auth::id();
        $testResult->approved_at      = now();
        $testResult->status           = 'Approved';
        $testResult->approver_comment = $this->approver_comment;
        $testResult->update();
        $this->emit('updateNav', 'testsPendindApprovalCount');

        $this->reset('approver_comment');
        $this->viewReport = false;
        $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => 'Test Result Updated successfully!']);
    }

    public function markAsDeclined(TestResult $testResult)
    {
        $this->validate([
            'approver_comment' => 'required',
        ]);

        $testResult->approved_by      = Auth::id();
        $testResult->approved_at      = now();
        $testResult->approver_comment = $this->approver_comment;
        $testResult->status           = 'Rejected';
        $testResult->update();
        $this->emit('updateNav', 'testsPendindApprovalCount');

        $this->reset('approver_comment');
        $this->viewReport = false;
        $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => 'Test Result Updated successfully.!']);
    }

    public function viewPreliminaryReport(TestResult $testResult)
    {
        $this->testResult = $testResult;
        $this->viewReport = true;
    }

    public function refresh()
    {
        return redirect(request()->header('Referer'));
    }
    public function filterTests()
    {
        $results = TestResult::resultSearch($this->search, 'Rejected')
            ->where('creator_lab', auth()->user()->laboratory_id)->with(['test', 'sample', 'sample.participant', 'sample.sampleReception', 'sample.sampleType:id,type', 'sample.study:id,name', 'sample.requester', 'sample.collector:id,name'])->where('status', 'Rejected')
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
            ->when($this->status != 0, function ($query) {
                $query->where('status', $this->status);
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

    public function render()
    {
        if ($this->viewReport) {
            $data['testResults'] = $this->testResult->load(['test', 'sample', 'sample.participant', 'sample.sampleReception', 'sample.sampleType:id,type', 'sample.study:id,name', 'sample.requester', 'sample.collector:id,name']);
        } else {
            $data['testResults'] = $this->filterTests()
                ->orderBy($this->orderBy, $this->orderAsc ? 'asc' : 'desc')
                ->paginate($this->perPage);
        }
        $data['users']       = User::where(['is_active' => 1, 'laboratory_id' => auth()->user()->laboratory_id])->latest()->get();
        $data['facilities']  = Facility::whereIn('id', auth()->user()->laboratory->associated_facilities ?? [])->get();
        $data['sampleTypes'] = SampleType::where('creator_lab', auth()->user()->laboratory_id)->orderBy('type', 'asc')->get();
        $data['tests']       = Test::where('creator_lab', auth()->user()->laboratory_id)->orderBy('name', 'asc')->get();
        $data['studies']     = Study::whereIn('id', auth()->user()->laboratory->associated_studies ?? [])->where('facility_id', $this->facility_id)->get();

        return view('livewire.lab.sample-management.test-approval-component', $data);
    }
}
