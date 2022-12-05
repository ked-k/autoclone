<?php

namespace App\Http\Livewire\Lab\SampleManagement;

use App\Helpers\Generate;
use App\Models\Admin\Test;
use App\Models\Collector;
use App\Models\Participant;
use App\Models\Requester;
use App\Models\Sample;
use App\Models\SampleReception;
use App\Models\SampleType;
use App\Models\Study;
use Exception;
use Livewire\Component;

class SpecimenRequestComponent extends Component
{
    //SPECIAL VARIABLES
    public $batch_no;

    public $facility_id;

    public $source_facility;

    public $batch_sample_count;

    public $batch_samples_handled;

    public $sample_reception_id;

    public $date_delivered;

    public $participant_id;

    public $entry_type;

    public $sample_id;

    public $activeParticipantTab = true;

    public $same_participant;

    public $same_participant_id;

    public $same_requested_by;

    public $same_study_id;

    public $same_collected_by;

    public $delete_id;

    public $toggleForm = false;

    public $tabToggleBtn = false;

    public $participantMatch = false;

    public $matched_participant_id;

    public $lastVisit;

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

    //Optional participant fields

    public $title;

    public $nin_number;

    public $surname;

    public $first_name;

    public $other_name;

    public $dob;

    public $nationality;

    public $district;

    public $email;

    public $birth_place;

    public $religious_affiliation;

    public $occupation;

    public $civil_status;

    public $nok;

    public $nok_relationship;

    //SAMPLE INFORMATION
    public $requested_by;

    public $date_requested;

    public $is_isolate;

    public $collected_by;

    public $date_collected;

    public $study_id;

    public $sample_identity;

    public $sample_is_for;

    public $priority;

    public $sample_type_id;

    public $visit;

    public $volume;

    public $tests_requested = [];

    public $tests;

    protected $validationAttributes = [
        'study_id' => 'study',
        'sample_type_id' => 'sample_type',

    ];

    public function updated($fields)
    {
        $this->validateOnly($fields, [

            'identity' => 'required|string',
            'age' => 'required|integer|min:1',
            'address' => 'required|string|max:40',
            'gender' => 'required|string|max:6',
            'contact' => 'required|string',
            'nok_contact' => 'required|string',
            'nok_address' => 'required|string|max:40',
            'clinical_notes' => 'string|required',
        ]);
    }

    public function updatedEntryType()
    {
        $this->resetParticipantInputs();
    }

    public function updatedSampleTypeId()
    {
        $this->reset(['tests_requested', 'tests']);
        $sampleType = SampleType::where('id', $this->sample_type_id)->first();
        sleep(1);
        $this->tests = Test::whereIn('id', (array) $sampleType->possible_tests)->orderBy('name', 'asc')->get();
    }

    public function updatedRequestedBy()
    {
        $requester = Requester::findOrFail($this->requested_by);
        $this->reset(['study_id']);
        $this->study_id = $requester->study_id;
    }

    public function updatedStudyId()
    {
        $participant = Participant::where('id', $this->participant_id)->orWhere('id', $this->same_participant_id)->first();
        // dd($participant);
        if ($participant->study_id != null && $this->study_id != $participant->study_id) {
            $this->dispatchBrowserEvent('mismatch', ['type' => 'error',  'message' => 'Oops! You have supplied a study to which the participant does not belong!']);
        }
    }

    public function updatedSameParticipant()
    {
        if ($this->same_participant) {
            $this->activeParticipantTab = false;
            $this->resetParticipantInputs();
        } else {
            $this->reset(['participant_id', 'same_participant_id', 'same_participant', 'same_requested_by', 'same_study_id', 'same_collected_by', 'requested_by', 'study_id', 'collected_by']);
            $this->activeParticipantTab = true;
        }
    }

