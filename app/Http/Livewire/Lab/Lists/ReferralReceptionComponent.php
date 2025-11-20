<?php

namespace App\Http\Livewire\Lab\Lists;

use Exception;
use App\Models\Sample;
use App\Models\Courier;
use Livewire\Component;
use App\Models\Facility;
use App\Helpers\Generate;
use App\Models\Collector;
use App\Models\Requester;
use App\Models\Admin\Test;
use App\Models\SampleType;
use App\Models\Participant;
use App\Models\SampleReception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ReferralReceptionComponent extends Component
{
    public $requestCode;
    public $referralData;
    public $showReceptionForm = false;
    public $receptionCreated = false;
    public $batchNo;

    // Reception form fields
    public $date_delivered;
    public $samples_delivered;
    public $samples_accepted;
    public $samples_rejected = 0;
    public $received_by;
    public $courier_signed;
    public $comment;


    public $sampleReception;
    public $showAccessionModal = false;
    public $selectedSample;
    public $participantData = [];
    public $sampleData = [];

    // Form fields (same as your existing component)
    public $participant_id;
    public $identity;
    public $age;
    public $gender;
    public $contact;
    public $address;
    public $sample_identity;
    public $sample_type_id;
    public $volume;
    public $sample_is_for = 'Testing';
    public $priority = 'Normal';
    public $tests_requested = [];


    //SAMPLE INFORMATION
    public $requested_by;

    public $date_requested;

    public $is_isolate;

    public $collected_by;

    public $date_collected;

    public $study_id;


    public $lastSampleId = false;


    public $visit;


    public $aliquots_requested = [];

    public $tests;

    public $aliquots;

    public $patient_found = false;

    public $patno;
    public $rejection_reason;

    public $entry_type = 'Client';

    protected $rules = [
        'date_delivered' => 'required|date|before_or_equal:now',
        'samples_delivered' => 'required|integer|min:1',
        'samples_accepted' => 'required|integer|lte:samples_delivered',
        'received_by' => 'required|string',
        'courier_signed' => 'required|string',
    ];

    public function mount($batch)
    {
        $this->requestCode = $batch;
         $this->fetchReferral();
           $this->tests    = collect([]);
             $this->aliquots = collect([]);
        $sampleReception = SampleReception::where('referral_request_no', $this->requestCode)->first();
        if ($sampleReception) {
             $this->sampleReception = $sampleReception;
        $this->batchNo = $sampleReception->batch_no;
        $this->receptionCreated = true;
        $this->showReceptionForm = false;

        }else{
              $this->receptionCreated = false;
               $this->showReceptionForm = true;
        }
    }


    public function fetchReferral()
{
    try {
        $key = env('INSTITUTION_API_KEY');

        $response = Http::withHeaders([
            // Use the header your API expects
            'X-Institution-API-Key' => $key,
            'Accept' => 'application/json',
        ])->get(env('CENTRAL_INSTANCE_URL') . "/api/v1/SampleReferralCrossBorder/referral/view_request/{$this->requestCode}");

        // Check HTTP status
        if (!$response->successful()) {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => 'Referral API Error: ' . $response->body()
            ]);

            Log::error('Referral API Error: ' . $response->body());
            return;
        }

        // Decode JSON
        $data = $response->json();

        // Debugging (optional):
        // logger($data);

        // Validate response format
        if (!isset($data['success']) || !$data['success']) {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => 'Failed to fetch referral request!'
            ]);
            return;
        }

        // Success: store data
        $this->referralData = $data['data'];

        // Auto-fill form
        $this->prefillReceptionData();

        // Show UI to user
        $this->showReceptionForm = true;

        $this->dispatchBrowserEvent('alert', [
            'type' => 'success',
            'message' => 'Referral request fetched successfully!'
        ]);

    } catch (\Exception $e) {

        Log::error('Referral API Exception: ' . $e->getMessage());

        $this->dispatchBrowserEvent('alert', [
            'type' => 'error',
            'message' => 'Referral API Exception: ' . $e->getMessage()
        ]);
    }
}



    protected function prefillReceptionData()
    {
        $this->date_delivered = now()->format('Y-m-d\TH:i');
        $this->samples_delivered = $this->referralData['no_of_samples'];
        $this->samples_accepted = $this->referralData['no_of_samples'];
        $this->received_by = auth()->user()->name;
        $this->courier_signed = $this->referralData['courier']['name'] ?? 'Unknown Courier';
        $this->comment = $this->referralData['courier_delivery_comment'] ?? 'Referral from ' . $this->referralData['requester_institution']['name'];
    }

    public function createReceptionBatch()
    {
        $this->validate();

        // Find or create facility
        $facility = Facility::firstOrCreate(
            ['name' => $this->referralData['requester_institution']['name']],
            [
                'type' => 'Institution',
                'parent_id' => 97,
                'contact' => $this->referralData['requester_institution']['contact'],
                'email' => $this->referralData['requester_institution']['email'],
                'country' => $this->referralData['requester_institution']['country'],
            ]
        );

        // Find or create courier
        $courier = Courier::firstOrCreate(
            ['name' => $this->referralData['courier']['name']],
            [
                'contact' => $this->referralData['courier']['contact'],
                'email' => $this->referralData['courier']['email'],
                'country' => $this->referralData['courier']['country'],
                'facility_id' => $facility->id,
            ]
        );

        $sampleReception = new SampleReception();
        $sampleReception->batch_no = Generate::batchNo();
        $sampleReception->date_delivered = $this->date_delivered;
        $sampleReception->samples_delivered = $this->samples_delivered;
        $sampleReception->samples_accepted = $this->samples_accepted;
        $sampleReception->samples_rejected = $this->samples_rejected;
        $sampleReception->received_by = auth()->user()->id;
        $sampleReception->courier_signed = true;
        $sampleReception->facility_id = $facility->id;
        $sampleReception->courier_id = $courier->id;
        $sampleReception->comment =  $this->referralData['details'] ?? 'No additional comments';
        $sampleReception->referral_request_no = $this->referralData['request_no'];
        $sampleReception->is_referral = true;
        $sampleReception->save();

        $this->batchNo = $sampleReception->batch_no;
        $this->receptionCreated = true;

        $this->dispatchBrowserEvent('alert', [
            'type' => 'success',
            'message' => 'Reception batch created successfully!'
        ]);

        //    protected $fillable = ['batch_no', 'date_delivered', 'samples_delivered', 'courier_id', 'facility_id', 'received_by', 'samples_accepted', 'samples_rejected', 'samples_handled', 'rejection_reason', 'courier_signed', 'created_by', 'creator_lab',
        // 'reviewed_by', 'date_reviewed', 'comment', 'status'];
    }


    public function openAccessionModal($sampleIndex)
    {
        $this->selectedSample = $this->referralData['samples'][$sampleIndex];
        $this->prefillSampleData();
        $this->showAccessionModal = true;
    }

    protected function prefillSampleData()
    {
        $sample = $this->selectedSample;
        // Prefill participant data
        $this->identity = $sample['sample_id'] ?? $sample['identifier'];
        $this->age = $sample['age'];
        $this->gender = $sample['gender'];
        $this->address = $sample['district'] . ', ' . $sample['region'] . ', ' . $sample['country'];

        // Try to find existing participant
        $participant = Participant::where('identity', $this->identity)->first();
        if ($participant) {
            $this->participant_id = $participant->id;
            $this->participantMatch = true;
        } else {
            $this->participantMatch = false;
        }

        // Prefill sample data
        $this->sample_identity = $sample['identifier'] ?? $sample['sample_id'];
        $this->volume = $sample['volume'];

        // Map specimen type to sample type
        $sampleType = SampleType::where('type', 'like', '%' . $sample['specimen_type'] . '%')->first();
        $this->sample_type_id = $sampleType->id ?? null;

        // Set default values
        $this->date_requested = $sample['collection_date'] ?? now()->format('Y-m-d');
        $this->date_collected = $sample['collection_date'] ?? now()->format('Y-m-d');
        $this->priority = $this->referralData['priority'] ?? 'Normal';

        // Auto-select tests based on pathogen
        $this->setTestsBasedOnPathogen($sample['pathogen'] ?? null);
    }

    protected function setTestsBasedOnPathogen($pathogen)
    {
        // Map pathogens to tests - you'll need to customize this based on your test catalog
        $pathogenTests = [
            'SARS-CoV-2' => [1, 2], // COVID-19 related tests
            'HIV' => [3, 4], // HIV related tests
            // Add more mappings as needed
        ];

        if ($pathogen && isset($pathogenTests[$pathogen])) {
            $this->tests_requested = $pathogenTests[$pathogen];
        }
    }

    public function saveSampleOld()
    {
        try{
             $key = env('INSTITUTION_API_KEY');
        // Use your existing saveSampleInformation method logic
        $this->validate([
            'sample_identity' => 'required',
            'sample_type_id' => 'required|integer',
            'volume' => 'required|numeric',
            // ... other validation rules from your existing component
        ]);
        $existingSample = Sample::where('sample_identity', str_replace(' ', '', trim($this->sample_identity)))->first();
        if ($existingSample) {
            $this->dispatchBrowserEvent('alert', ['type' => 'warning', 'message' => 'Sample with this identity already exists. Please use a different identity.']);
              $this->showAccessionModal = false;
            $this->resetForm();
              $response = Http::withHeaders([
            // Use the header your API expects
            'X-Institution-API-Key' => $key,
            'Accept' => 'application/json',
        ])->put(env('CENTRAL_INSTANCE_URL') . "/api/v1/SampleReferralCrossBorder/referral/sample/{$this->sample_identity}/accept");


            return;
        }

        $this->storeParticipant();
        // dd( $this->participant);
        $this->saveSampleInformation();

        $this->showAccessionModal = false;
        $this->resetForm();

        $this->dispatchBrowserEvent('alert', [
            'type' => 'success',
            'message' => 'Sample accessioned successfully!'
        ]);
            $response = Http::withHeaders([
            // Use the header your API expects
            'X-Institution-API-Key' => $key,
            'Accept' => 'application/json',
        ])->put(env('CENTRAL_INSTANCE_URL') . "/api/v1/SampleReferralCrossBorder/referral/sample/{$this->sample_identity}/accept");


    } catch (Exception $e) {
        $this->dispatchBrowserEvent('alert', [
            'type' => 'warning',
            'message' => 'Error accessioning sample: ' . $e->getMessage()
        ]);}
    }
    public function saveSample()
{
    $key = env('INSTITUTION_API_KEY');

    try {
        // Validate required fields
        $this->validate([
            'sample_identity' => 'required',
            'sample_type_id' => 'required|integer',
            'volume' => 'required|numeric',
            'tests_requested' => 'array|required',
        ]);

        // Normalize identity
        $identity = str_replace(' ', '', trim($this->sample_identity));

        // Check if sample already exists locally
        $existingSample = Sample::where('sample_identity', $identity)->first();

        if ($existingSample) {

            // 1. Notify user
            $this->dispatchBrowserEvent('alert', [
                'type' => 'warning',
                'message' => 'Sample with this identity already exists. Marking as accepted...'
            ]);

            // 2. Close modal + reset
            $this->showAccessionModal = false;
            $this->resetForm();

            // 3. Call central API to accept the sample
            $this->acceptSampleAtCentral($identity, $key);

            return;
        }

        // ------------------------------
        // Process NEW sample accession
        // ------------------------------

        $this->storeParticipant();
        $this->saveSampleInformation();

        $this->showAccessionModal = false;
        $this->resetForm();

        $this->dispatchBrowserEvent('alert', [
            'type' => 'success',
            'message' => 'Sample accessioned successfully!'
        ]);

        // Notify central server
        $this->acceptSampleAtCentral($identity, $key);

    } catch (Exception $e) {

        $this->dispatchBrowserEvent('alert', [
            'type' => 'error',
            'message' => 'Error accessioning sample: ' . $e->getMessage(),
        ]);
    }
}


