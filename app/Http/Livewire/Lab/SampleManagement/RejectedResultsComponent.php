<?php

namespace App\Http\Livewire\Lab\SampleManagement;

use Livewire\Component;
use App\Models\Admin\Test;
use App\Models\TestResult;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;

class RejectedResultsComponent extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $perPage = 10;

    public $search = '';

    public $orderBy = 'id';

    public $orderAsc = true;

    public $viewReport = false;

    public $testResult;

    public $testResultId;

    public $reviewer_comment;

    public $approver_comment;

    public $test;

    public $comment;

    protected $paginationTheme = 'bootstrap';


    //RESULTS
    public $result;

    public $link;

    public $attachment;

    public $attachmentPath;

    public $performed_by;


    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->performed_by=auth()->user()->id;
    }


    public function updateResult()
    {
        $this->validate([
            'performed_by' => 'required|integer',
        ]);

        if ($this->link != null) {
            $this->validate([
                'link' => 'required|url',
            ]);
        }
        $testResult = TestResult::findOrfail($this->testResultId);

        if ($this->attachment != null) {
            $this->validate([
                'attachment' => ['mimes:pdf,xls,xlsx,csv,doc,docx', 'max:5000'],
            ]);
            $attachmentName = date('YmdHis').'.'.$this->attachment->extension();
            $this->attachmentPath = $this->attachment->storeAs('attachmentResults', $attachmentName);

            if (file_exists(storage_path('app/').$testResult->attachment)) {
                @unlink(storage_path('app/').$testResult->attachment);
            }

        } else {
            if ($this->test->result_type == 'File') {
                $this->validate([
                    'attachment' => ['required'],
                ]);
            } else {
                $this->attachmentPath = null;
            }
        }

        if ($this->link != null) {
            $testResult->result = $this->link;
        } else {
            if ($this->test->result_type == 'Measurable') {
                if ($testResult->result==$this->result) {
                    $testResult->result = $this->result;
                }else{
                    $testResult->result = $this->result.''.$this->measurable_result_uom;
                }
                
            } else {
                $testResult->result = $this->result;
            }
        }

        $testResult->attachment = $this->attachmentPath;
        $testResult->performed_by = $this->performed_by;
        $testResult->reviewed_by = null;
        $testResult->reviewed_at = null;
        $testResult->approved_by = null;
        $testResult->approved_at = null;
        $testResult->reviewer_comment = null;
        $testResult->approver_comment = null;
        $testResult->comment = $this->comment;
        $testResult->status = 'Pending Review';

        $testResult->update();

        $this->viewReport = false;
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Test Result Updated successfully!']);
    }

    public function viewPreliminaryReport(TestResult $testResult)
    {
        $this->testResult=$testResult;
        $this->testResultId=$testResult->id;
        $this->result=$testResult->result;
        $this->comment=$testResult->comment;
        $this->test = Test::findOrFail($testResult->test_id);
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
            $testResults = TestResult::resultSearch($this->search, 'Rejected')
            ->where('status', 'Rejected')
            ->where(['status'=>'Rejected','performed_by'=> auth()->user()->id])
            ->with(['test', 'sample', 'sample.participant', 'sample.sampleReception', 'sample.sampleType:id,type', 'sample.study:id,name', 'sample.requester', 'sample.collector:id,name'])
            ->orderBy($this->orderBy, $this->orderAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);
        }
        return view('livewire.lab.sample-management.rejected-results-component',compact('testResults'));
    }
}
