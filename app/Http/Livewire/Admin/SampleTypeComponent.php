<?php

namespace App\Http\Livewire\Admin;

use App\Exports\SampleTypesExport;
use App\Models\Admin\Test;
use App\Models\SampleType;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class SampleTypeComponent extends Component
{
    use WithPagination;

    // public $paginate = 10;
    public $perPage = 10;

    public $search = '';

    public $orderBy = 'type';

    public $orderAsc = true;

    public $type;

    public $status;

    public $edit_id;

    public $delete_id;

    public $possible_tests = [];

    protected $paginationTheme = 'bootstrap';

    public function updated($fields)
    {
        $this->validateOnly($fields, [
            'type' => 'required|unique:sample_types',
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function export()
    {
        return (new SampleTypesExport())->download('sample_types.xlsx');
    }

    public function storeData()
    {
        $this->validate([
            'type' => 'required|unique:sample_types',
        ]);
        $sampleType = new SampleType();
        $sampleType->type = $this->type;
        $sampleType->possible_tests = $this->possible_tests;
        $sampleType->save();

        $this->status = '';
        $this->type = '';
        $this->possible_tests = [];
        $this->dispatchBrowserEvent('close-modal');
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Sample Type created successfully!']);
    }

    public function editdata($id)
    {
        $sampleType = SampleType::where('id', $id)->first();
        $this->edit_id = $sampleType->id;
        $this->type = $sampleType->type;
        $this->status = $sampleType->status;
        $this->possible_tests = $sampleType->possible_tests ?? [];

        $this->dispatchBrowserEvent('edit-modal');
    }

    public function refresh()
    {
        return redirect(request()->header('Referer'));
    }

    public function resetInputs()
    {
        $this->status = '';
        $this->type = '';
        $this->possible_tests = [];
    }

    public function updateData()
    {
        $this->validate([
            'type' => 'required|unique:sample_types,type,'.$this->edit_id.'',
            'status' => 'required',
        ]);
        $sampleType = SampleType::find($this->edit_id);
        $sampleType->type = $this->type;
        $sampleType->status = $this->status;
        $sampleType->possible_tests = $this->possible_tests;
        $sampleType->update();

        $this->resetInputs();
        $this->dispatchBrowserEvent('close-modal');
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Sample Type updated successfully!']);
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
            $value = SampleType::where('creator_lab', auth()->user()->laboratory_id)->where('id', $this->delete_id)->first();
            $value->delete();
            $this->delete_id = '';
            $this->dispatchBrowserEvent('close-modal');
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Record deleted successfully!']);
        } catch(\Exception $error) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => 'Record can not be deleted!']);
        }
    }

    public function cancel()
    {
        $this->delete_id = '';
    }

    public function close()
    {
        $this->resetInputs();
    }

    public function render()
    {
        // $sampleType = SampleType::where('creator_lab', auth()->user()->laboratory_id)->get();
        $sampleType = SampleType::search($this->search)->where('creator_lab', auth()->user()->laboratory_id)
        ->orderBy($this->orderBy, $this->orderAsc ? 'asc' : 'desc')
        ->paginate($this->perPage);
        $tests = Test::where('creator_lab', auth()->user()->laboratory_id)->select('id', 'name')->get();

        return view('livewire.admin.sample-type-component', compact('sampleType', 'tests'))->layout('layouts.app');
    }
}
