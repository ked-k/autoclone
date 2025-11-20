<?php

namespace App\Http\Livewire\Lab\SampleStorage;

use App\Exports\FreezerLocationsExport;
use App\Models\FreezerLocation;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class FreezerLocationComponent extends Component
{
    use WithPagination;

    public $perPage = 10;

    public $search = '';

    public $orderBy = 'name';

    public $orderAsc = true;

    public $name;

    public $description;

    public $is_active;

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
            'name' => 'required|unique:freezer_locations',
            'is_active' => 'required',

        ]);
    }

    public function storeData()
    {
        $this->validate([
            'name' => 'required|unique:freezer_locations',
        ]);

        $location = new FreezerLocation();
        $location->name = $this->name;
        $location->description = $this->description ?? null;
        $location->save();
        $this->description = '';
        $this->name = '';
        $this->dispatchBrowserEvent('close-modal');
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Freezer Location created successfully!']);
    }

    public function editdata($id)
    {
        $location = FreezerLocation::where('id', $id)->first();
        $this->edit_id = $location->id;
        $this->name = $location->name;
        $this->description = $location->description;
        $this->is_active = $location->is_active;
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
        $location = FreezerLocation::find($this->edit_id);
        $location->name = $this->name;
        $location->description = $this->description ?? null;
        $location->is_active = $this->is_active;
        $location->update();

        $this->description = '';
        $this->name = '';
        $this->is_active = '';
        $this->dispatchBrowserEvent('close-modal');
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Freezer Location updated  successfully!']);
    }

    public function export()
    {
        return (new FreezerLocationsExport())->download('FreezerLocations.xlsx');
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
            $location = FreezerLocation::where('id', $this->delete_id)->first();
            $location->delete();
            $this->delete_id = '';
            $this->dispatchBrowserEvent('close-modal');
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Freezer Location deleted successfully!']);
        } catch(Exception $error) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => 'Freezer Location can not be deleted!']);
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
        $freezerLocations = FreezerLocation::search($this->search)
        ->orderBy($this->orderBy, $this->orderAsc ? 'asc' : 'desc')
        ->paginate($this->perPage);
        // $export=true;
        return view('livewire.lab.sample-storage.freezer-location-component', compact('freezerLocations'));
    }
}
