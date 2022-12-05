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

    public $resultId;

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function markAsApproved(TestResult $testResult)
    {
        $testResult->approved_by = Auth::id();
        $testResult->approved_at = now();
        $testResult->status = 'Approved';
        $testResult->update();
        $this->emit('updateNav', 'testsPendindApprovalCount');

        $this->viewReport = false;
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Test Result Updated successfully!']);
    }

    public function viewPreliminaryReport(TestResult $testResult)
    {
        $this->resultId = $testResult->id;
        $this->viewReport = true;
    }

    public function refresh()
    {
        return redirect(request()->header('Referer'));
    }

    public function render()
    {
        if ($this->viewReport) {
            $testResults = TestResult::where('creator_lab', auth()->user()->laboratory_id)->with(['test', 'sample', 'sample.participant', 'sample.sampleReception', 'sample.sampleType:id,type', 'sample.study:id,name', 'sample.requester', 'sample.collector:id,name'])->where(['id' => $this->resultId, 'status' => 'Reviewed'])->first();
        } else {
            $testResults = TestResult::resultSearch($this->search, 'Reviewed')
            ->where('creator_lab', auth()->user()->laboratory_id)->with(['test', 'sample', 'sample.participant', 'sample.sampleReception', 'sample.sampleType:id,type', 'sample.study:id,name', 'sample.requester', 'sample.collector:id,name'])->where('status', 'Reviewed')
            ->orderBy($this->orderBy, $this->orderAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);
        }

        return view('livewire.lab.sample-management.test-approval-component', compact('testResults'));
    }
}
