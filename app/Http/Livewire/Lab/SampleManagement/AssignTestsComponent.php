<?php

namespace App\Http\Livewire\Lab\SampleManagement;

use App\Models\Admin\Test;
use App\Models\Sample;
use App\Models\TestAssignment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AssignTestsComponent extends Component
{
    use WithPagination;

    public $perPage = 10;

    public $search = '';

    public $orderBy = 'lab_no';

    public $orderAsc = true;

    public $tests_requested;

    public $request_acknowledged_by;

    public $sample_identity;

    public $clinical_notes;

    public $lab_no;

    public $sample_id;

    public $test_id;

    public $assignee;

    public $assignedTests;

    public $backlog;

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->tests_requested = collect([]);
        $this->assignedTests = [];
    }

    public function activateTest($id)
    {
        $this->test_id = $id;
    }

    public function UpdatedAssignee()
    {
        $this->backlog = TestAssignment::where(['assignee' => $this->assignee, 'status' => 'Assigned'])->count();
    }

    public function refresh()
    {
        return redirect(request()->header('Referer'));
    }

    public function viewTests(Sample $sample)
    {
        $this->reset(['tests_requested', 'request_acknowledged_by']);
        $this->assignedTests = TestAssignment::where('sample_id', $sample->id)->get()->pluck('test_id')->toArray();
        $tests = Test::whereIn('id', array_diff($sample->tests_requested, $this->assignedTests ?? []))->get();
        $this->tests_requested = $tests;
        $this->test_id = $tests[0]->id;
        $this->sample_identity = $sample->sample_identity;
        $this->lab_no = $sample->lab_no;
        $this->clinical_notes = $sample->participant->clinical_notes;
        $this->sample_id = $sample->id;
        $this->request_acknowledged_by = $sample->request_acknowledged_by;

        $this->dispatchBrowserEvent('view-tests');
    }

    public function assignTest()
    {
        $this->validate([
            'assignee' => 'required|integer',
        ]);
        $isExist = TestAssignment::select('*')
        ->where('sample_id', $this->sample_id)
        ->where('test_id', $this->test_id)
        ->exists();

        if ($isExist) {
            $this->dispatchBrowserEvent('close-modal');
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => 'Test already Assigned to someone!']);
        } else {
            $test_assignment = new TestAssignment();
            $test_assignment->sample_id = $this->sample_id;
            $test_assignment->test_id = $this->test_id;
            $test_assignment->assignee = $this->assignee;
            $test_assignment->save();

            $sample = Sample::where('id', $this->sample_id)->first();
            $this->assignedTests = TestAssignment::where('sample_id', $this->sample_id)->get()->pluck('test_id')->toArray();
            if (array_diff($sample->tests_requested, $this->assignedTests) == []) {
                $sample->update(['status' => 'Assigned']);
                // $this->refresh();
                $this->dispatchBrowserEvent('close-modal');
                $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Test Assignment completed successfully!']);
            } else {
                $this->tests_requested = Test::whereIn('id', array_diff($sample->tests_requested, $this->assignedTests))->get();
                $this->test_id = $this->tests_requested[0]->id;
                $this->reset(['assignee', 'backlog']);
                $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Test Assigned successfully!']);
            }
        }
    }

    public function acknowledgeRequest(Sample $sample)
    {
        $sample->request_acknowledged_by = Auth::id();
        $sample->date_acknowledged = now();
        $sample->status = 'Processing';
        $sample->update();
        $this->request_acknowledged_by = $sample->request_acknowledged_by;
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Test Request Updated successfully!']);
    }

    public function close()
    {
        $this->reset(['sample_id', 'sample_identity', 'lab_no', 'assignee', 'test_id', 'clinical_notes']);
        $this->tests_requested = collect([]);
    }

    public function render()
    {
        $samples = Sample::search($this->search, ['Accessioned', 'Processing'])
        ->whereIn('status', ['Accessioned', 'Processing'])
        ->where(['creator_lab' => auth()->user()->laboratory_id, 'sample_is_for' => 'Testing'])
        ->with(['participant', 'sampleType:id,type', 'study:id,name', 'requester:id,name', 'collector:id,name', 'sampleReception'])
        ->orderBy($this->orderBy, $this->orderAsc ? 'asc' : 'desc')
        ->paginate($this->perPage);

        $users = User::where(['is_active' => 1, 'laboratory_id' => auth()->user()->laboratory_id])->get();
        $tests = $this->tests_requested;

        return view('livewire.lab.sample-management.assign-tests-component', compact('samples', 'users', 'tests'));
    }
}
