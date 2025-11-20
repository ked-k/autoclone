<?php

namespace App\Http\Livewire\Lab\Lists;

use App\Exports\ParticipantsExport;
use App\Models\Facility;
use App\Models\Participant;
use App\Models\Sample;
use App\Models\Study;
use Livewire\Component;
use Livewire\WithPagination;
use Ramsey\Uuid\Type\Integer;

class ParticipantListComponent extends Component
{
    use WithPagination;

    public $facility_id = 0;

    public $study_id = 0;

    public $entryType = '';

    public $from_date = '';

    public $to_date = '';

    public $perPage = 10;

    public $orderBy = 'id';

    public $orderAsc = 0;

    public $activeRow;

    public $export;

    public $studies;

    public $participantIds = [];
    
    public $search;

    public $toggleForm = false;

    public $filter = true;

     //PARTICIPANT INFORMATION
     public $identity;

     public $patient_id;
 
     public $age;
 
     public $gender;
 
     public $address;
 
     public $contact;
 
     public $nok_contact;
 
     public $nok_address;
 
     public $clinical_notes;

     public $entry_type;

     public $participant_id;
     //Optional participant fields
 
     public $title;
 
     public $nin_number;
 
     public $surname;
 
     public $first_name;
 
     public $other_name;
 
     public $dob;
 
     public $months;
 
     public $nationality;
 
     public $district;
 
     public $email;
 
     public $birth_place;
 
     public $religious_affiliation;
 
     public $occupation;
 
     public $civil_status;
 
     public $nok;
 
     public $nok_relationship;

    protected $paginationTheme = 'bootstrap';

    public function updatedFacilityId()
    {
        if ($this->facility_id != 0) {
            $this->studies = Study::whereIn('id', auth()->user()->laboratory->associated_studies ?? [])->where('facility_id', $this->facility_id)->get();
        }
    }

    public function mount()
    {
        $this->studies = collect([]);
    }

    public function export()
    {
        if (count($this->participantIds) > 0) {
            return (new ParticipantsExport($this->participantIds))->download('Participants_'.date('Y-m-d').'_'.now()->toTimeString().'.xlsx');
        } else {
            $this->dispatchBrowserEvent('not-found', ['type' => 'error',  'message' => 'Oops! No Participants selected for export!']);
        }
    }

    public function editParticipant(Participant $participant)
    {
        $this->entry_type = $participant->entry_type;
        $this->identity = $participant->identity;
        $this->age = $participant->age;
        $this->months = $participant->months;
        $this->address = $participant->address;
        $this->gender = $participant->gender;
        $this->contact = $participant->contact;
        $this->nok_contact = $participant->nok_contact;
        $this->nok_address = $participant->nok_address;
        $this->clinical_notes = $participant->clinical_notes;

        $this->title = $participant->title;
        $this->nin_number = $participant->nin_number;
        $this->surname = $participant->surname;
        $this->first_name = $participant->first_name;
        $this->other_name = $participant->other_name;
        $this->nationality = $participant->nationality;
        $this->district = $participant->district;
        $this->dob = $participant->dob;
        $this->birth_place = $participant->birth_place;
        $this->religious_affiliation = $participant->religious_affiliation;
        $this->occupation = $participant->occupation;
        $this->civil_status = $participant->civil_status;
        $this->email = $participant->email;
        $this->nok = $participant->nok;
        $this->nok_relationship = $participant->nok_relationship;
        $this->participant_id = $participant->id;
        $this->toggleForm = true;
        $this->filter = false;
    }

    public function close()
    {
        $this->reset(['age', 'months' ,'gender', 'contact', 'address',
                'nok_contact', 'nok_address', 'clinical_notes', 'title', 'nin_number', 'surname', 'first_name',
                'other_name', 'nationality', 'district', 'dob', 'email', 'birth_place', 'religious_affiliation',
                'occupation', 'civil_status', 'nok', 'nok_relationship', ]);
        $this->toggleForm = false;
        $this->filter = true;
    }

