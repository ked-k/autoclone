<?php

namespace App\Http\Livewire\Admin;

use App\Exports\RequestersExport;
use App\Models\Facility;
use App\Models\Requester;
use App\Models\Study;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class RequesterComponent extends Component
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
        return (new RequestersExport())->download('requesters.xlsx');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    protected $validationAttributes = [
        'facility_id' => 'facility',
        'study_id' => 'study',
        'is_active' => 'status',
    ];

    public function updated($fields)
    {
        $this->validateOnly($fields, [
            'name' => 'required',
            'contact' => 'required',
            'email' => 'required|email:filter',
            'facility_id' => 'required',
            'study_id' => 'required|unique:requesters',
            'is_active' => 'required',

        ]);
    }

    public function getStudies()
    {
        $this->studies = Study::where('creator_lab', auth()->user()->laboratory_id)->where('facility_id', $this->facility_id)->latest()->get();
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
            // 'email' => 'required|unique:requesters|email:filter',
            'email' => 'required|email:filter',
            'facility_id' => 'required',
            'study_id' => 'required|unique:requesters',
            'is_active' => 'required',
        ]);

        $requester = new Requester();
        $requester->name = $this->name;
        $requester->contact = $this->contact;
        $requester->email = $this->email;
        $requester->facility_id = $this->facility_id;
        $requester->study_id = $this->study_id == '' ? null : $this->study_id;
        $requester->save();

        $this->reset(['name', 'contact', 'facility_id', 'email', 'is_active', 'study_id']);
        $this->dispatchBrowserEvent('close-modal');
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Requester created successfully!']);
    }

    public function editdata($id)
    {
        $requester = Requester::where('id', $id)->first();
        $this->edit_id = $requester->id;
        $this->name = $requester->name;
        $this->contact = $requester->contact;
        $this->email = $requester->email;
        $this->facility_id = $requester->facility_id;
        $this->study_id = $requester->study_id;
        $this->is_active = $requester->is_active;

        $this->studies = Study::where('facility_id', $requester->facility_id)->latest()->get();

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
        $requester = Requester::find($this->edit_id);
        $requester->name = $this->name;
        $requester->contact = $this->contact;
        $requester->email = $this->email;
        $requester->facility_id = $this->facility_id;
        $requester->study_id = $this->study_id == '' ? null : $this->study_id;
        $requester->is_active = $this->is_active;
        $requester->update();

        if ($this->is_active == 0) {
            Study::where('id', $requester->study_id)->update(['is_active' => $this->is_active]);
        }

        $this->reset(['name', 'contact', 'facility_id', 'email', 'is_active', 'study_id']);
        $this->dispatchBrowserEvent('close-modal');
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Requester updated successfully!']);
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
            $requester = Requester::where('creator_lab', auth()->user()->laboratory_id)->where('id', $this->delete_id)->first();
            $requester->delete();
            $this->delete_id = '';
            $this->dispatchBrowserEvent('close-modal');
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Requester deleted successfully!']);
        } catch(Exception $error) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => 'Requester can not be deleted!']);
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
        $requesters = Requester::search($this->search)
        ->whereIn('study_id', auth()->user()->laboratory->associated_studies ?? [])->with('facility', 'study')
        ->orderBy($this->orderBy, $this->orderAsc ? 'asc' : 'desc')
        ->paginate($this->perPage);
        $facilities = Facility::whereIn('id', auth()->user()->laboratory->associated_facilities ?? [])->latest()->get();

        return view('livewire.admin.requester-component', compact('requesters', 'facilities'))->layout('layouts.app');
    }
}
