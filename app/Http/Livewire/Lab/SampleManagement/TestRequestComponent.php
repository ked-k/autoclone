<?php

namespace App\Http\Livewire\Lab\SampleManagement;

use App\Models\Sample;
use Livewire\Component;
use App\Models\Admin\Test;
use App\Models\SampleType;
use Livewire\WithPagination;
use App\Models\TestAssignment;
use App\Models\AliquotingAssignment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class TestRequestComponent extends Component
{
    use WithPagination;

    public $perPage = 10;

    public $search = '';

    public $orderBy = 'lab_no';

    public $orderAsc = true;

    public $sample_is_for = 'Testing';

    public $tests_requested;

    public $aliquots;

    public $request_acknowledged_by;

    public $sample_identity;

    public $clinical_notes;

    public $lab_no;

    public $sample_id;

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->tests_requested = collect([]);
        $this->aliquots = collect([]);
    }

    public function refresh()
    {
        return redirect(request()->header('Referer'));
    }

    public function viewTests(Sample $sample)
    {
        $assignedTests = TestAssignment::where(['sample_id' => $sample->id, 'assignee' => auth()->user()->id])->get()->pluck('test_id')->toArray();
        $tests = Test::whereIn('id', $assignedTests)->get();
        $this->tests_requested = $tests;
        $this->sample_identity = $sample->sample_identity;
        $this->lab_no = $sample->lab_no;
        $this->request_acknowledged_by = $sample->request_acknowledged_by;
        $this->clinical_notes = $sample->participant->clinical_notes;
        $this->sample_id = $sample->id;

        $this->dispatchBrowserEvent('view-tests');
    }

    public function viewAliquots(Sample $sample)
    {
        $aliquots = SampleType::whereIn('id', (array) $sample->tests_requested)->get();
        $this->aliquots = $aliquots;
        $this->sample_identity = $sample->sample_identity;
        $this->lab_no = $sample->lab_no;
        $this->request_acknowledged_by = $sample->request_acknowledged_by;
        $this->sample_id = $sample->id;

        $this->dispatchBrowserEvent('view-tests');
    }

    public function close()
    {
        $this->tests_requested = collect([]);
        $this->aliquots = collect([]);
        $this->reset(['sample_id', 'sample_identity', 'lab_no', 'request_acknowledged_by']);
    }

    public function getSamples()
    {
        $samples = Sample::search($this->search, ['Assigned', 'Processing'])
        ->whereIn('status', ['Assigned', 'Processing'])
        ->where(['creator_lab' => auth()->user()->laboratory_id, 'sample_is_for' => $this->sample_is_for])
        ->with(['participant', 'sampleType:id,type', 'study:id,name', 'requester:id,name', 'collector:id,name', 'sampleReception'])
        ->when($this->sample_is_for == 'Testing', function ($query) {
            $query->whereHas('testAssignment', function (Builder $query) {
                $query->where(['assignee' => auth()->user()->id, 'status' => 'Assigned']);
            });
        }, function ($query) {
            $query->whereHas('aliquotingAssignment', function (Builder $query) {
                $query->where(['assignee' => auth()->user()->id, 'status' => 'Assigned']);
            });
        })
        ->orderBy($this->orderBy, $this->orderAsc ? 'asc' : 'desc')
        ->paginate($this->perPage);

        return $samples;
    }

    public function getAssignments()
    {
        $counts['testAssignmentCount']=TestAssignment::where(['assignee' => auth()->user()->id, 'status' => 'Assigned'])->count();
        
        $counts['aliquotingAssignmentCount']=AliquotingAssignment::where(['assignee' => auth()->user()->id, 'status' => 'Assigned'])
        ->whereHas('sample', function (Builder $query) {
            $query->where('sample_is_for', 'Aliquoting');
        })->count();

        $counts['storageAssignmentCount']=AliquotingAssignment::where(['assignee' => auth()->user()->id, 'status' => 'Assigned'])
        ->whereHas('sample', function (Builder $query) {
            $query->where('sample_is_for', 'Storage');
        })->count();

        return $counts;
    }

    public function render()
    {
        $samples=$this->getSamples();
        $testAssignmentCount=$this->getAssignments()['testAssignmentCount'];
        $aliquotingAssignmentCount=$this->getAssignments()['aliquotingAssignmentCount'];
        $storageAssignmentCount=$this->getAssignments()['storageAssignmentCount'];

        return view('livewire.lab.sample-management.test-request-component', compact('samples','testAssignmentCount','aliquotingAssignmentCount','storageAssignmentCount'));
    }
}
