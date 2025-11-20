<?php

namespace App\Http\Livewire\Admin;

use App\Exports\FacilitiesExport;
use App\Models\Collector;
use App\Models\Courier;
use App\Models\Facility;
use App\Models\Laboratory;
use App\Models\Requester;
use App\Models\SampleReception;
use App\Models\Study;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class FacilityComponent extends Component
{
    use WithPagination;

    public $perPage = 10;

    public $search = '';

    public $orderBy = 'name';

    public $orderAsc = true;

    public $name;

    public $type;

    public $parent_id;

    public $description;

    public $associated_facilities;

    public $associated_studies=[];

    public $target_facility_id;

    public $is_active;

    public $delete_id;
    public $edit_id;
    public $selected_facility;

    public $exportIds = [];
    // public $count=0;

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
            'name' => 'required|unique:facilities',
            'type' => 'required',
            'is_active' => 'required',
        ]);
    }

    public function mount()
    {
        $this->associated_facilities = auth()->user()->laboratory->associated_facilities ?? [];
    }

    public function storeData()
    {
        $this->validate([
            'name' => 'required|unique:facilities',
            'type' => 'required',
            'is_active' => 'required',
        ]);

        $facility = new Facility();
        $facility->name = $this->name;
        $facility->type = $this->type;
        $facility->parent_id = $this->parent_id;
        $facility->save();

        array_push($this->associated_facilities, $facility->id);
        $lab = Laboratory::findOrfail(auth()->user()->laboratory_id);
        $lab->associated_facilities = $this->associated_facilities;
        $lab->update();

        $this->reset(['name', 'type', 'parent_id', 'is_active']);
        $this->dispatchBrowserEvent('close-modal');
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Facility and association to Lab created successfully!']);
    }

    public function associateFacility()
    {
        $this->validate([
            'associated_facilities' => 'required',
        ]);
        $associatedFacilites = auth()->user()->laboratory->associated_facilities ?? [];
        $disassociatedFacilities = array_diff($associatedFacilites, $this->associated_facilities ?? []);

        $facilityData = [];
        foreach ($disassociatedFacilities as $facility) {
            if (SampleReception::where(['facility_id' => $facility, 'creator_lab' => auth()->user()->laboratory_id])->first()) {
                array_push($facilityData, $facility);
            }
        }
        if (count($facilityData)) {
            $this->associated_facilities = $associatedFacilites;
            $this->dispatchBrowserEvent('mismatch', ['type' => 'error',  'message' => 'Oops! You can not disassociate from facilities that already have sample information recorded!']);
        } else {
            $laboratory = Laboratory::findorFail(auth()->user()->laboratory_id);
            $laboratory->associated_facilities = $this->associated_facilities ?? [];
            $laboratory->update();
            $this->render();

            $this->dispatchBrowserEvent('close-modal');
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Laboratory Information successfully updated!']);
        }
    }

    public function updatedTargetFacilityId()
    {
        if ($this->target_facility_id) {
          $this->selected_facility =  $facility = Facility::where('id',$this->target_facility_id)->first();
            $this->associated_studies = array_unique(array_merge($this->associated_studies, $facility->associated_studies??[]));
        }
    }

    public function associateStudiesToFacility()
    {
        $this->validate([
            'associated_studies' => 'required',
        ]);

        if ($this->target_facility_id) {
            // dd($this->associated_studies);
            $facility = Facility::findOrFail($this->target_facility_id);
            $facility->update([
                'associated_studies'=>array_unique(array_merge($this->associated_studies, $facility->associated_studies??[])),
            ]);
            // dd($facility);
            $this->dispatchBrowserEvent('close-modal');
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Facility Information successfully updated!']);
        }

        $this->resetInputs();
    }

    public function refresh()
    {
        return redirect(request()->header('Referer'));
    }

    public function editdata($id)
    {
        $facility = Facility::where('id', $id)->first();
        $this->edit_id = $facility->id;
        $this->name = $facility->name;
        $this->type = $facility->type;
        $this->parent_id = $facility->parent_id != null ? $facility->parent_id : '';
        $this->is_active = $facility->is_active;
        $this->dispatchBrowserEvent('edit-modal');
    }

    public function resetInputs()
    {
        $this->reset(['name', 'type', 'parent_id', 'is_active','target_facility_id']);
        $this->associated_studies=[];
    }

    public function updateData()
    {
        $this->validate([
            'name' => 'required',
        ]);
        $facility = Facility::find($this->edit_id);
        $facility->name = $this->name;
        $facility->type = $this->type;
        $facility->parent_id = $this->parent_id != '' ? $this->parent_id : null;

        if ($facility->is_active == $this->is_active) {
            $facility->update();
        } else {
            $facility->is_active = $this->is_active;
            $facility->update();
            Study::where('facility_id', $this->edit_id)->update(['is_active' => $this->is_active]);
            Requester::where('facility_id', $this->edit_id)->update(['is_active' => $this->is_active]);
            Collector::where('facility_id', $this->edit_id)->update(['is_active' => $this->is_active]);
            Courier::where('facility_id', $this->edit_id)->update(['is_active' => $this->is_active]);
        }

        $this->reset(['name', 'type', 'parent_id', 'is_active']);
        $this->dispatchBrowserEvent('close-modal');
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Facility updated successfully!']);
    }

    public function export()
    {
        return (new FacilitiesExport())->download('facilities.xlsx');
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
            $facility = Facility::where('creator_lab', auth()->user()->laboratory_id)->where('id', $this->delete_id)->first();
            $facility->delete();
            $this->delete_id = '';
            $this->dispatchBrowserEvent('close-modal');
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Facility deleted successfully!']);
        } catch(Exception $error) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => 'Facility can not be deleted!']);
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
        $facilities = Facility::search($this->search)->with('parent')
        ->orderBy($this->orderBy, $this->orderAsc ? 'asc' : 'desc')
        ->paginate($this->perPage);

        $studies=Study::where('is_active', true)->get();

        return view('livewire.admin.facility-component', compact('facilities','studies'))->layout('layouts.app');
    }
}
