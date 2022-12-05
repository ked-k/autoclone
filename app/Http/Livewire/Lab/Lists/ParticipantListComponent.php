<?php

namespace App\Http\Livewire\Lab\Lists;

use App\Exports\ParticipantsExport;
use App\Models\Facility;
use App\Models\Participant;
use App\Models\Sample;
use App\Models\Study;
use Livewire\Component;
use Livewire\WithPagination;

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

    public function filterParticipants()
    {
        $participantList = Sample::select('participant_id')->where('creator_lab', auth()->user()->laboratory_id)->distinct()->get()->pluck('participant_id')->toArray();
        $participants = Participant::select('*')->whereIn('id', $participantList ?? [])->withCount(['sample', 'testResult'])->with('facility', 'study')
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
