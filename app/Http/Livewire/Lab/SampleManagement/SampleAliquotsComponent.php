<?php

namespace App\Http\Livewire\Lab\SampleManagement;

use App\Helpers\Generate;
use App\Models\Admin\Test;
use App\Models\AliquotingAssignment;
use App\Models\Sample;
use App\Models\SampleReception;
use App\Models\SampleType;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SampleAliquotsComponent extends Component
{
    public $aliquots;

    public $aliquot_id;

    // public $tests_performed = [];
    public $aliquots_performed = [];

    public $requestedAliquots;

    public $aliquotIdentities = [];

    public $tests_requested = [];

    public $tests;

    public $sample;

    public $sample_id;

    public $sample_identity;

    public $performed_by;

    public $volume;

    public $sample_is_for;

    public $priority;

    public $comment;

    public $sample_reception_id;

    public $participant_id;

    public $visit;

    public $requested_by;

    public $date_requested;

    public $collected_by;

    public $date_collected;

    public $study_id;

    public function mount($id)
    {
        $sample = Sample::findOrFail($id);
        $this->sample = $sample;
        $this->sample_id = $sample->id;
        $this->sample_reception_id = $sample->sample_reception_id;
        $this->aliquots = SampleType::whereIn('id', (array) $sample->tests_requested)->orderBy('id', 'asc')->get();

        foreach ($this->aliquots->pluck('id')->toArray() as $key => $aliquot) {
            $this->aliquotIdentities[$aliquot] = $sample->sample_identity.'-'.$key + 1;
        }

        $aliquotsPending = array_diff($sample->tests_requested, $sample->tests_performed ?? []);

        if (count($aliquotsPending) > 0) {
            $this->requestedAliquots = $this->aliquots->whereIn('id', (array) $aliquotsPending)->values();
            $this->aliquot_id = $this->requestedAliquots[0]->id ?? null;
            $this->sample_identity = $this->aliquotIdentities[$this->aliquot_id];
        } else {
            $this->requestedAliquots = collect([]);
            $this->reset('aliquot_id');
        }

        $this->aliquots_performed = (array) $sample->tests_performed;
        $this->performed_by = auth()->user()->id;
        $this->tests = collect([]);
    }

    public function activateAliquotInput($id)
    {
        $this->aliquot_id = $id;
        $this->sample_identity = $this->aliquotIdentities[$this->aliquot_id];
        $this->tests = collect([]);
        $this->tests_requested = [];
        $this->sample_is_for = '';
    }

    public function updatedSampleIsFor()
    {
        if ($this->sample_is_for == 'Testing' || $this->sample_is_for == 'Deffered') {
            $this->tests = collect([]);
            $this->tests_requested = [];

            $sampleType = SampleType::where('id', $this->aliquot_id)->first();
            sleep(1);
            $this->tests = Test::whereIn('id', (array) $sampleType->possible_tests)->orderBy('name', 'asc')->get();
        } else {
            $this->tests = collect([]);
            $this->tests_requested = [];
        }
    }

    // public function storeAliquots()
    // {
    //     if (count($this->aliquots_performed) > 0) {
    //         foreach ($this->aliquots_performed as $aliquot) {
    //             SamplesAliquot::create(['parent_id' => $this->sample_id, 'aliquot_type_id' => $aliquot, 'aliquot_identity' => $this->aliquotIdentities[$aliquot] == '' ? null : $this->aliquotIdentities[$aliquot]]);
    //         }

    //         $this->sample->update(['tests_performed' => $this->aliquots_performed, 'status' => 'Aliquoted']);
    //         AliquotingAssignment::where('sample_id', $this->sample_id)->update(['comment' => $this->comment, 'status' => 'Aliquoted']);
    //         redirect()->route('test-request');
    //     } else {
    //         $this->dispatchBrowserEvent('mismatch', ['type' => 'error',  'message' => 'No aliquots selected for this sample!']);
    //     }
    // }

    public function storeAliquotInformation()
    {
        $this->validate([
            'sample_identity' => 'required|string|unique:samples',
            'volume' => 'required',
            'sample_is_for' => 'required|string',
            'priority' => 'required|string',

        ]);

        DB::transaction(function () {
            $aliquot = new Sample();
            $aliquot->sample_reception_id = $this->sample->sample_reception_id;
            $aliquot->participant_id = $this->sample->participant_id;
            $aliquot->visit = $this->sample->visit;
            $aliquot->sample_type_id = $this->aliquot_id;
            $aliquot->parent_id = $this->sample_id;
            $aliquot->sample_no = Generate::sampleNo();
            $aliquot->lab_no = Generate::labNo();
            $aliquot->volume = $this->volume;
            $aliquot->requested_by = $this->sample->requested_by;
            $aliquot->date_requested = $this->sample->date_requested;
            $aliquot->collected_by = $this->sample->collected_by;
            $aliquot->date_collected = $this->sample->date_collected;
            $aliquot->study_id = $this->sample->study_id ?? null;
            $aliquot->sample_identity = $this->sample_identity;
            $aliquot->sample_is_for = $this->sample_is_for;
            $aliquot->priority = $this->priority;

            if ($this->sample_is_for == 'Testing' || $this->sample_is_for == 'Deffered') {
                $aliquot->tests_requested = count($this->tests_requested) >= 1 ? $this->tests_requested : null;
                $aliquot->test_count = count($this->tests_requested);
            } else {
                $aliquot->tests_requested = null;
                $aliquot->test_count = 0;
            }

            $aliquot->status = 'Accessioned';
            $aliquot->save();

            array_push($this->aliquots_performed, "{$this->aliquot_id}");
            $this->sample->update(['tests_performed' => $this->aliquots_performed]);

            if (count(array_diff($this->sample->tests_requested, $this->sample->tests_performed)) == 0) {
                AliquotingAssignment::where('sample_id', $this->sample_id)->update(['status' => 'Aliquoted']);
                $this->sample->update(['status' => 'Aliquoted']);
                redirect()->route('test-request');
            }

            $sampleReception = SampleReception::where('id', $this->sample_reception_id)->first();
            $sampleReception->increment('samples_delivered');
            $sampleReception->increment('samples_accepted');
            $sampleReception->increment('samples_handled');
        });

        $this->requestedAliquots = $this->requestedAliquots->where('id', '!=', $this->aliquot_id)->values();
        $this->aliquot_id = $this->requestedAliquots[0]->id ?? null;
        $this->tests_requested = [];
        $this->tests = collect([]);
    }

    public function render()
    {
        $aliquotsRequested = $this->requestedAliquots ?? collect();

        return view('livewire.lab.sample-management.sample-aliquots-component', compact('aliquotsRequested'));
    }
}
