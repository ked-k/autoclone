<?php

namespace App\Http\Livewire\Admin;

use App\Exports\CouriersExport;
use App\Models\Courier;
use App\Models\Facility;
use App\Models\Study;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class CourierComponent extends Component
{
    use WithPagination;

    public $perPage = 10;

    public $search = '';

    public $orderBy = 'name';

    public $orderAsc = true;

    public $name;

    public $email;

    public $facility_id;

    public $contact;

    public $is_active;

    public $delete_id;

    public $studies;

    public $study_id;

    protected $paginationTheme = 'bootstrap';

    public function export()
    {
        return (new CouriersExport())->download('couriers.xlsx');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    protected $validationAttributes = [
        'facility_id' => 'facility',
        'is_active' => 'status',
    ];

    public function updated($fields)
    {
        $this->validateOnly($fields, [
            'name' => 'required',
            'contact' => 'required',
            'email' => 'required|email:filter',
            'facility_id' => 'required',
            'is_active' => 'required',

        ]);
    }

    public function getStudies()
    {
        $this->studies = Study::where('facility_id', $this->facility_id)->latest()->get();
    }

    public function mount()
    {
        $this->studies = collect();
    }

    public function storeData()
    {
        $this->validate([
            'name' => 'required',
            'contact' => 'required',
            'email' => 'required|unique:couriers|email:filter',
            'facility_id' => 'required',
            'is_active' => 'required',
        ]);

        $courier = new Courier();
        $courier->name = $this->name;
        $courier->contact = $this->contact;
        $courier->email = $this->email;
        $courier->facility_id = $this->facility_id;
        $courier->study_id = $this->study_id == '' ? null : $this->study_id;
        $courier->save();

        $this->reset(['name', 'contact', 'facility_id', 'email', 'is_active', 'study_id']);
        $this->dispatchBrowserEvent('close-modal');
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Courier created successfully!']);
    }

    public function editdata($id)
    {
        $courier = Courier::where('id', $id)->first();
        $this->edit_id = $courier->id;
        $this->name = $courier->name;
        $this->contact = $courier->contact;
        $this->email = $courier->email;
        $this->facility_id = $courier->facility_id;
        $this->study_id = $courier->study_id;
        $this->is_active = $courier->is_active;

        $this->studies = Study::where('facility_id', $courier->facility_id)->latest()->get();

        $this->dispatchBrowserEvent('edit-modal');
    }

    public function resetInputs()
    {
        $this->reset(['name', 'contact', 'facility_id', 'email', 'is_active', 'study_id']);
    }

    public function updateData()
    {
        $this->validate([
            'name' => 'required',
            'contact' => 'required',
            'email' => 'required|email:filter',
            'facility_id' => 'required',
            'is_active' => 'required',
        ]);
        $courier = Courier::find($this->edit_id);
        $courier->name = $this->name;
        $courier->contact = $this->contact;
        $courier->email = $this->email;
        $courier->facility_id = $this->facility_id;
        $courier->study_id = $this->study_id == '' ? null : $this->study_id;
        $courier->is_active = $this->is_active;
        $courier->update();

        $this->reset(['name', 'contact', 'facility_id', 'email', 'is_active', 'study_id']);
        $this->dispatchBrowserEvent('close-modal');
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Courier updated successfully!']);
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
            $courier = Courier::where('creator_lab', auth()->user()->laboratory_id)->where('id', $this->delete_id)->first();
            $courier->delete();
            $this->delete_id = '';
            $this->dispatchBrowserEvent('close-modal');
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Courier deleted successfully!']);
        } catch(Exception $error) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => 'Courier can not be deleted!']);
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
        $couriers = Courier::search($this->search)
        ->whereIn('facility_id', auth()->user()->laboratory->associated_facilities ?? [])->with('facility', 'study')
        ->orderBy($this->orderBy, $this->orderAsc ? 'asc' : 'desc')
        ->paginate($this->perPage);
        $facilities = Facility::whereIn('id', auth()->user()->laboratory->associated_facilities ?? [])->latest()->get();

        return view('livewire.admin.courier-component', compact('couriers', 'facilities'))->layout('layouts.app');
    }
}
