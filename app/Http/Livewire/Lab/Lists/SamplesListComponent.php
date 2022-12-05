<?php

namespace App\Http\Livewire\Lab\Lists;

use App\Exports\SamplesExport;
use App\Models\Facility;
use App\Models\Sample;
use App\Models\SampleType;
use App\Models\Study;
use Livewire\Component;
use Livewire\WithPagination;

class SamplesListComponent extends Component
{
    use WithPagination;

    public $facility_id = 0;

    public $study_id = 0;

    public $job = '';

    public $sampleType;

    public $from_date = '';

    public $to_date = '';

    public $perPage = 10;

    public $orderBy = 'id';

    public $orderAsc = 0;

    public $export;

    public $studies;

    public $sampleIds = [];

    protected $paginationTheme = 'bootstrap';

    public $recall_id;

    public $reception_id;

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
        if (count($this->sampleIds) > 0) {
            return (new SamplesExport($this->sampleIds))->download('Samples_'.date('Y-m-d').'_'.now()->toTimeString().'.xlsx');
        } else {
            $this->dispatchBrowserEvent('not-found', ['type' => 'error',  'message' => 'Oops! No Samples selected for export!']);
        }
    }

    public function filterSamples()
    {
        $samples = Sample::select('*')->where('creator_lab', auth()->user()->laboratory_id)->with(['participant', 'participant.facility', 'sampleType:id,type', 'study:id,name', 'sampleReception'])
                    ->when($this->facility_id != 0, function ($query) {
                        $query->whereHas('participant', function ($query) {
                            $query->where('facility_id', $this->facility_id);
                        });
                    }, function ($query) {
                        return $query;
                    })
                    ->when($this->study_id != 0, function ($query) {
                        $query->where('study_id', $this->study_id);
                    }, function ($query) {
                        return $query;
                    })
                    ->when($this->job != '', function ($query) {
                        $query->where('sample_is_for', $this->job);
                    }, function ($query) {
                        return $query;
                    })
                    ->when($this->sampleType != 0, function ($query) {
                        $query->where('sample_type_id', $this->sampleType);
                    }, function ($query) {
                        return $query;
                    })
                    ->when($this->from_date != '' && $this->to_date != '', function ($query) {
                        $query->whereBetween('created_at', [$this->from_date, $this->to_date]);
                    }, function ($query) {
                        return $query;
                    });

        $this->sampleIds = $samples->pluck('id')->toArray();

        return $samples;
    }

    public function recallSampleConfirmation($id)
    {
        $sample = Sample::where('creator_lab', auth()->user()->laboratory_id)->where('id', $id)->first();
        $this->recall_id = $sample->id;
        $this->reception_id = $sample->sample_reception_id;
        $this->dispatchBrowserEvent('recall-confirmation');
    }

    public function recallForTesting()
    {
        $sample = Sample::where('creator_lab', auth()->user()->laboratory_id)->where('id', $this->recall_id)->first();
        $sample->update(['sample_is_for' => 'Testing']);
        $this->recall_id = '';
        $this->dispatchBrowserEvent('close-modal');
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Sample Successfully recalled for testing!']);
    }

    public function recallBatchForTesting()
    {
        Sample::where(['sample_reception_id' => $this->reception_id, 'sample_is_for' => 'Deffered'])->update(['sample_is_for' => 'Testing']);
        $this->recall_id = '';
        $this->dispatchBrowserEvent('close-modal');
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Batch Samples Successfully recalled for testing!']);
    }

    public function refresh()
    {
        return redirect(request()->header('Referer'));
    }

    public function cancel()
    {
        $this->reset('recall_id');
    }

    public function render()
    {
        $facilities = Facility::whereIn('id', auth()->user()->laboratory->associated_facilities ?? [])->get();
        $sampleTypes = SampleType::where('creator_lab', auth()->user()->laboratory_id)->orderBy('type', 'asc')->get();
        $jobs = Sample::select('sample_is_for')->distinct()->get();
        $samples = $this->filterSamples()->orderBy($this->orderBy, $this->orderAsc ? 'asc' : 'desc')
        ->paginate($this->perPage);

        return view('livewire.lab.lists.samples-list-component', compact('samples', 'facilities', 'jobs', 'sampleTypes'));
    }
}