/**
 * Send "accept" request to central server
 */
private function acceptSampleAtCentral($identity, $key)
{
    $response = Http::withHeaders([
        'X-Institution-API-Key' => $key,
        'Accept' => 'application/json',
    ])->put(env('CENTRAL_INSTANCE_URL') . "/api/v1/SampleReferralCrossBorder/referral/sample/{$identity}/accept");

    if (!$response->successful()) {
        Log::error("Central API ACCEPT failed: " . $response->body());

        $this->dispatchBrowserEvent('alert', [
            'type' => 'warning',
            'message' => 'Central API error: ' . $response->body(),
        ]);

        return;
    }

    // If central server says success
    $data = $response->json();

    if (isset($data['success']) && $data['success']) {
        $this->dispatchBrowserEvent('alert', [
            'type' => 'success',
            'message' => 'Central server updated: sample accepted.',
        ]);
    } else {
        $this->dispatchBrowserEvent('alert', [
            'type' => 'warning',
            'message' => 'Central server rejected the request.',
        ]);

        Log::warning("Central API returned failure: " . json_encode($data));
    }
}

    public $nok_contact, $nok_address,$clinical_notes, $participant;
    // Reuse your existing methods for storing participant and sample
    public function storeParticipant()
    {
        if ($this->sampleReception->samples_accepted == $this->sampleReception->samples_handled) {
            $this->activeParticipantTab = true;
            $this->tabToggleBtn         = true;

            $this->resetParticipantInputs();
            $this->dispatchBrowserEvent('maximum-reached', ['type' => 'warning', 'message' => 'Oops! Sample maximum already reached for this batch!']);
        } else {
            if ($this->entry_type != 'Other') {
                if ($this->entry_type == 'Participant' || $this->entry_type == 'Client') {
                    $this->validate([
                        'entry_type'     => 'required|string',
                        'age'            => 'nullable|integer|min:1',
                        'address'        => 'required|string|max:40',
                        'gender'         => 'nullable|string|max:6',
                        'contact'        => 'nullable|string|max:15',
                        'nok_contact'    => 'nullable|string|max:15',
                        'nok_address'    => 'nullable|string|max:75',
                        'clinical_notes' => 'nullable|string|max:30',

                    ]);
                }
            }
            $existingParticipant = Participant::where('identity', str_replace(' ', '', trim($this->identity)))->first();
            if ($existingParticipant) {
                $this->participant_id = $existingParticipant->id;
                $this->participant    = $existingParticipant;
                $this->entry_type     = $existingParticipant->entry_type;
                $this->dispatchBrowserEvent('alert', ['type' => 'info', 'message' => 'Existing participant found and loaded.']);
            }else{
            // try {
                $participant                 = new Participant();
                $patNo                       = Generate::participantNo();
                $participant->participant_no = $patNo;
                $participant->identity = str_replace(' ', '', trim($this->identity));

                $participant->age            = $this->age ?? null;
                $participant->months         = $this->months ?? null;
                $participant->address        = $this->address ?? null;
                $participant->gender         = $this->gender ?? null;
                $participant->contact        = $this->contact ?? null;
                $participant->nok_contact    = $this->nok_contact ?? null;
                $participant->nok_address    = $this->nok_address ?? null;
                $participant->clinical_notes = $this->clinical_notes ?? null;

                $participant->title                 = $this->title ?? null;
                $participant->nin_number            = $this->nin_number ?? null;
                $participant->surname               = $this->surname ?? null;
                $participant->first_name            = $this->first_name ?? null;
                $participant->other_name            = $this->other_name ?? null;
                $participant->nationality           = $this->nationality ?? null;
                $participant->district              = $this->district ?? null;
                $participant->dob                   = $this->dob ?? null;
                $participant->birth_place           = $this->birth_place ?? null;
                $participant->religious_affiliation = $this->religious_affiliation ?? null;
                $participant->occupation            = $this->occupation ?? null;
                $participant->civil_status          = $this->civil_status ?? null;
                $participant->email                 = $this->email ?? null;
                $participant->nok                   = $this->nok ?? null;
                $participant->nok_relationship      = $this->nok_relationship ?? null;
                $participant->entry_type            = 'Client';
                $participant->save();

                $this->participant_id = $participant->id;
                $this->participant    = $participant;
                $this->entry_type     = $participant->entry_type;

                // $this->resetParticipantInputs();
                $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => 'Participant Data Recorded successfully!']);
            }
            // } catch (Exception $e) {
            //     $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => 'Error saving participant: ' . $e->getMessage()]);
            // }
        }
    }

     public function saveSampleInformation()
    {

        $this->validate([
            // 'requested_by'    => 'required|integer',
            'date_requested'  => 'required|date|before_or_equal:' . date('Y-m-d', strtotime($this->date_delivered)),
            'sample_identity' => 'required|string|unique:samples',
            'sample_is_for'   => 'required|string',
            'priority'        => 'required|string',
            'sample_type_id'  => 'integer|required',
        ]);

        if ($this->sample_is_for == 'Testing' || $this->sample_is_for == 'Deffered') {
            $this->validate([
                'tests_requested' => 'array|required',
                'sample_identity' => 'required|string|unique:samples',
            ]);
        } elseif ($this->sample_is_for == 'Aliquoting') {
            $this->validate([
                'aliquots_requested' => 'array|required',
                'sample_identity'    => 'required|string|unique:samples',
            ]);
        }

        if (! $this->is_isolate) {
            $this->validate([
                // 'collected_by'    => 'required|integer',
                'sample_identity' => 'required|string|unique:samples',
                'date_collected'  => 'required|date|before_or_equal:' . date('Y-m-d H:i', strtotime($this->date_delivered)),
            ]);
        }

        if ($this->entry_type != 'Client') {
            $this->validate([
                'study_id' => 'required|integer',
            ]);
        }

        $sample                      = new Sample();
        $sample->sample_reception_id = $this->sampleReception->id;
        $sample->participant_id      = $this->participant_id;
        // $sample->visit               = $this->visit;
        $sample->sample_type_id      = $this->sample_type_id;
        $sample->sample_no           = Generate::sampleNo();
        $sample->lab_no              = Generate::labNo();
        $sample->volume              = $this->volume;
        // $sample->requested_by        = $this->requested_by;
        $sample->date_requested      = $this->date_requested;
        $sample->collected_by        = $this->collected_by;
        $sample->date_collected      = $this->date_collected;
        // $sample->study_id            = $this->study_id ?? null;
        $sample->sample_identity     = str_replace(' ', '', trim($this->sample_identity));
        $sample->sample_is_for       = $this->sample_is_for;
        $sample->priority            = $this->priority;

        if ($this->sample_is_for == 'Testing' || $this->sample_is_for == 'Deffered') {
            $sample->tests_requested = count($this->tests_requested) >= 1 ? $this->tests_requested : null;
            $sample->test_count      = count($this->tests_requested);
        } elseif ($this->sample_is_for == 'Aliquoting') {
            $sample->tests_requested = count($this->aliquots_requested) >= 1 ? $this->aliquots_requested : null;
            $sample->test_count      = count($this->aliquots_requested) ?? 0;
        } else {
            $sample->tests_requested = null;
            $sample->test_count      = 0;
        }

        $sample->status     = 'Accessioned';
        $sample->is_isolate = $this->is_isolate;
        $sample->save();

        $this->same_participant_id = $sample->participant_id;
        $this->same_requested_by   = $sample->requested_by;
        $this->same_study_id       = $sample->study_id;
        $this->same_collected_by   = $sample->collected_by;

        $participant = Participant::where('id', $sample->participant_id)->first();
        if ($participant->study_id == null && $this->entry_type != 'Client') {
            $participant->update(['study_id' => $this->study_id]);
        }

        $sampleReception = SampleReception::where('batch_no', $this->sampleReception->batch_no)->first();
        $sampleReception->increment('samples_handled');

        $this->batch_samples_handled = $this->sampleReception->samples_handled;
        $this->tests_requested       = [];
        $this->aliquots_requested    = [];
        $this->tests = collect([]);

        if ($this->sampleReception->samples_accepted == $this->sampleReception->samples_handled) {
            $this->activeParticipantTab = true;
            $this->tabToggleBtn         = true;
            $this->reset(['same_participant_id', 'same_participant']);
        }
    }

    public function updatedSampleIsFor()
    {
        if ($this->sample_type_id >= 1 && $this->sample_is_for == 'Aliquoting') {
            $this->tests    = collect([]);
            $sampleType     = SampleType::where('id', $this->sample_type_id)->first();
            $this->aliquots = SampleType::whereIn('id', (array) $sampleType->possible_aliquots)->orderBy('type', 'asc')->get();
        } elseif ($this->sample_type_id >= 1 && ($this->sample_is_for == 'Testing' || $this->sample_is_for == 'Deffered')) {
            $this->aliquots = collect([]);
            $sampleType     = SampleType::where('id', $this->sample_type_id)->first();
            $this->tests    = Test::whereIn('id', (array) $sampleType->possible_tests)->orderBy('name', 'asc')->get();
        } else {
            $this->tests    = collect([]);
            $this->aliquots = collect([]);
        }
    }

    public function resetForm()
    {
        $this->reset([
            'participant_id', 'identity', 'age', 'gender', 'contact', 'address',
            'sample_identity', 'sample_type_id', 'volume', 'tests_requested'
        ]);
    }

    public function render()
    {
         $data['samples'] = $this->referralData['samples'] ?? [];
        $data['accessionedSamples'] = Sample::where('sample_reception_id', $this->sampleReception?->id)
            ->with('participant')
            ->get();
        $data['sampleTypes'] = SampleType::all();
        $data['collectors']  = Collector::where(['facility_id' => $this->sampleReception?->facility_id, 'is_active' => true])->orderBy('name', 'asc')->get();
        $data['requesters']  = Requester::where(['facility_id' => $this->sampleReception?->facility_id, 'is_active' => true])->whereIn('study_id', auth()->user()->laboratory->associated_studies ?? [])->orderBy('name', 'asc')->get();
        return view('livewire.lab.lists.referral-reception-component',$data);
    }
}
