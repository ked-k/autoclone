<?php

namespace App\Http\Livewire\Lab\SampleStorage;

use App\Exports\FreezersExport;
use App\Models\Freezer;
use App\Models\FreezerLocation;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class FreezersComponent extends Component
{
    use WithPagination;

    public $perPage = 10;

    public $search = '';

    public $orderBy = 'type';

    public $orderAsc = true;

    public $freezer_location_id;

    public $name;

    public $type;

    public $temp;

    public $description;

    public $is_active;

    public $edit_id;

    public $delete_id;

    protected $paginationTheme = 'bootstrap';

    public $toggleForm = false;

    protected $validationAttributes = [
        'is_active' => 'status',
        'freezer_location_id' => 'Location',

    ];

    public function updated($fields)
    {
        $this->validateOnly($fields, [
            'freezer_location_id' => 'required',
            'name' => 'required|unique:freezers',
            'type' => 'required',
            'temp' => 'required',
            'is_active' => 'required',

        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function export()
    {
        return (new FreezersExport())->download('freezers.xlsx');
    }

    public function storeFreezer()
    {
        $this->validate([
            'freezer_location_id' => 'required',
            'name' => 'required|unique:freezers',
            'type' => 'required',
            'temp' => 'required',
            'is_active' => 'required',
        ]);

        $freezer = new Freezer();
        $freezer->name = $this->name;
        $freezer->type = $this->type;
        $freezer->temp = $this->temp;
        $freezer->description = $this->description;
        $freezer->freezer_location_id = $this->freezer_location_id;

        $freezer->save();
        $this->resetInputs();
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Freezer created successfully!']);
    }

    public function editFreezer(Freezer $freezer)
    {
        $this->edit_id = $freezer->id;
        $this->name = $freezer->name;
        $this->type = $freezer->type;
        $this->temp = $freezer->temp;
        $this->description = $freezer->description;
        $this->freezer_location_id = $freezer->freezer_location_id;
        $this->is_active = $freezer->is_active;
        $this->toggleForm = true;
    }

    public function refresh()
    {
        return redirect(request()->header('Referer'));
    }

    public function resetInputs()
    {
        $this->reset(['name', 'type', 'temp', 'description', 'is_active', 'freezer_location_id']);
    }

    public function updateFreezer()
    {
        $this->validate([
            'freezer_location_id' => 'required',
            'name' => 'required|unique:freezers,name,'.$this->edit_id.'',
            'type' => 'required',
            'temp' => 'required',
            'is_active' => 'required',
        ]);
        $freezer = Freezer::find($this->edit_id);
        $freezer->name = $this->name;
        $freezer->type = $this->type;
        $freezer->temp = $this->temp;
        $freezer->description = $this->description;
        $freezer->freezer_location_id = $this->freezer_location_id;
        $freezer->is_active = $this->is_active;

        $freezer->update();

        $this->toggleForm = false;
        $this->resetInputs();
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Freezer updated successfully!']);
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
            $value = Freezer::where('creator_lab', auth()->user()->laboratory_id)->where('id', $this->delete_id)->first();
            $value->delete();
            $this->delete_id = '';
            $this->dispatchBrowserEvent('close-modal');
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Record deleted successfully!']);
        } catch(Exception $error) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => 'Record can not be deleted!']);
        }
    }

    public function cancel()
    {
        $this->delete_id = '';
    }

    public function close()
    {
        $this->toggleForm = false;
        $this->resetInputs();
    }

    public function render()
    {
        $freezers = Freezer::search($this->search)->with('location')
        ->orderBy($this->orderBy, $this->orderAsc ? 'asc' : 'desc')
        ->paginate($this->perPage);

        $freezerLocations = FreezerLocation::select('id', 'name')->get();

        return view('livewire.lab.sample-storage.freezers-component', compact('freezers', 'freezerLocations'));
    }
}
