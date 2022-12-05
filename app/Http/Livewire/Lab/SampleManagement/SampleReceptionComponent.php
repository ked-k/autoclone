<?php

namespace App\Http\Livewire\Lab\SampleManagement;

use App\Helpers\Generate;
use App\Models\Courier;
use App\Models\Facility;
use App\Models\SampleReception;
use App\Models\Study;
use App\Models\User;
use Exception;
use Livewire\Component;
use Livewire\WithPagination;

class SampleReceptionComponent extends Component
{
    use WithPagination;

    public $perPage = 10;

    public $search = '';

    public $orderBy = 'batch_no';

    public $orderAsc = true;

    public $batch_no;

    public $date_delivered;

    public $samples_delivered;

    public $facility_id;

    public $courier_id;

    public $samples_accepted;

    public $samples_rejected;

    public $comment;

    public $courier_signed;

    public $received_by;

    public $couriers;

    public $edit_id;

    public $delete_id;

    public $toggleForm = false;

    //SHOW DETAILS
    public $delivery_date;

    public $delivered_samples;

    public $facility_name;

    public $courier_name;

    public $courier_contact;

    public $courier_email;

    public $accepted;

    public $rejected;

    public $handled;

    public $reason_for_rejection;

    public $signed_by_courier;

    public $receiver;

    public $reviewer;

    public $review_date;

    public $batch_status;

    //NEW FACILITY FIELDS
    public $facilityname;

    public $facility_type;

    public $facility_parent_id;

    public $facility_status;

    //NEW COURIER FIELDS
    public $couriername;

    public $couriercontact;

    public $courierfacility;

    public $courierstudy;

    public $courierstatus;

    public $courieremail;

    public $studies;

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    protected $validationAttributes = [
        'facility_id' => 'facility',
        'courier_id' => 'courier',
    ];

    public function updated($fields)
    {
        $this->validateOnly($fields, [
            'date_delivered' => 'required|date|before_or_equal:now',
            'samples_delivered' => 'required|integer|min:1',
            'facility_id' => 'required',
            'courier_id' => 'required',
            'samples_accepted' => 'required|integer|min:0|lte:samples_delivered',
            'samples_rejected' => 'required|integer|min:0|lte:samples_delivered',
            'received_by' => 'required',
            'courier_signed' => 'required',
            'comment' => 'required_if:rejected,>,0',

        ]);
    }

    public function updatedSamplesAccepted()
    {
        if ($this->samples_delivered == '' || $this->samples_delivered == 0 && $this->samples_accepted == 0 || $this->samples_accepted == '') {
            $this->reset(['samples_delivered', 'samples_accepted', 'samples_rejected']);
        } elseif ($this->samples_accepted <= $this->samples_delivered) {
            $sampleDifference = $this->samples_delivered - $this->samples_accepted;
            if ($sampleDifference > 0) {
                $this->samples_rejected = $sampleDifference;
                $this->validate([
                    'comment' => 'required',
                ]);
            } elseif ($sampleDifference <= 0) {
                $this->samples_rejected = 0;
            }
        } elseif ($this->samples_accepted > $this->samples_delivered) {
            $this->reset(['samples_accepted', 'samples_rejected']);
        }
    }

    public function refresh()
    {
        return redirect(request()->header('Referer'));
    }

    public function getStudies()
    {
        $this->studies = Study::where('facility_id', $this->courierfacility)->latest()->get();
    }

    public function getCouriers()
    {
        $this->couriers = Courier::where('facility_id', $this->facility_id)->latest()->get();
    }

    public function mount()
    {
        $this->couriers = collect([]);
        $this->studies = collect([]);
    }

    public function storeData()
    {
        $this->validate([
            'date_delivered' => 'required|date|before_or_equal:now',
            'samples_delivered' => 'required',
            'facility_id' => 'required',
            'courier_id' => 'required',
            'samples_accepted' => 'required',
            'samples_rejected' => 'required',
            'received_by' => 'required',
            'courier_signed' => 'required',
            'comment' => 'required_if:rejected,>,0',
        ]);

        $sampleReception = new SampleReception();
        $sampleReception->batch_no = Generate::batchNo();
        $sampleReception->date_delivered = $this->date_delivered;
        $sampleReception->samples_delivered = $this->samples_delivered;
        $sampleReception->samples_accepted = $this->samples_accepted;
        $sampleReception->samples_rejected = $this->samples_rejected;
        $sampleReception->received_by = $this->received_by;
        $sampleReception->courier_signed = $this->courier_signed;
        $sampleReception->facility_id = $this->facility_id;
        $sampleReception->courier_id = $this->courier_id == '' ? '' : $this->courier_id;
        $sampleReception->comment = $this->comment ?? null;
        $sampleReception->save();
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Sample Reception Data created successfully!']);
        $this->resetInputs();
    }

    public function editdata(SampleReception $sampleReception)
    {
        $this->edit_id = $sampleReception->id;
        $this->batch_no = $sampleReception->batch_no;
        $this->date_delivered = $sampleReception->date_delivered;
        $this->samples_delivered = $sampleReception->samples_delivered;
        $this->samples_accepted = $sampleReception->samples_accepted;
        $this->samples_rejected = $sampleReception->samples_rejected;
        $this->received_by = $sampleReception->received_by;
        $this->courier_signed = $sampleReception->courier_signed;
        $this->facility_id = $sampleReception->facility_id;
        $this->courier_id = $sampleReception->courier_id;
        $this->comment = $sampleReception->comment;

        $this->couriers = Courier::where('facility_id', $sampleReception->facility_id)->latest()->get();

        $this->toggleForm = true;
    }