    public function updatedIdentity()
    {
        $participant = Participant::where(['identity' => $this->identity, 'facility_id' => $this->facility_id])->whereIn('study_id', auth()->user()->laboratory->associated_studies)->orWhere(function ($query) {
            $query->where(['identity' => $this->identity, 'facility_id' => $this->facility_id, 'creator_lab' => auth()->user()->laboratory_id]);
        })->first();

        if ($participant) {
            $lastSampleEntry = Sample::where(['participant_id' => $participant->id, 'creator_lab' => auth()->user()->laboratory_id])->latest()->first();
            $this->lastVisit = $lastSampleEntry->visit ?? null;
            $this->participantMatch = true;
            $this->matched_participant_id = $participant->id;
            // $this->entry_type = $participant->entry_type;
            $this->identity = $participant->identity;
            $this->age = $participant->age;
            $this->address = $participant->address;
            $this->gender = $participant->gender;
            $this->contact = $participant->contact;
            $this->nok_contact = $participant->nok_contact;
            $this->nok_address = $participant->nok_address;
            $this->clinical_notes = $participant->clinical_notes;
        } else {
            $this->reset(['age', 'gender', 'contact', 'address',
                'nok_contact', 'nok_address', 'clinical_notes', 'title', 'nin_number', 'surname', 'first_name',
                'other_name', 'nationality', 'district', 'dob', 'email', 'birth_place', 'religious_affiliation',
                'occupation', 'civil_status', 'nok', 'nok_relationship', ]);
            $this->participantMatch = false;
        }
    }

    public function toggleTab()
    {
        $this->activeParticipantTab = ! $this->activeParticipantTab;
    }

    public function mount($batch)
    {
        $sampleReception = SampleReception::where(['batch_no' => $batch, 'creator_lab' => auth()->user()->laboratory_id])->with('facility')->first();
        if ($sampleReception) {
            $this->batch_no = $sampleReception->batch_no;
            $this->sample_reception_id = $sampleReception->id;
            $this->batch_sample_count = $sampleReception->samples_accepted;
            $this->batch_samples_handled = $sampleReception->samples_handled;
            $this->facility_id = $sampleReception->facility_id;
            $this->source_facility = $sampleReception->facility->name;
            $this->date_delivered = $sampleReception->date_delivered;

            $this->tests = collect([]);
            $this->entry_type = 'Participant';

            if ($this->batch_sample_count == $this->batch_samples_handled) {
                $this->activeParticipantTab = true;
                $this->tabToggleBtn = true;
            }
        } else {
            redirect()->route('samplereception');
        }
    }

    public function storeParticipant()
    {
        if ($this->batch_sample_count == $this->batch_samples_handled) {
            $this->activeParticipantTab = true;
            $this->tabToggleBtn = true;

            $this->resetParticipantInputs();
            $this->dispatchBrowserEvent('maximum-reached', ['type' => 'warning',  'message' => 'Oops! Sample maximum already reached for this batch!']);
        } else {
            if ($this->entry_type != 'Other') {
                if ($this->entry_type == 'Participant' || $this->entry_type == 'Client') {
                    $this->validate([
                        'entry_type' => 'required|string',
                        'age' => 'required|integer|min:1',
                        'address' => 'required|string|max:40',
                        'gender' => 'required|string|max:6',
                        'contact' => 'required|string',
                        'nok_contact' => 'required|string',
                        'nok_address' => 'required|string|max:40',
                        'clinical_notes' => 'required|max:1000',
                    ]);
                }

                if ($this->entry_type == 'Participant') {
                    $this->validate([
                        'identity' => 'required|string|unique:participants',
                    ]);
                }
            }
            $participant = new Participant();
            $patNo = Generate::participantNo();
            $participant->participant_no = $patNo;
            if ($this->entry_type == 'Other' || $this->entry_type == 'Client') {
                $participant->identity = $patNo;
            } else {
                $participant->identity = $this->identity;
            }
            $participant->age = $this->age ?? null;
            $participant->address = $this->address ?? null;
            $participant->gender = $this->gender ?? null;
            $participant->contact = $this->contact ?? null;
            $participant->nok_contact = $this->nok_contact ?? null;
            $participant->nok_address = $this->nok_address ?? null;
            $participant->clinical_notes = $this->clinical_notes ?? null;

            $participant->title = $this->title ?? null;
            $participant->nin_number = $this->nin_number ?? null;
            $participant->surname = $this->surname ?? null;
            $participant->first_name = $this->first_name ?? null;
            $participant->other_name = $this->other_name ?? null;
            $participant->nationality = $this->nationality ?? null;
            $participant->district = $this->district ?? null;
            $participant->dob = $this->dob ?? null;
            $participant->birth_place = $this->birth_place ?? null;
            $participant->religious_affiliation = $this->religious_affiliation ?? null;
            $participant->occupation = $this->occupation ?? null;
            $participant->civil_status = $this->civil_status ?? null;
            $participant->email = $this->email ?? null;
            $participant->nok = $this->nok ?? null;
            $participant->nok_relationship = $this->nok_relationship ?? null;
            $participant->facility_id = $this->facility_id;
            $participant->entry_type = $this->entry_type;

            $participant->save();

            $this->participant_id = $participant->id;
            $this->entry_type = $participant->entry_type;
            $this->activeParticipantTab = false;
            $this->resetParticipantInputs();
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Participant Data Recorded successfully!']);
        }
    }

