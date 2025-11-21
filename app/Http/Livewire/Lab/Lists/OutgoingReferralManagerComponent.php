<?php

namespace App\Http\Livewire\Lab\Lists;

use Exception;
use App\Models\Sample;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class OutgoingReferralManagerComponent extends Component
{
    public $requestCode;
    public $referralRequest = null;
    public $loading = false;
    public $error = null;
    public $success = null;

    public $selectedSamples = [];
    public $availableSamples = [];
    public $searchTerm = '';
    public $filters = [
        'sample_type' => '',
        'status' => 'approved',
    ];
    public $selectAll = false;

    protected $queryString = ['requestCode'];

    public function mount($batch = null)
    {
        if ($batch) {
            $this->requestCode = $batch;
            $this->loadReferralRequest();
        }
    }

    private function apiKey()
    {

        return env('INSTITUTION_API_KEY')?:'qrokk2a5tZIoq9AOvc8LbTA9da886ApY9fZtE9uJfBzbLEdTNO7Qo7dluy47Hfau';
    }

    private function apiBase()
    {
        return env('CENTRAL_INSTANCE_URL') ?: 'https://nimsdev.africacdc.org';
    }

    private function api()
    {
        return Http::withHeaders([
            'X-Institution-API-Key' => $this->apiKey(),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->timeout(30)
          ->withOptions(['verify' => false]);
    }

    public function loadReferralRequest()
    {
        $this->resetFeedback();
        $this->loading = true;

        try {
            $response = $this->api()
                ->get($this->apiBase() . "/api/v1/SampleReferralCrossBorder/referral/view_request/{$this->requestCode}");

            if (!$response->successful()) {
                return $this->fail("API request failed: " . $response->status());
            }

            $data = $response->json();

            if (!($data['success'] ?? false)) {
                return $this->fail($data['message'] ?? "Failed to load referral request.");
            }

            $this->referralRequest = $data['data'];
            $this->loadAvailableSamples();

        } catch (Exception $e) {
            $this->fail("Error loading referral request: " . $e->getMessage(), $e);
        }

        $this->loading = false;
    }

    public function loadAvailableSamples()
    {
        if (!$this->referralRequest) return;

        $query = Sample::with(['participant', 'sampleType'])
            ->where('sample_is_for', 'Deffered');

        if (!empty($this->referralRequest['sample_type'])) {
            $query->whereHas('sampleType', function ($q) {
                $q->where('type', 'like', '%' . $this->referralRequest['sample_type'] . '%');
            });
        }

        if (!empty($this->searchTerm)) {
            $query->where(function ($q) {
                $q->where('sample_no', 'like', "%{$this->searchTerm}%")
                    ->orWhere('sample_identity', 'like', "%{$this->searchTerm}%")
                    ->orWhere('lab_no', 'like', "%{$this->searchTerm}%")
                    ->orWhereHas('participant', function ($q) {
                        $q->where('participant_no', 'like', "%{$this->searchTerm}%")
                          ->orWhere('identity', 'like', "%{$this->searchTerm}%");
                    });
            });
        }

        $this->availableSamples = $query->limit(100)->get()
            ->map(fn($sample) => $this->formatSample($sample))
            ->toArray();
    }

    private function formatSample($sample)
    {
        return [
            'id' => $sample->id,
            'sample_id' => $sample->sample_identity,
            'lab_no' => $sample->lab_no,
            'specimen_type' => $sample->sampleType->type ?? 'Unknown',
            'collection_date' => $sample->date_collected,
            'age' => $sample->participant->age,
            'gender' => $sample->participant->gender,
            'volume' => $sample->volume,
            'country' => 'Uganda',
            'region' => 'Central',
            'state' => 'Kampala',
            'district' => 'Unknown',
            'sampling_purpose' => 'Routine testing',
            'participant' => [
                'participant_no' => $sample->participant->participant_no,
                'identity' => $sample->participant->identity,
                'address' => $sample->participant->address,
            ]
        ];
    }

    public function toggleSelectAll()
    {
        $this->selectAll = !$this->selectAll;

        if ($this->selectAll) {
            $this->selectedSamples = collect($this->availableSamples)
                ->pluck('id')->toArray();
        } else {
            $this->selectedSamples = [];
        }
    }

    public function addSingleSample($sampleId)
    {
        $sample = collect($this->availableSamples)->firstWhere('id', $sampleId);

        if (!$sample) return $this->fail("Sample not found.");

        $this->sendSamples([$sample]);
    }

    public function addSelectedSamples()
    {
        $samples = collect($this->availableSamples)
            ->whereIn('id', $this->selectedSamples)
            ->values()
            ->toArray();

        if (empty($samples)) return $this->fail("No samples selected.");

        $this->sendSamples($samples);
    }

    private function sendSamples($samples)
    {
        $payload = ['samples' => array_map(fn($s) => [
            'sample_id' => $s['sample_id'],
            'specimen_type' => $s['specimen_type'],
            'collection_date' => $s['collection_date'],
            'age' => $s['age'],
            'volume' => $s['volume'],
            'gender' => $s['gender'],
            'country' => $s['country'],
            'region' => $s['region'],
            'state' => $s['state'],
            'district' => $s['district'],
            'sampling_purpose' => $s['sampling_purpose'],
            'symptoms' => [],
            'sequencing_pathogen_id' => 1,
        ], $samples)];

        try {
            $response = $this->api()
                ->post($this->apiBase() . "/api/v1/SampleReferralCrossBorder/referral/samples/{$this->requestCode}/add", $payload);

            if (!$response->successful()) {
                return $this->fail("API error: " . $response->status());
            }

            $data = $response->json();

            if (!($data['success'] ?? false)) {
                return $this->fail($data['message'] ?? "Sample upload failed.");
            }
        // -----------------------------
        // UPDATE LOCAL DATABASE HERE
        // -----------------------------
        $sampleIds = collect($samples)->pluck('id')->toArray();

        Sample::whereIn('id', $sampleIds)
            ->update(['sample_is_for' => 'Testing']);


            $this->success = "Samples added successfully!";
            $this->selectedSamples = [];
            $this->loadReferralRequest();

        } catch (Exception $e) {
            $this->fail("Error adding samples: " . $e->getMessage(), $e);
        }
    }

    private function fail($message, Exception $e = null)
    {
        $this->error = $message;
        if ($e) Log::error($message, ['exception' => $e]);
    }

    private function resetFeedback()
    {
        $this->error = null;
        $this->success = null;
    }

    public function updatedSearchTerm()
    {
        if ($this->referralRequest) {
            $this->loadAvailableSamples();
        }
    }

    public function updatedFilters()
    {
        if ($this->referralRequest) {
            $this->loadAvailableSamples();
        }
    }

    public function render()
    {
        return view('livewire.lab.lists.outgoing-referral-manager-component', [
            'existingSamples' => $this->referralRequest['samples'] ?? [],
            'pendingCount' => $this->referralRequest['pending_samples'] ?? 0,
        ]);
    }
}
