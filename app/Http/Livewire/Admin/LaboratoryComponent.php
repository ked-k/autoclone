<?php

namespace App\Http\Livewire\Admin;

use App\Exports\LaboratoriesExport;
use App\Models\Laboratory;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class LaboratoryComponent extends Component
{
    use WithPagination;

    public $perPage = 10;

    public $search = '';

    public $orderBy = 'laboratory_name';

    public $orderAsc = true;

    public $laboratory_name;

    public $short_code;

    public $description;

    public $is_active;

    public $delete_id;

    public $edit_id;

    public $test_approver;

    public $test_reviewer;

    public $lab_manger;

    protected $paginationTheme = 'bootstrap';

    public function export()
    {
        return (new LaboratoriesExport())->download('laboratories.xlsx');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    protected $validationAttributes = [
        'is_active' => 'status',
    ];

    public function updated($fields)
    {
        $this->validateOnly($fields, [
            'laboratory_name' => 'required|unique:laboratories',
            'short_code' => 'required',
            'is_active' => 'required',

        ]);
    }

    public function storeData()
    {
        $this->validate([
            'laboratory_name' => 'required|unique:laboratories',
            'short_code' => 'required|unique:laboratories',
        ]);

        $laboratory = new Laboratory();
        $laboratory->laboratory_name = $this->laboratory_name;
        $laboratory->short_code = $this->short_code;
        $laboratory->test_approver = $this->test_approver;
        $laboratory->test_reviewer = $this->test_reviewer;
        $laboratory->lab_manger = $this->lab_manger;
        $laboratory->description = $this->description;

        $laboratory->save();

        $this->description = '';
        $this->laboratory_name = '';
        $this->short_code = '';
        $this->dispatchBrowserEvent('close-modal');
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Laboratory created successfully!']);
    }

    public function editdata($id)
    {
        $laboratory = Laboratory::where('id', $id)->first();
        $this->edit_id = $laboratory->id;
        $this->laboratory_name = $laboratory->laboratory_name;
        $this->short_code = $laboratory->short_code;
        $this->description = $laboratory->description;
        $this->is_active = $laboratory->is_active;
        $this->test_approver = $laboratory->test_approver;
        $this->test_reviewer = $laboratory->test_reviewer;
        $this->lab_manger = $laboratory->lab_manger;
        $this->dispatchBrowserEvent('edit-modal');
    }

    public function resetInputs()
    {
        $this->description = '';
        $this->laboratory_name = '';
    }

    public function updateData()
    {
        $this->validate([
            'laboratory_name' => 'required',
            'short_code' => 'required',
        ]);
        $laboratory = Laboratory::find($this->edit_id);
        $laboratory->laboratory_name = $this->laboratory_name;
        $laboratory->short_code = $this->short_code;
        $laboratory->description = $this->description;
        $laboratory->is_active = $this->is_active;
        $laboratory->test_approver = $this->test_approver;
        $laboratory->test_reviewer = $this->test_reviewer;
        $laboratory->lab_manger = $this->lab_manger;
        $laboratory->update();

        $this->description = '';
        $this->laboratory_name = '';
        $this->short_code = '';
        $this->is_active = '';
        $this->dispatchBrowserEvent('close-modal');
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Laboratory updated successfully!']);
    }

    public function refresh()
    {
        return redirect(request()->header('Referer'));
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
            $laboratory = Laboratory::where('id', $this->delete_id)->first();
            $laboratory->delete();
            $this->delete_id = '';
            $this->dispatchBrowserEvent('close-modal');
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Laboratory deleted successfully!']);
        } catch(Exception $error) {
            $this->dispatchBrowserEvent('error', ['type' => 'success',  'message' => 'Laboratory can not be deleted!']);
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
        $laboratories = Laboratory::search($this->search)
        ->orderBy($this->orderBy, $this->orderAsc ? 'asc' : 'desc')
        ->paginate($this->perPage);
        $users = User::where('is_active', true)->get();
        return view('livewire.admin.laboratory-component', compact('laboratories','users'))->layout('layouts.app');
    }
}