    public function showData(SampleReception $sampleReception)
    {
        $sampleReception->load('facility', 'courier', 'receiver', 'reviewer');
        $this->batch_no = $sampleReception->batch_no;
        $this->delivery_date = $sampleReception->date_delivered;
        $this->delivered_samples = $sampleReception->samples_delivered;
        $this->accepted = $sampleReception->samples_accepted;
        $this->rejected = $sampleReception->samples_rejected;
        $this->receiver = $sampleReception->receiver->fullName;
        $this->reviewer = $sampleReception->reviewer ? $sampleReception->reviewer->fullName : 'N/A';
        $this->review_date = $sampleReception->date_reviewed != null ? $sampleReception->date_reviewed : 'N/A';
        $this->comment = $sampleReception->comment ?? 'N/A';
        $this->courier_name = $sampleReception->courier->name;
        $this->courier_email = $sampleReception->courier->email;
        $this->signed_by_courier = $sampleReception->courier_signed == 1 ? 'Yes' : 'No';
        $this->facility_name = $sampleReception->facility->name;
        $this->courier_contact = $sampleReception->courier->contact;
        $this->courier_email = $sampleReception->courier->email;
        $this->handled = $sampleReception->samples_handled;
        $this->batch_status = $sampleReception->status;

        $this->dispatchBrowserEvent('show-modal');
    }

    public function resetInputs()
    {
        $this->reset(['batch_no', 'date_delivered', 'samples_delivered', 'courier_id', 'facility_id', 'received_by', 'samples_accepted', 'samples_rejected', 'comment', 'courier_signed']);
        $this->reset(['couriername', 'couriercontact', 'courieremail', 'courierfacility', 'courierstudy', 'courierstatus']);
        $this->reset(['facilityname', 'facility_type', 'facility_parent_id']);
    }

    public function updateData()
    {
        $this->validate([
            'date_delivered' => 'required|date|before_or_equal:now',
            'samples_delivered' => 'required',
            'facility_id' => 'required',
            'courier_id' => 'required',
            'samples_accepted' => 'required',
            'samples_rejected' => 'required',
            'received_by' => 'required',
            'courier_signed' => 'required',
            'comment' => 'required_if:rejected,>,0',
        ]);
        $sampleReception = SampleReception::find($this->edit_id);

        $sampleReception->date_delivered = $this->date_delivered;
        $sampleReception->samples_delivered = $this->samples_delivered;
        $sampleReception->samples_accepted = $this->samples_accepted;
        $sampleReception->samples_rejected = $this->samples_rejected;
        $sampleReception->received_by = $this->received_by;
        $sampleReception->courier_signed = $this->courier_signed;
        $sampleReception->facility_id = $this->facility_id;
        $sampleReception->courier_id = $this->courier_id == '' ? '' : $this->courier_id;
        $sampleReception->comment = $this->comment ?? null;
        $sampleReception->update();
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Sample Reception Data updated successfully!']);

        $this->resetInputs();
        $this->toggleForm = false;
    }

    public function deleteConfirmation($id)
    {
        $this->delete_id = $id;
        $this->dispatchBrowserEvent('delete-modal');
    }

    public function deleteData()
    {
        try {
            $sampleReception = SampleReception::where('id', $this->delete_id)->first();
            $sampleReception->delete();
            $this->delete_id = '';
            $this->dispatchBrowserEvent('close-modal');
            session()->flash('success', 'SampleReception deleted successfully.');
        } catch(Exception $error) {
            $this->dispatchBrowserEvent('alert', ['error' => 'success',  'message' => 'SampleReception can not be deleted!']);
        }
    }

    public function storeFacility()
    {
        $this->validate([
            'facilityname' => 'required',
            'facility_type' => 'required',
            'facility_status' => 'required',
        ]);

        $facility = new Facility();
        $facility->name = $this->facilityname;
        $facility->type = $this->facility_type;
        $facility->parent_id = $this->facility_parent_id;
        $facility->is_active = $this->facility_status;
        $facility->save();
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Facility created successfully!']);
        $this->reset(['facilityname', 'facility_type', 'facility_parent_id']);

        $this->dispatchBrowserEvent('close-modal');
    }

    public function storeCourier()
    {
        $this->validate([
            'couriername' => 'required',
            'couriercontact' => 'required',
            'courieremail' => 'required',
            'courierfacility' => 'required',
            'courierstudy' => 'required',
            'courierstatus' => 'required',
        ]);

        $courier = new Courier();
        $courier->name = $this->couriername;
        $courier->contact = $this->couriercontact;
        $courier->email = $this->courieremail;
        $courier->facility_id = $this->courierfacility;
        $courier->study_id = $this->courierstudy;
        $courier->is_active = $this->courierstatus;

        $courier->save();

        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Courier created successfully!']);
        $this->reset(['couriername', 'couriercontact', 'courieremail', 'courierfacility', 'courierstudy', 'courierstatus']);

        $this->dispatchBrowserEvent('close-modal');
    }

    public function cancel()
    {
        $this->delete_id = '';
    }

    public function close()
    {
        $this->resetInputs();
        $this->toggleForm = false;
        $this->couriers = collect();
    }

    public function render()
    {
        $users = User::where(['is_active' => 1, 'laboratory_id' => auth()->user()->laboratory_id])->latest()->get();

        $facilities = Facility::whereIn('id', auth()->user()->laboratory->associated_facilities ?? [])->latest()->get();

        $sampleReceptions = SampleReception::search($this->search)
        ->where('creator_lab', auth()->user()->laboratory_id)
        ->orderBy($this->orderBy, $this->orderAsc ? 'asc' : 'desc')
        ->simplePaginate($this->perPage);

        return view('livewire.lab.sample-management.sample-reception-component', compact('sampleReceptions', 'users', 'facilities'))->layout('layouts.app');
    }
}
