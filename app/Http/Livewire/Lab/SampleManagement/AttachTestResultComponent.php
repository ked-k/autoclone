<?php

namespace App\Http\Livewire\Lab\SampleManagement;

use App\Models\Admin\Test;
use App\Models\Sample;
use App\Models\TestAssignment;
use App\Models\TestResult;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithFileUploads;

class AttachTestResultComponent extends Component
{
    use WithFileUploads;

    public $requestedTests;

    public $tests_performed = [];

    public $sample;

    public $sample_id;

    public $test_id;

    public $result;

    public $link;

    public $attachment;

    public $attachmentPath;

    public $performed_by;

    public $comment;

    public $status;

    public $sample_identity;

    public $lab_no;

    public function mount($id)
    {
        $sample = Sample::findOrFail($id);
        $this->sample = $sample;
        $this->sample_id = $sample->id;
        $this->sample_identity = $sample->sample_identity;
        $this->lab_no = $sample->lab_no;
        $testsPendingResults = array_diff($sample->tests_requested, $sample->tests_performed ?? []);

        if (count($testsPendingResults) > 0) {
            $this->requestedTests = Test::whereIn('id', (array) $testsPendingResults)
            ->whereHas('testAssignment', function (Builder $query) {
                $query->where(['assignee' => auth()->user()->id, 'sample_id' => $this->sample_id, 'status' => 'Assigned']);
            })
            ->orderBy('name', 'asc')->get();
            $this->test_id = $this->requestedTests[0]->id ?? null;
        } else {
            $this->requestedTests = collect([]);
            $this->reset('test_id');
        }

        $this->tests_performed = (array) $sample->tests_performed;
        $this->performed_by = auth()->user()->id;
    }

    public function storeTestResults()
    {
        $this->validate([
            'performed_by' => 'required|integer',
        ]);

        if ($this->link != null) {
            $this->validate([
                'link' => 'required|url',
            ]);
        }

        if ($this->attachment != null) {
            $this->validate([
                'attachment' => ['mimes:pdf,xls,xlsx,csv,doc,docx', 'max:5000'],
            ]);
            $attachmentName = date('YmdHis').'.'.$this->attachment->extension();
            $this->attachmentPath = $this->attachment->storeAs('attachmentResults', $attachmentName);
        } else {
            $test = Test::findOrfail($this->test_id);
            if ($test->result_type == 'File') {
                $this->validate([
                    'attachment' => ['required'],
                ]);
            } else {
                $this->attachmentPath = null;
            }
        }

        $testResult = new TestResult();
        $testResult->sample_id = $this->sample_id;
        $testResult->test_id = $this->test_id;
        if ($this->link != null) {
            $testResult->result = $this->link;
        } else {
            $test = Test::findOrfail($this->test_id);
            if ($test->result_type == 'Measurable') {
                $testResult->result = $this->result.''.$test->measurable_result_uom;
            } else {
                $testResult->result = $this->result;
            }
        }

        $testResult->attachment = $this->attachmentPath;
        $testResult->performed_by = $this->performed_by;
        $testResult->comment = $this->comment;
        $testResult->status = 'Pending Review';

        $testResult->save();

        array_push($this->tests_performed, "{$testResult->test_id}");
        $associatedSample = Sample::findOrfail($this->sample_id);
        $testAssignment = TestAssignment::where(['assignee' => auth()->user()->id, 'sample_id' => $this->sample_id, 'test_id' => $this->test_id])->first();
        $associatedSample->update(['tests_performed' => $this->tests_performed]);
        $testAssignment->update(['status' => 'Test Done']);

        if (count(array_diff($associatedSample->tests_requested, $associatedSample->tests_performed)) == 0) {
            $associatedSample->update(['status' => 'Tests Done']);
            redirect()->route('test-request');
        }

        if (TestAssignment::where(['sample_id' => $this->sample_id, 'assignee' => auth()->user()->id, 'status' => 'Assigned'])->count() == 0) {
            redirect()->route('test-request');
        }

        $this->resetResultInputs();
        $this->mount($associatedSample->id);
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Test Results Recorded successfully!']);
    }

    public function activateResultInput($id)
    {
        $this->reset(['result', 'attachment', 'comment']);
        $this->test_id = $id;
    }

    public function resetResultInputs()
    {
        $this->reset(['result', 'link', 'attachment', 'performed_by', 'comment', 'attachmentPath']);
    }

    public function close()
    {
        $this->reset(['result', 'attachment', 'performed_by', 'comment']);
    }

    public function render()
    {
        $users = User::where(['is_active' => 1, 'laboratory_id' => auth()->user()->laboratory_id])->get();
        $testsRequested = $this->requestedTests ?? collect();

        return view('livewire.lab.sample-management.attach-test-result-component', compact('users', 'testsRequested'));
    }
}
