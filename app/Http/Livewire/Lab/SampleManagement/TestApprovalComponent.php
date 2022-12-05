<?php

namespace App\Http\Livewire\Lab\SampleManagement;

use App\Models\TestResult;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class TestApprovalComponent extends Component
{
    use WithPagination;

    public $perPage = 10;

    public $search = '';

    public $orderBy = 'reviewed_at';

    public $orderAsc = true;

    public $viewReport = false;

    public $testResult;

    public $approver_comment;

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function markAsApproved(TestResult $testResult)
    {
        $this->validate([
            'approver_comment' => 'required',
        ]);
        $testResult->approved_by = Auth::id();
        $testResult->approved_at = now();
        $testResult->status = 'Approved';
        $testResult->approver_comment = $this->approver_comment;
        $testResult->update();
        $this->emit('updateNav', 'testsPendindApprovalCount');

        $this->reset('approver_comment');
        $this->viewReport = false;
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Test Result Updated successfully!']);
    }

    public function markAsDeclined(TestResult $testResult)
    {
        $this->validate([
            'approver_comment' => 'required',
        ]);

        $testResult->approved_by = Auth::id();
        $testResult->approved_at = now();
        $testResult->approver_comment = $this->approver_comment;
        $testResult->status = 'Rejected';
        $testResult->update();
        $this->emit('updateNav', 'testsPendindApprovalCount');

        $this->reset('approver_comment');
        $this->viewReport = false;
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Test Result Updated successfully.!']);
    }

    public function viewPreliminaryReport(TestResult $testResult)
    {
        $this->testResult=$testResult;
        $this->viewReport = true;
    }

    public function refresh()
    {
        return redirect(request()->header('Referer'));
    }

    public function render()
    {
        if ($this->viewReport) {
            $testResults = $this->testResult->load(['test', 'sample', 'sample.participant', 'sample.sampleReception', 'sample.sampleType:id,type', 'sample.study:id,name', 'sample.requester', 'sample.collector:id,name']);
        } else {
            $testResults = TestResult::resultSearch($this->search, 'Reviewed')
            ->where('creator_lab', auth()->user()->laboratory_id)->with(['test', 'sample', 'sample.participant', 'sample.sampleReception', 'sample.sampleType:id,type', 'sample.study:id,name', 'sample.requester', 'sample.collector:id,name'])->where('status', 'Reviewed')
            ->orderBy($this->orderBy, $this->orderAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);
        }

        return view('livewire.lab.sample-management.test-approval-component', compact('testResults'));
    }
}
