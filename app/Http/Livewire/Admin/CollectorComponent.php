<?php

namespace App\Http\Livewire\Admin;

use App\Exports\CollectorsExport;
use App\Models\Collector;
use App\Models\Facility;
use App\Models\Study;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class CollectorComponent extends Component
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
        return (new CollectorsExport())->download('sample_collectors.xlsx');
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
            'email' => 'required|unique:collectors|email:filter',
            'facility_id' => 'required',
            'is_active' => 'required',
        ]);

        $collector = new Collector();
        $collector->name = $this->name;
        $collector->contact = $this->contact;
        $collector->email = $this->email;
        $collector->facility_id = $this->facility_id;
        $collector->study_id = $this->study_id == '' ? null : $this->study_id;

        $collector->save();
        $this->reset(['name', 'contact', 'facility_id', 'email', 'is_active', 'study_id']);
        $this->dispatchBrowserEvent('close-modal');
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Collector created successfully!']);
    }

    public function editdata($id)
    {
        $collector = Collector::where('id', $id)->first();
        $this->edit_id = $collector->id;
        $this->name = $collector->name;
        $this->contact = $collector->contact;
        $this->email = $collector->email;
        $this->facility_id = $collector->facility_id;
        $this->study_id = $collector->study_id;
        $this->is_active = $collector->is_active;

        $this->studies = Study::where('facility_id', $collector->facility_id)->latest()->get();

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
        $collector = Collector::find($this->edit_id);
        $collector->name = $this->name;
        $collector->contact = $this->contact;
        $collector->email = $this->email;
        $collector->facility_id = $this->facility_id;
        $collector->study_id = $this->study_id == '' ? null : $this->study_id;
        $collector->is_active = $this->is_active;
        $collector->update();

        $this->reset(['name', 'contact', 'facility_id', 'email', 'is_active', 'study_id']);
        $this->dispatchBrowserEvent('close-modal');
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Collector updated successfully!']);
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
            $collector = Collector::where('creator_lab', auth()->user()->laboratory_id)->where('id', $this->delete_id)->first();
            $collector->delete();
            $this->delete_id = '';
            $this->dispatchBrowserEvent('close-modal');
            session()->flash('success', 'Collector deleted successfully.');
        } catch(Exception $error) {
            session()->flash('erorr', 'Collector can not be deleted !!.');
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
        $collectors = Collector::search($this->search)
        ->whereIn('facility_id', auth()->user()->laboratory->associated_facilities ?? [])->with('facility', 'study')
        ->orderBy($this->orderBy, $this->orderAsc ? 'asc' : 'desc')
        ->paginate($this->perPage);
        $facilities = Facility::whereIn('id', auth()->user()->laboratory->associated_facilities ?? [])->latest()->get();

        return view('livewire.admin.collector-component', compact('collectors', 'facilities'))->layout('layouts.app');
    }
}
