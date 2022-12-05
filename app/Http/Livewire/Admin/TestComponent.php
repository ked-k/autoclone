<?php

namespace App\Http\Livewire\Admin;

use App\Exports\TestsExport;
use App\Models\Admin\Test;
use App\Models\TestCategory;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class TestComponent extends Component
{
    use WithPagination;

    public $perPage = 10;

    public $search = '';

    public $orderBy = 'name';

    public $orderAsc = true;

    public $category_name;

    public $category_id;

    public $name;

    public $short_code;

    public $price;

    public $tat;

    public $reference_range_min;

    public $reference_range_max;

    public $status;

    public $precautions;

    public $result_type;

    public $dynamicResults = [];

    public $absolute_results = [];

    public $dynamicComments = [];

    public $comments = [];

    public $measurable_result_uom;

    public $toggleForm = false;

    public $edit_id;

    protected $paginationTheme = 'bootstrap';

    public function export()
    {
        return (new TestsExport())->download('Tests.xlsx');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updated($fields)
    {
        $this->validateOnly($fields, [
            'category_id' => 'required|integer',
            'name' => 'required|string',
            'price' => 'required|numeric',
            'result_type' => 'required|string',
            'status' => 'required|integer',
        ]);
    }

    public function mount()
    {
        $this->dynamicResults = [
            ['result' => 'result'],
        ];

        $this->dynamicComments = [
            ['comment' => 'comment'],
        ];
    }

    public function updatedResultType()
    {
        if ($this->result_type === 'Text' || $this->result_type === 'File') {
            $this->reset(['dynamicResults', 'measurable_result_uom', 'absolute_results']);
        }
    }

    public function addResult()
    {
        $this->dynamicResults[] = ['result' => 'result'];
    }

    public function removeResult($index)
    {
        unset($this->dynamicResults[$index]);
        $this->dynamicResults = array_values($this->dynamicResults);
    }

    public function pushResults()
    {
        $results = [];
        foreach ($this->dynamicResults as $key => $result) {
            if ($result['result'] != 'result') {
                array_push($results, $result['result']);
            }
        }

        return $results;
    }

    public function addComment()
    {
        $this->dynamicComments[] = ['comment' => 'comment'];
    }

    public function removeComment($index)
    {
        unset($this->dynamicComments[$index]);
        $this->dynamicComments = array_values($this->dynamicComments);
    }

    public function pushComments()
    {
        $comments = [];
        foreach ($this->dynamicComments as $key => $comment) {
            if ($comment['comment'] != 'comment') {
                array_push($comments, $comment['comment']);
            }
        }

        return $comments;
    }

    public function storeTest()
    {
        $this->validate([
            'category_id' => 'required|integer',
            'name' => 'required|string',
            'price' => 'required|numeric',
            'result_type' => 'required|string',
        ]);
        $test = new Test();
        $test->category_id = $this->category_id;
        $test->name = $this->name;
        $test->short_code = $this->short_code;
        $test->price = $this->price;
        $test->tat = $this->tat;
        $test->reference_range_min = $this->reference_range_min;
        $test->reference_range_max = $this->reference_range_max;
        $test->status = $this->status;
        $test->precautions = $this->precautions;
        $test->result_type = $this->result_type;
        $test->absolute_results = count($this->pushResults()) ? $this->pushResults() : null;
        $test->measurable_result_uom = $this->measurable_result_uom;
        $test->comments = count($this->pushComments()) ? $this->pushComments() : null;
        $test->save();

        $this->resetTestInputs();
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Test created successfully!']);
    }

    public function editTest(Test $test)
    {
        $this->edit_id = $test->id;
        $this->category_id = $test->category_id;
        $this->name = $test->name;
        $this->short_code = $test->short_code;
        $this->price = $test->price;
        $this->tat = $test->tat;
        $this->reference_range_min = $test->reference_range_min;
        $this->reference_range_max = $test->reference_range_max;
        $this->status = $test->status;
        $this->precautions = $test->precautions;
        $this->result_type = $test->result_type;
        $this->measurable_result_uom = $test->measurable_result_uom;

        $this->dynamicResults = [];
        $this->dynamicComments = [];
        if ($test->absolute_results != null) {
            foreach ($test->absolute_results as $key => $result) {
                if (count($test->absolute_results)) {
                    array_push($this->dynamicResults, ['result' => $result]);
                }
            }
        }
        if ($test->comments != null) {
            foreach ($test->comments as $key => $comment) {
                if (count($test->comments)) {
                    array_push($this->dynamicComments, ['comment' => $comment]);
                }
            }
        }

        $this->toggleForm = true;
    }

    public function updateTest()
    {
        $this->validate([
            'category_id' => 'required|integer',
            'name' => 'required|string',
            'price' => 'required|numeric',
            'result_type' => 'required|string',
            'status' => 'required|integer',
        ]);
        $test = Test::find($this->edit_id);
        $test->category_id = $this->category_id;
        $test->name = $this->name;
        $test->short_code = $this->short_code;
        $test->price = $this->price;
        $test->tat = $this->tat;
        $test->reference_range_min = $this->reference_range_min;
        $test->reference_range_max = $this->reference_range_max;
        $test->status = $this->status;
        $test->precautions = $this->precautions;
        $test->result_type = $this->result_type;
        $test->absolute_results = count($this->pushResults()) ? $this->pushResults() : null;
        $test->measurable_result_uom = $this->measurable_result_uom;
        $test->comments = count($this->pushComments()) ? $this->pushComments() : null;
        $test->update();

        $this->toggleForm = false;
        $this->resetTestInputs();
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Test updated successfully!']);
    }

    public function resetTestInputs()
    {
        $this->reset(['category_id', 'name', 'short_code', 'tat', 'price', 'reference_range_max', 'reference_range_min', 'status', 'precautions', 'result_type', 'measurable_result_uom', 'dynamicResults', 'absolute_results', 'dynamicComments']);
    }

    public function deleteConfirmation($id)
    {
        if (Auth::user()->hasPermission(['manage-users'])) {
            $this->delete_id = $id;
            $this->dispatchBrowserEvent('delete-modal');
        } else {
            $this->dispatchBrowserEvent('cant-delete', ['type' => 'warning',  'message' => 'Oops! You do not have the necessary permissions to delete this resource!']);
        }
    }

    public function deleteData()
    {
        try {
            $test = Test::where('id', $this->delete_id)->first();
            $test->delete();
            $this->delete_id = '';
            $this->dispatchBrowserEvent('close-modal');
            session()->flash('success', 'Test deleted successfully.');
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Test deleted successfully!']);
        } catch(\Exception $error) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => 'Test can not be deleted!']);
        }
    }

    public function refresh()
    {
        return redirect(request()->header('Referer'));
    }

    public function cancel()
    {
        $this->delete_id = '';
    }

    public function close()
    {
        $this->toggleForm = false;
        $this->resetTestInputs();
    }

    public function render()
    {
        $tests = Test::search($this->search)
        ->where('creator_lab', auth()->user()->laboratory_id)
        ->orderBy($this->orderBy, $this->orderAsc ? 'asc' : 'desc')
        ->paginate($this->perPage);
        $testCategories = TestCategory::where('creator_lab', auth()->user()->laboratory_id)->latest()->get();

        return view('livewire.admin.test-component', compact('tests', 'testCategories'));
    }
}
