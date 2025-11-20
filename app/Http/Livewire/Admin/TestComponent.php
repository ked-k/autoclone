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

    public $orderBy = 'id';

    public $orderAsc = 0;

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
    public $dynamicTests   = [];

    public $absolute_results = [];

    public $dynamicComments = [];

    public $dynamicParameters = [];

    public $comments = [];

    public $measurable_result_uom;
    public $parameter_uom;

    public $result_presentation;

    public $toggleForm = false;

    public $edit_id;

    public $is_sanas_accredited;

    protected $paginationTheme = 'bootstrap';

    // public $accreditation = 0;

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
            'name'        => 'required|string',
            'price'       => 'required|numeric',
            'result_type' => 'required|string',
            'status'      => 'required|integer',
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

        $this->dynamicParameters = [
            ['parameter' => 'parameter'],
        ];

        $this->dynamicTests = [
            ['Test' => 'test'],
        ];
    }

    public function updatedResultType()
    {
        if ($this->result_type === 'Text' || $this->result_type === 'File') {
            $this->reset(['dynamicResults', 'measurable_result_uom', 'is_sanas_accredited', 'absolute_results', 'dynamicParameters', 'result_presentation']);
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
            if ($result['result'] != 'result' && $result['result'] != '') {
                array_push($results, $result['result']);
            }
        }

        return $results;
    }

    public function addTest()
    {
        $this->dynamicTests[] = ['Test' => ' '];
    }

    public function removeTest($index)
    {
        unset($this->dynamicTests[$index]);
        $this->dynamicTests = array_values($this->dynamicTests);
    }

    public function pushTests()
    {
        $tests = [];
        foreach ($this->dynamicTests as $key => $test) {
            if ($test['test'] != 'test' && $test['test'] != '') {
                array_push($tests, $test['test']);
            }
        }

        return $tests;
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
            if ($comment['comment'] != 'comment' && $comment['comment'] != '') {
                array_push($comments, $comment['comment']);
            }
        }

        return $comments;
    }

    public function addParameter()
    {
        $this->dynamicParameters[] = ['parameter' => 'parameter'];
    }

    public function removeParameter($index)
    {
        unset($this->dynamicParameters[$index]);
        $this->dynamicParameters = array_values($this->dynamicParameters);
    }

    public function pushParameters()
    {
        $parameters = [];
        foreach ($this->dynamicParameters as $key => $parameter) {
            if ($parameter['parameter'] != 'parameter' && $parameter['parameter'] != '') {
                array_push($parameters, $parameter['parameter']);
            }
        }

        return $parameters;
    }

    public function storeTest()
    {
        $this->validate([
            'category_id'   => 'required|integer',
            'status'        => 'required',
            'name'          => 'required|string',
            'price'         => 'required|numeric',
            'is_sanas_accredited' => 'required|integer',
            'result_type'   => 'required|string',
        ]);
        $test                        = new Test();
        $test->category_id           = $this->category_id;
        $test->name                  = $this->name;
        $test->short_code            = $this->short_code;
        $test->price                 = $this->price;
        $test->tat                   = $this->tat;
        $test->reference_range_min   = $this->reference_range_min;
        $test->reference_range_max   = $this->reference_range_max;
        $test->status                = $this->status;
        $test->is_sanas_accredited   = $this->is_sanas_accredited;
        $test->precautions           = $this->precautions;
        $test->result_type           = $this->result_type;
        $test->absolute_results      = count($this->pushResults()) ? $this->pushResults() : null;
        $test->measurable_result_uom = $this->measurable_result_uom;
        $test->comments              = count($this->pushComments()) ? $this->pushComments() : null;
        if ($this->result_type == 'Multiple') {
            $test->sub_tests = count($this->pushTests()) ? $this->pushTests() : null;
        }
        $test->parameters          = count($this->pushParameters()) ? $this->pushParameters() : null;
        $test->parameter_uom       = $this->parameter_uom ?? null;
        $test->result_presentation = $this->result_presentation ?? null;
        $test->save();

        $this->resetTestInputs();
        $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => 'Test created successfully!']);
    }

    public function editTest(Test $test)
    {
        $this->edit_id               = $test->id;
        $this->category_id           = $test->category_id;
        $this->name                  = $test->name;
        $this->short_code            = $test->short_code;
        $this->price                 = $test->price;
        $this->tat                   = $test->tat;
        $this->reference_range_min   = $test->reference_range_min;
        $this->reference_range_max   = $test->reference_range_max;
        $this->status                = $test->status;
        $this->is_sanas_accredited   = $test->is_sanas_accredited;
        $this->precautions           = $test->precautions;
        $this->result_type           = $test->result_type;
        $this->measurable_result_uom = $test->measurable_result_uom;
        $this->result_presentation   = $test->result_presentation;
        $this->parameter_uom         = $test->parameter_uom;

        $this->dynamicResults    = [];
        $this->dynamicComments   = [];
        $this->dynamicParameters = [];
        if ($test->absolute_results != null) {
            foreach ($test->absolute_results as $key => $result) {
                if (count($test->absolute_results)) {
                    array_push($this->dynamicResults, ['result' => $result]);
                }
            }
        }
        if ($test->sub_tests != null) {
            foreach ($test->sub_tests as $key => $sub_test) {
                if (count($test->sub_tests)) {
                    array_push($this->dynamicTests, ['test' => $sub_test]);
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

        if ($test->parameters != null) {
            foreach ($test->parameters as $key => $parameter) {
                if (count($test->parameters)) {
                    array_push($this->dynamicParameters, ['parameter' => $parameter]);
                }
            }
        }

        $this->toggleForm = true;
    }

    public function updateTest()
    {
        $this->validate([
            'category_id' => 'required|integer',
            'name'        => 'required|string',
            'price'       => 'required|numeric',
            'result_type' => 'required|string',
            'status'      => 'required|integer',
        ]);

        $test                        = Test::find($this->edit_id);
        $test->category_id           = $this->category_id;
        $test->name                  = $this->name;
        $test->short_code            = $this->short_code;
        $test->price                 = $this->price;
        $test->tat                   = $this->tat;
        $test->reference_range_min   = $this->reference_range_min;
        $test->reference_range_max   = $this->reference_range_max;
        $test->is_sanas_accredited   = $this->is_sanas_accredited;
        $test->status                = $this->status;
        $test->precautions           = $this->precautions;
        $test->result_type           = $this->result_type;
        $test->absolute_results      = count($this->pushResults()) ? $this->pushResults() : null;
        $test->measurable_result_uom = $this->measurable_result_uom;
        if ($this->result_type == 'Multiple') {
            $test->sub_tests = count($this->pushTests()) ? $this->pushTests() : null;
        }
        $test->comments            = count($this->pushComments()) ? $this->pushComments() : null;
        $test->parameters          = count($this->pushParameters()) ? $this->pushParameters() : null;
        $test->result_presentation = $this->result_presentation ?? null;
        $test->parameter_uom       = $this->parameter_uom ?? null;
        $test->update();

        $this->toggleForm = false;
        $this->resetTestInputs();
        $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => 'Test updated successfully!']);
    }

    public function resetTestInputs()
    {
        $this->reset(['category_id', 'name', 'short_code', 'tat', 'price', 'reference_range_max', 'reference_range_min', 'status', 'precautions', 'result_type', 'measurable_result_uom', 'dynamicResults', 'absolute_results', 'dynamicComments', 'dynamicParameters', 'dynamicTests', 'result_presentation', 'parameter_uom']);
    }
    public function accreditation($id, $state)
    {
        // dd($id, $state);
        $test = Test::where('id', $id)->update(['is_sanas_accredited' => $state]);
        if ($test) {
            $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => 'Test accreditation status updated successfully!']);
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => 'Test accreditation status could not be updated!']);
        }
    }
    public function deleteConfirmation($id)
    {
        if (Auth::user()->hasPermission(['manage-users'])) {
            $this->delete_id = $id;
            $this->dispatchBrowserEvent('delete-modal');
        } else {
            $this->dispatchBrowserEvent('cant-delete', ['type' => 'warning', 'message' => 'Oops! You do not have the necessary permissions to delete this resource!']);
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
            $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => 'Test deleted successfully!']);
        } catch (\Exception $error) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => 'Test can not be deleted!']);
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