    public function editParticipant(Participant $participant)
    {
        $this->entry_type = $participant->entry_type;
        $this->identity = $participant->identity;
        $this->age = $participant->age;
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
        $this->activeParticipantTab = true;
    }

    public function updateParticipant()
    {
        if ($this->entry_type != 'Other') {
            if ($this->entry_type == 'Participant' || $this->entry_type == 'Client') {
                $this->validate([
                    'entry_type' => 'required|string',
                    'age' => 'required|integer|min:1',
                    'address' => 'required|string|max:40',
                    'gender' => 'required|string|max:6',
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

        $participant = Participant::find($this->participant_id);
        $participant->identity = $this->identity;
        $participant->age = $this->age;
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
        $participant->update();

        $this->participant_id = $participant->id;
        $this->toggleForm = false;
        $this->resetParticipantInputs();
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Participant Data updated successfully!']);
    }

    public function setParticipantId(Participant $participant)
    {
        if ($participant->entry_type == 'Participant') {
            $this->participant_id = $participant->id;
            $this->entry_type = $participant->entry_type;
            $this->study_id = $participant->study_id ?? '';
            $this->requested_by = $participant->study ? $participant->study->requester->id : '';
            $this->activeParticipantTab = false;
        }
    }

    public function storeSampleInformation()
    {
        if ($this->batch_sample_count == $this->batch_samples_handled) {
            $this->activeParticipantTab = true;
            $this->tabToggleBtn = true;
            $this->resetParticipantInputs();
            $this->resetSampleInformationInputs();
            $this->dispatchBrowserEvent('maximum-reached', ['type' => 'warning',  'message' => 'Oops! Sample maximum already reached for this batch!']);
        } else {
            if ($this->same_participant && $this->participant_id) {
                //just save sample information
                $this->saveSampleInformation();
                $this->resetSampleInformationInputs();
                $this->tests = collect([]);
                $this->requested_by = $this->same_requested_by;
                $this->study_id = $this->same_study_id;
                $this->collected_by = $this->same_collected_by;
                $this->activeParticipantTab = false;
                $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Sample Request Data Recorded successfully!']);
            } elseif ($this->same_participant && ! $this->participant_id) {
                //set participant id and save sample information
                $this->participant_id = $this->same_participant_id;
                $this->saveSampleInformation();
                $this->resetParticipantInputs();
                $this->resetSampleInformationInputs();
                $this->tests = collect([]);
                $this->requested_by = $this->same_requested_by;
                $this->study_id = $this->same_study_id;
                $this->collected_by = $this->same_collected_by;
                $this->activeParticipantTab = false;
                $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Sample Request Data Recorded successfully!']);
            } elseif (! $this->same_participant && $this->participant_id) {
                //just save sample information but return to participant tab
                $this->saveSampleInformation();
                $this->resetParticipantInputs();
                $this->resetSampleInformationInputs();
                $this->tests = collect([]);
                $this->reset(['same_participant_id', 'same_participant', 'same_requested_by', 'same_study_id', 'same_collected_by']);
                $this->activeParticipantTab = true;
                $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Sample Request Data Recorded successfully!']);
            } else {
                //return to participant tab
                $this->tests = collect([]);
                $this->reset(['same_participant_id', 'same_participant', 'same_requested_by', 'same_study_id', 'same_collected_by']);
                $this->activeParticipantTab = true;
            }
        }
    }

    public function saveSampleInformation()
    {
        $this->validate([
            'requested_by' => 'required|integer',
            'date_requested' => 'required|date|before_or_equal:'.date('Y-m-d', strtotime($this->date_delivered)),
            'sample_identity' => 'required|string|unique:samples',
            'sample_is_for' => 'required|string',
            'priority' => 'required|string',
            'sample_type_id' => 'integer|required',
            'tests_requested' => 'array|required',
        ]);

        if (! $this->is_isolate) {
            $this->validate([
                'collected_by' => 'required|integer',
                'date_collected' => 'required|date|before_or_equal:'.date('Y-m-d H:i', strtotime($this->date_delivered)).'|before_or_equal:'.date('Y-m-d 23:59', strtotime($this->date_requested)),
            ]);
        }
        if ($this->entry_type != 'Client') {
            $this->validate([
                'study_id' => 'required|integer',
            ]);
        }

        $sample = new Sample();
        $sample->sample_reception_id = $this->sample_reception_id;
        $sample->participant_id = $this->participant_id;
        $sample->visit = $this->visit;
        $sample->sample_type_id = $this->sample_type_id;
        $sample->sample_no = Generate::sampleNo();
        $sample->lab_no = Generate::labNo();
        $sample->volume = $this->volume;
        $sample->requested_by = $this->requested_by;
        $sample->date_requested = $this->date_requested;
        $sample->collected_by = $this->collected_by;
        $sample->date_collected = $this->date_collected;
        $sample->study_id = $this->study_id ?? null;
        $sample->sample_identity = $this->sample_identity;
        $sample->sample_is_for = $this->sample_is_for;
        $sample->priority = $this->priority;
        $sample->tests_requested = $this->tests_requested;
        $sample->test_count = count($this->tests_requested);
        $sample->status = 'Accessioned';
        $sample->is_isolate = $this->is_isolate;
        $sample->save();

        $this->same_participant_id = $sample->participant_id;
        $this->same_requested_by = $sample->requested_by;
        $this->same_study_id = $sample->study_id;
        $this->same_collected_by = $sample->collected_by;

        $participant = Participant::where('id', $sample->participant_id)->first();
        if ($participant->study_id == null && $this->entry_type != 'Client') {
            $participant->update(['study_id' => $this->study_id]);
        }

        $sampleReception = SampleReception::where('batch_no', $this->batch_no)->first();
        $sampleReception->increment('samples_handled');

        $this->batch_samples_handled = $sampleReception->samples_handled;
        $this->tests_requested = [];
        $this->tests = collect([]);

        if ($this->batch_sample_count == $this->batch_samples_handled) {
            $this->activeParticipantTab = true;
            $this->tabToggleBtn = true;
            $this->reset(['same_participant_id', 'same_participant']);
        }
    }

    public function editSampleInformation(Sample $sample)
    {
        if ($sample->participant->entry_type != 'Client') {
            $this->study_id = $sample->study_id;
        } else {
            $this->study_id = null;
        }
        $this->sample_id = $sample->id;
        $this->sample_type_id = $sample->sample_type_id;
        $this->visit = $sample->visit;
        $this->volume = $sample->volume;
        $this->requested_by = $sample->requested_by;
        $this->date_requested = $sample->date_requested;
        $this->collected_by = $sample->collected_by;
        $this->date_collected = $sample->date_collected;

        $this->sample_identity = $sample->sample_identity;
        $this->sample_is_for = $sample->sample_is_for;
        $this->priority = $sample->priority;
        $this->tests_requested = $sample->tests_requested ?? [];
        $this->participant_id = $sample->participant_id;
        $this->entry_type = $sample->participant->entry_type;
        $this->is_isolate = $sample->is_isolate;

        $sampleType = SampleType::where('id', $sample->sample_type_id)->first();
        $this->tests = Test::whereIn('id', (array) $sampleType->possible_tests)->orderBy('name', 'asc')->get();

        $this->toggleForm = true;
        $this->activeParticipantTab = false;
    }

    public function updateSampleInformation()
    {
        $this->validate([
            'requested_by' => 'required|integer',
            'date_requested' => 'required|date|before_or_equal:now',
            'sample_identity' => 'required|string',
            'sample_is_for' => 'required|string',
            'priority' => 'required|string',
            'sample_type_id' => 'integer|required',
            'tests_requested' => 'array|required',
        ]);

        if (! $this->is_isolate) {
            $this->validate([
                'collected_by' => 'required|integer',
                'date_collected' => 'required|date|before_or_equal:'.date('Y-m-d H:i', strtotime($this->date_delivered)).'|before_or_equal:'.date('Y-m-d 23:59', strtotime($this->date_requested)),
            ]);
        }
        if ($this->entry_type != 'Client') {
            $this->validate([
                'study_id' => 'required|integer',
            ]);
        }

        $sample = Sample::find($this->sample_id);
        $sample->sample_type_id = $this->sample_type_id;
        $sample->visit = $this->visit;
        $sample->volume = $this->volume;
        $sample->requested_by = $this->requested_by;
        $sample->date_requested = $this->date_requested;
        $sample->collected_by = $this->collected_by;
        $sample->date_collected = $this->date_collected;
        $sample->study_id = $this->study_id ?? null;
        $sample->sample_identity = $this->sample_identity;
        $sample->sample_is_for = $this->sample_is_for;
        $sample->priority = $this->priority;
        $sample->is_isolate = $this->is_isolate;
        $sample->tests_requested = $this->tests_requested ?? [];
        $sample->test_count = count($this->tests_requested) ?? 0;
        $sample->update();

        $this->resetSampleInformationInputs();
        $this->resetParticipantInputs();
        $this->toggleForm = false;
        $this->activeParticipantTab = true;
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Sample Data updated successfully!']);
    }

    public function resetParticipantInputs()
    {
        $this->reset(['identity', 'age', 'gender', 'contact', 'address',
            'nok_contact', 'nok_address', 'clinical_notes', 'title', 'nin_number', 'surname', 'first_name',
            'other_name', 'nationality', 'district', 'dob', 'email', 'birth_place', 'religious_affiliation', 'participantMatch',
            'occupation', 'civil_status', 'nok', 'nok_relationship', 'matched_participant_id', ]);
    }

    public function resetSampleInformationInputs()
    {
        $this->reset(['sample_id', 'participant_id', 'visit', 'volume', 'sample_type_id', 'sample_identity', 'requested_by', 'is_isolate',
            'date_requested', 'collected_by', 'date_collected', 'study_id', 'sample_is_for', 'priority', 'tests_requested', 'matched_participant_id', 'participantMatch', ]);
    }

    public function deleteConfirmation($id)
    {
        $this->delete_id = $id;
        $this->dispatchBrowserEvent('delete-modal');
    }

    public function deleteData()
    {
        try {
            $sample = Sample::where('id', $this->delete_id)->first();
            $sample->delete();
            $this->delete_id = '';
            $this->dispatchBrowserEvent('close-modal');
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Sample Information deleted successfully!']);
        } catch(Exception $error) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => 'Sample Information can not be deleted!']);
        }
    }

    public function refresh()
    {
        return redirect(request()->header('Referer'));
    }

    public function cancel()
    {
        $this->delete_id = '';
    }

    public function close()
    {
        $this->resetParticipantInputs();
        $this->resetSampleInformationInputs();
        $this->tests = collect([]);
        $this->toggleForm = false;
        $this->activeParticipantTab = true;
    }

    public function render()
    {
        $collectors = Collector::where(['facility_id' => $this->facility_id])->orderBy('name', 'asc')->get();
        $requesters = Requester::where(['facility_id' => $this->facility_id])->whereIn('study_id', auth()->user()->laboratory->associated_studies ?? [])->orderBy('name', 'asc')->get();
        $studies = Study::where(['facility_id' => $this->facility_id])->whereIn('id', auth()->user()->laboratory->associated_studies ?? [])->with('requester:id,name')->orderBy('name', 'asc')->get();
        $sampleTypes = SampleType::orderBy('type', 'asc')->get();
        $samples = Sample::where(['creator_lab' => auth()->user()->laboratory_id, 'sample_reception_id' => $this->sample_reception_id])->with(['participant', 'sampleType:id,type', 'study:id,name', 'requester:id,name', 'collector:id,name'])->latest()->get();

        return view('livewire.lab.sample-management.specimen-request-component', compact('sampleTypes', 'collectors', 'studies', 'requesters', 'samples'))->layout('layouts.app');
    }
}
