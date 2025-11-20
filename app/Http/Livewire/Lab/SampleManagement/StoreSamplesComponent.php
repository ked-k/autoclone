<?php

namespace App\Http\Livewire\Lab\SampleManagement;

use App\Models\AliquotingAssignment;
use App\Models\Freezer;
use App\Models\Sample;
use App\Models\SampleStorage;
use Livewire\Component;

class StoreSamplesComponent extends Component
{
    public $sample;

    public $sample_id;

    public $sample_identity;

    public $lab_no;

    public $barcode;

    public $freezer_id;

    public $section_id;

    public $rack_id;

    public $drawer_id;

    public $box_id;

    public $box_column;

    public $box_row;

    public $comment;

    protected $validationAttributes = [
        'freezer_id' => 'Freezer',
        'section_id' => 'section',
    ];

    public function updated($fields)
    {
        $this->validateOnly($fields, [
            'freezer_id' => 'required',
            'section_id' => 'required',
        ]);
    }

    public function mount($id)
    {
        $sample = Sample::findOrFail($id);
        $this->sample = $sample;
        $this->sample_id = $sample->id;
        $this->sample_identity = $sample->sample_identity;
        $this->lab_no = $sample->lab_no;
    }

    public function storeSample()
    {
        $storage = new SampleStorage();
        $storage->sample_id = $this->sample_id;
        $storage->barcode = $this->barcode;
        $storage->freezer_id = $this->freezer_id;
        $storage->section_id = $this->section_id;
        $storage->rack_id = $this->rack_id;
        $storage->drawer_id = $this->drawer_id;
        $storage->box_id = $this->box_id;
        $storage->box_column = $this->box_column;
        $storage->box_row = $this->box_row;
        $storage->save();

        $this->sample->update(['status' => 'Stored']);
        AliquotingAssignment::where('sample_id', $this->sample_id)->update(['comment' => $this->comment, 'status' => 'Stored']);

        redirect()->route('test-request');
    }

    public function render()
    {
        $freezers = Freezer::with('location')->get();

        return view('livewire.lab.sample-management.store-samples-component', compact('freezers'));
    }
}
