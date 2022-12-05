<?php

namespace App\Http\Livewire\Lab\SampleManagement;

use App\Models\TestResult;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class TestReviewComponent extends Component
{
    use WithPagination;

    public $perPage = 10;

    public $search = '';

    public $orderBy = 'id';

    public $orderAsc = true;

    public $viewReport = false;

    public $testResult;

    public $reviewer_comment;

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function markAsReviewed(TestResult $testResult)
    {
        $this->validate([
            'reviewer_comment' => 'required',
        ]);
        $testResult->reviewed_by = Auth::id();
        $testResult->reviewed_at = now();
        $testResult->status = 'Reviewed';
        $testResult->reviewer_comment = $this->reviewer_comment;
        $testResult->approver_comment =null;
        $testResult->update();
        $this->emit('updateNav', 'testsPendindReviewCount');

        $this->reset('reviewer_comment');
        $this->viewReport = false;
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Test Result Updated successfully.!']);
    }

    public function markAsDeclined(TestResult $testResult)
    {
        $this->validate([
            'reviewer_comment' => 'required',
        ]);

        $testResult->reviewed_by = Auth::id();
        $testResult->reviewed_at = now();
        $testResult->reviewer_comment = $this->reviewer_comment;
        $testResult->status = 'Rejected';
        $testResult->update();
        $this->emit('updateNav', 'testsPendindReviewCount');

        $this->reset('reviewer_comment');
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
            $testResults = TestResult::resultSearch($this->search, 'Pending Review')
            ->where('status', 'Pending Review')
            ->with(['test', 'sample', 'sample.participant', 'sample.sampleReception', 'sample.sampleType:id,type', 'sample.study:id,name', 'sample.requester', 'sample.collector:id,name'])
            ->orderBy($this->orderBy, $this->orderAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);
        }

        return view('livewire.lab.sample-management.test-review-component', compact('testResults'));
    }
}