    public function updated($fields)
    {
        $this->validateOnly($fields, [
            'entry_type' => 'required|string',
            'age' => 'nullable|integer|min:1',
            'months' => 'nullable|integer|min:1|max:11',
            'address' => 'required|string|max:40',
            'gender' => 'nullable|string|max:6',
            'contact' => 'required|string',
            'nok_contact' => 'required|string',
            'nok_address' => 'required|string|max:40',
            'clinical_notes' => 'required|max:1000',
        ]);
    }
    public function updateParticipant()
    {
        if ($this->entry_type != 'Other') {
            if ($this->entry_type == 'Participant' || $this->entry_type == 'Client') {
                $this->validate([
                    'entry_type' => 'required|string',
                    'age' => 'nullable|integer|min:1',
                    'months' => 'nullable|integer|min:1|max:11',
                    'address' => 'required|string|max:40',
                    'gender' => 'nullable|string|max:6',
                    'contact' => 'required|string',
                    'nok_contact' => 'required|string',
                    'nok_address' => 'required|string|max:40',
                    'clinical_notes' => 'required|max:1000',
                ]);
            }

            if ($this->entry_type == 'Participant') {
                $this->validate([
                    'identity' => 'required|string',
                ]);
            }
        }
        $age = $this->age;
        if($this->age<1){
            $age = null;
        }
        $months = $this->months;
        if($this->months<1){
            $months = null;
        }
        // dd($age);
        $participant = Participant::find($this->participant_id);
        $participant->identity = $this->identity;
        $participant->age = $age??null;
        $participant->months = $months;
        $participant->address = $this->address;
        $participant->gender = $this->gender;
        $participant->contact = $this->contact;
        $participant->nok_contact = $this->nok_contact;
        $participant->nok_address = $this->nok_address;
        $participant->clinical_notes = $this->clinical_notes;

        $participant->title = $this->title;
        $participant->nin_number = $this->nin_number;
        $participant->surname = $this->surname;
        $participant->first_name = $this->first_name;
        $participant->other_name = $this->other_name;
        $participant->nationality = $this->nationality;
        $participant->district = $this->district;
        $participant->dob = $this->dob;
        $participant->birth_place = $this->birth_place;
        $participant->religious_affiliation = $this->religious_affiliation;
        $participant->occupation = $this->occupation;
        $participant->civil_status = $this->civil_status;
        $participant->email = $this->email;
        $participant->nok = $this->nok;
        $participant->nok_relationship = $this->nok_relationship;
        // dd($participant);
        $participant->update();

        $this->participant_id = $participant->id;
        $this->toggleForm = false;        
        $this->filter = false;
        $this->close();
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Participant Data updated successfully!']);
    }

    public function filterParticipants()
    {
        $participantList = Sample::select('participant_id')->where('creator_lab', auth()->user()->laboratory_id)->distinct()->get()->pluck('participant_id')->toArray();
        $participants = Participant::search($this->search)->select('*')
        ->whereIn('id', $participantList ?? [])
        // ->withCount(['sample', 'testResult'])
        ->with('facility', 'study')
                    ->when($this->facility_id != 0, function ($query) {
                        $query->where('facility_id', $this->facility_id);
                    }, function ($query) {
                        return $query;
                    })
                    ->when($this->study_id != 0, function ($query) {
                        $query->where('study_id', $this->study_id);
                    }, function ($query) {
                        return $query;
                    })
                    ->when($this->entryType != '', function ($query) {
                        $query->where('entry_type', $this->entryType);
                    }, function ($query) {
                        return $query;
                    })
                    ->when($this->from_date != '' && $this->to_date != '', function ($query) {
                        $query->whereBetween('created_at', [$this->from_date, $this->to_date]);
                    }, function ($query) {
                        return $query;
                    });

        $this->participantIds = $participants->pluck('id')->toArray();

        return $participants;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function refresh()
    {
        return redirect(request()->header('Referer'));
    }

    public function render()
    {
        $entryTypes = Participant::select('entry_type')->distinct()->get();
        $facilities = Facility::whereIn('id', auth()->user()->laboratory->associated_facilities ?? [])->get();
        $participants = $this->filterParticipants()->orderBy($this->orderBy, $this->orderAsc ? 'asc' : 'desc')
        ->paginate($this->perPage);

        return view('livewire.lab.lists.participant-list-component', compact('entryTypes', 'participants', 'facilities'));
    }
}
