<?php

namespace App\Http\Livewire\Admin;

use App\Exports\StudiesExport;
use App\Models\Facility;
use App\Models\Laboratory;
use App\Models\Requester;
use App\Models\Sample;
use App\Models\Study;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class StudyComponent extends Component
{
    use WithPagination;

    public $perPage = 10;

    public $search = '';

    public $orderBy = 'name';

    public $orderAsc = true;

    public $name;

    public $description;

    public $facility_id;

    public $associated_studies;

    public $is_active;

    public $delete_id;

    public $edit_id;

    protected $paginationTheme = 'bootstrap';

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
            'name' => 'required|unique:studies',
            'facility_id' => 'required',
            'is_active' => 'required',

        ]);
    }

    public function mount()
    {
        $this->associated_studies = auth()->user()->laboratory->associated_studies ?? [];
    }

    public function storeData()
    {
        $this->validate([
            'name' => 'required|unique:studies',
            'facility_id' => 'required',
            'is_active' => 'required',
        ]);

        $study = new Study();
        $study->name = $this->name;
        $study->description = $this->description;
        $study->facility_id = $this->facility_id;
        $study->save();

        array_push($this->associated_studies, $study->id);
        $lab = Laboratory::findOrfail(auth()->user()->laboratory_id);
        $lab->associated_studies = $this->associated_studies;
        $lab->update();

        $this->reset(['name', 'description', 'facility_id', 'is_active']);
        $this->dispatchBrowserEvent('close-modal');
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Study/Project and association to Lab successfully!']);
    }

    public function editdata($id)
    {
        $study = Study::where('id', $id)->first();
        $this->edit_id = $study->id;
        $this->name = $study->name;
        $this->description = $study->description;
        $this->facility_id = $study->facility_id;
        $this->is_active = $study->is_active;
        $this->dispatchBrowserEvent('edit-modal');
    }

    public function resetInputs()
    {
        $this->reset(['name', 'description', 'facility_id', 'is_active']);
    }

    public function updateData()
    {
        $this->validate([
            'name' => 'required',
        ]);
        $study = Study::find($this->edit_id);
        $study->name = $this->name;
        $study->description = $this->description;
        $study->facility_id = $this->facility_id;

        if ($study->is_active == $this->is_active) {
            $study->update();
        } else {
            $study->is_active = $this->is_active;
            $study->update();
            Requester::where('study_id', $this->edit_id)->update(['is_active' => $this->is_active]);
        }

        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Study/Project updated successfully!']);
        $this->reset(['name', 'description', 'facility_id', 'is_active']);
        $this->dispatchBrowserEvent('close-modal');
    }

    public function associateStudy()
    {
        $this->validate([
            'associated_studies' => 'required',
        ]);

        $associatedStudies = auth()->user()->laboratory->associated_studies ?? [];
        $disassociatedStudies = array_diff($associatedStudies, $this->associated_studies ?? []);

        $studyData = [];
        foreach ($disassociatedStudies as $study) {
            if (Sample::where(['study_id' => $study, 'creator_lab' => auth()->user()->laboratory_id])->first()) {
                array_push($studyData, $study);
            }
        }
        if (count($studyData)) {
            $this->associated_studies = $associatedStudies;
            $this->dispatchBrowserEvent('mismatch', ['type' => 'error',  'message' => 'Oops! You can not disassociate from studies that already have sample information recorded!']);
        } else {
            $laboratory = Laboratory::find(auth()->user()->laboratory_id);
            $laboratory->associated_studies = $this->associated_studies ?? [];
            $laboratory->update();
            $this->render();

            $this->dispatchBrowserEvent('close-modal');
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Laboratory Information successfully updated!']);
        }
    }

    public function export()
    {
        return (new StudiesExport())->download('studies.xlsx');
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
            $study = Study::where('creator_lab', auth()->user()->laboratory_id)->where('id', $this->delete_id)->first();
            $study->delete();
            $this->delete_id = '';
            $this->dispatchBrowserEvent('close-modal');
            session()->flash('success', 'Study/Project deleted successfully.');
        } catch(Exception $error) {
            session()->flash('erorr', 'Study/Project can not be deleted.');
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
        $studies = Study::search($this->search)
        ->with('facility')->whereIn('facility_id', auth()->user()->laboratory->associated_facilities ?? [])
        ->orderBy($this->orderBy, $this->orderAsc ? 'asc' : 'desc')
        ->paginate($this->perPage);
        $facilities = Facility::whereIn('id', auth()->user()->laboratory->associated_facilities ?? [])->where('is_active', 1)->latest()->get();

        return view('livewire.admin.study-component', compact('studies', 'facilities'))->layout('layouts.app');
    }
}
