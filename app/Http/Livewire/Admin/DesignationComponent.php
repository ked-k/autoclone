<?php

namespace App\Http\Livewire\Admin;

use App\Exports\DesignationsExport;
use App\Models\Designation;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class DesignationComponent extends Component
{
    use WithPagination;

    public $perPage = 10;

    public $search = '';

    public $orderBy = 'name';

    public $orderAsc = true;

    public $name;

    public $description;

    public $is_active;
    
    public $edit_id;

    public $delete_id;

    public $export;

    protected $paginationTheme = 'bootstrap';

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
            'name' => 'required|unique:designations',
            'is_active' => 'required',

        ]);
    }

    public function storeData()
    {
        $this->validate([
            'name' => 'required|unique:designations',
        ]);

        $designation = new Designation();
        $designation->name = $this->name;
        $designation->description = $this->description ?? null;
        $designation->save();
        $this->description = '';
        $this->name = '';
        $this->dispatchBrowserEvent('close-modal');
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Designation created successfully!']);
    }

    public function editdata($id)
    {
        $designation = Designation::where('id', $id)->first();
        $this->edit_id = $designation->id;
        $this->name = $designation->name;
        $this->description = $designation->description;
        $this->is_active = $designation->is_active;
        $this->dispatchBrowserEvent('edit-modal');
    }

    public function resetInputs()
    {
        $this->description = '';
        $this->name = '';
    }

    public function updateData()
    {
        $this->validate([
            'name' => 'required',
        ]);
        $designation = Designation::find($this->edit_id);
        $designation->name = $this->name;
        $designation->description = $this->description ?? null;
        $designation->is_active = $this->is_active;
        $designation->update();

        $this->description = '';
        $this->name = '';
        $this->is_active = '';
        $this->dispatchBrowserEvent('close-modal');
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Designation updated  successfully!']);
    }

    public function export()
    {
        return (new DesignationsExport())->download('Designations.xlsx');
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
            $designation = Designation::where('id', $this->delete_id)->first();
            $designation->delete();
            $this->delete_id = '';
            $this->dispatchBrowserEvent('close-modal');
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Designation deleted successfully!']);
        } catch(Exception $error) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => 'Designation can not be deleted!']);
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
        $designations = Designation::search($this->search)
        ->orderBy($this->orderBy, $this->orderAsc ? 'asc' : 'desc')
        ->paginate($this->perPage);
        // $export=true;
        return view('livewire.admin.designation-component', compact('designations'))->layout('layouts.app');
    }
}
