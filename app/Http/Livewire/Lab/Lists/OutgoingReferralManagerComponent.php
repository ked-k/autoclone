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
use Livewire\WithPagination;
use App\Models\SampleReception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class OutgoingReferralManagerComponent extends Component
{
     use WithPagination;

    public $requestCode;
    public $referralRequest;
    public $loading = false;
    public $error = null;
    public $success = null;

    // Sample selection
    public $selectedSamples = [];
    public $availableSamples = [];
    public $searchTerm = '';
    public $filters = [
        'sample_type' => '',
        'status' => 'approved', // Only show approved results
    ];

    protected $queryString = ['requestCode'];

    public function mount($requestCode = null)
    {
        $this->requestCode = $requestCode;
        if ($requestCode) {
            $this->loadReferralRequest();
        }
    }

    public function loadReferralRequest()
    {
        $this->loading = true;
        $this->error = null;
        $this->referralRequest = null;

        try {
            $response = Http::withHeaders([
                'X-Institution-API-Key' => env('INSTITUTION_API_KEY'),
                'Accept' => 'application/json',
            ])->timeout(30)
            ->get(env('CENTRAL_INSTANCE_URL') . "/api/v1/SampleReferralCrossBorder/referral/view_request/{$this->requestCode}");

            if ($response->successful()) {
                $data = $response->json();
                if ($data['success']) {
                    $this->referralRequest = $data['data'];
                    $this->loadAvailableSamples();
                } else {
                    $this->error = $data['message'] ?? 'Failed to load referral request';
                }
            } else {
                $this->error = 'API request failed: ' . $response->status();
            }
        } catch (\Exception $e) {
            $this->error = 'Error loading referral request: ' . $e->getMessage();
            Log::error('Error loading referral request', [
                'request_code' => $this->requestCode,
                'error' => $e->getMessage()
            ]);
        }

        $this->loading = false;
    }

    public function loadAvailableSamples()
    {
        if (!$this->referralRequest) return;

        // Query samples from your local LIMS that match the referral criteria
        $query = Sample::with(['participant', 'testResults.test', 'sampleType'])
            ->where('status', 'approved') // Only approved samples
            ->whereHas('testResults', function ($query) {
                $query->where('status', 'approved');
            });

        // Filter by sample type if specified in referral
        if (!empty($this->referralRequest['sample_type'])) {
            $query->whereHas('sampleType', function ($q) {
                $q->where('type', 'like', '%' . $this->referralRequest['sample_type'] . '%');
            });
        }

        // Filter by pathogen if specified
        if (!empty($this->referralRequest['pathogen'])) {
            $query->whereHas('testResults.test', function ($q) {
                $q->where('name', 'like', '%' . $this->referralRequest['pathogen'] . '%');
            });
        }

        // Apply search term
        if (!empty($this->searchTerm)) {
            $query->where(function ($q) {
                $q->where('sample_no', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('sample_identity', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('lab_no', 'like', '%' . $this->searchTerm . '%')
                  ->orWhereHas('participant', function ($q) {
                      $q->where('participant_no', 'like', '%' . $this->searchTerm . '%')
                        ->orWhere('identity', 'like', '%' . $this->searchTerm . '%');
                  });
            });
        }

        $this->availableSamples = $query->latest()
            ->limit(100) // Limit for performance
            ->get()
            ->map(function ($sample) {
                return $this->formatSampleForReferral($sample);
            })
            ->toArray();
    }

    private function formatSampleForReferral($sample)
    {
        $latestResult = $sample->testResults->where('status', 'approved')->first();

        return [
            'id' => $sample->id,
            'sample_id' => $sample->sample_identity, // Using sample_identity as unique ID
            'sample_no' => $sample->sample_no,
            'lab_no' => $sample->lab_no,
            'specimen_type' => $sample->sampleType->type ?? 'Unknown',
            'collection_date' => $sample->date_collected,
            'age' => $sample->participant->age,
            'gender' => $sample->participant->gender,
            'volume' => $sample->volume,
            'country' => 'Uganda', // Default or from your system
            'region' => $this->extractRegion($sample->participant->address),
            'state' => 'Kampala', // Default or from your system
            'district' => $this->extractDistrict($sample->participant->address),
            'sampling_purpose' => 'Routine testing', // Default or from your system
            'pathogen' => $latestResult->test->name ?? 'Unknown',
            'test_result' => $latestResult->result ?? 'N/A',
            'participant' => [
                'participant_no' => $sample->participant->participant_no,
                'identity' => $sample->participant->identity,
                'address' => $sample->participant->address,
            ]
        ];
    }

    private function extractRegion($address)
    {
        // Simple extraction - you might want to make this more sophisticated
        if (str_contains($address, 'Kampala')) return 'Central';
        if (str_contains($address, 'Gulu')) return 'Northern';
        if (str_contains($address, 'Mbarara')) return 'Western';
        if (str_contains($address, 'Jinja')) return 'Eastern';
        return 'Central'; // Default
    }

    private function extractDistrict($address)
    {
        // Simple extraction - you might want to make this more sophisticated
        if (str_contains($address, 'Kampala')) return 'Kampala';
        return 'Unknown';
    }

    public function addSelectedSamples()
    {
        if (empty($this->selectedSamples)) {
            $this->error = 'Please select at least one sample to add.';
            return;
        }

        $this->loading = true;
        $this->error = null;
        $this->success = null;

        try {
            // Filter selected samples from available samples
            $samplesToAdd = collect($this->availableSamples)
                ->whereIn('id', $this->selectedSamples)
                ->map(function ($sample) {
                    return [
                        'sample_id' => $sample['sample_id'],
                        'specimen_type' => $sample['specimen_type'],
                        'collection_date' => $sample['collection_date'],
                        'age' => $sample['age'],
                        'volume' => $sample['volume'],
                        'gender' => $sample['gender'],
                        'country' => $sample['country'],
                        'region' => $sample['region'],
                        'state' => $sample['state'],
                        'district' => $sample['district'],
                        'sampling_purpose' => $sample['sampling_purpose'],
                        'symptoms' => [], // You might want to get this from your system
                        'sequencing_pathogen_id' => 1, // You might want to map this properly
                    ];
                })
                ->values()
                ->toArray();

            $response = Http::withHeaders([
                'X-Institution-API-Key' => env('INSTITUTION_API_KEY'),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(30)
            ->post(
                env('CENTRAL_INSTANCE_URL') . "/api/v1/SampleReferralCrossBorder/referral/samples/{$this->requestCode}/add",
                ['samples' => $samplesToAdd]
            );

            if ($response->successful()) {
                $result = $response->json();
                if ($result['success']) {
                    $this->success = $result['message'] ?? 'Samples added successfully!';
                    $this->selectedSamples = [];

                    // Reload the referral request to get updated sample list
                    $this->loadReferralRequest();

                    // Emit event to refresh parent component if needed
                    $this->emit('samplesAdded', $this->requestCode);
                } else {
                    $this->error = $result['message'] ?? 'Failed to add samples';
                }
            } else {
                $errorData = $response->json();
                $this->error = $errorData['message'] ?? 'API request failed: ' . $response->status();
            }

        } catch (\Exception $e) {
            $this->error = 'Error adding samples: ' . $e->getMessage();
            Log::error('Error adding samples to referral', [
                'request_code' => $this->requestCode,
                'samples' => $this->selectedSamples,
                'error' => $e->getMessage()
            ]);
        }

        $this->loading = false;
    }

    public function updatedSearchTerm()
    {
        $this->loadAvailableSamples();
    }

    public function updatedFilters()
    {
        $this->loadAvailableSamples();
    }

    public function render()
    {

        return view('livewire.lab.lists.outgoing-referral-manager-component', [
            'existingSamples' => $this->referralRequest['samples'] ?? [],
            'pendingCount' => $this->referralRequest['pending_samples'] ?? 0,
        ]);
    }
}
