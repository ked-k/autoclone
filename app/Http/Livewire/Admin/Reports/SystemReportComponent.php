<?php
namespace App\Http\Livewire\Admin\Reports;

use App\Models\Admin\SystemReport;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class SystemReportComponent extends Component
{
    use WithPagination;

    public $perPage = 10;

    public $search = '';

    public $orderBy = 'id';

    public $orderAsc = true;

    public $from_date;
    public $to_date;
    public $delete_id;

    public $report_date;

    public $toggleForm = false;

    protected $paginationTheme = 'bootstrap';

    public function export()
    {
        // return (new SystemReportsExport())->download('SystemReports.xlsx');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updated($fields)
    {
        $this->validateOnly($fields, [
            'report_date' => 'required',

        ]);
    }

    public function mount()
    {

    }

    public function storeData()
    {
        $this->validate([
            'report_date' => 'required',
        ]);

        $SystemReport              = new SystemReport();
        $SystemReport->facility_id = auth()->user()->laboratory_id;
        $SystemReport->report_date = $this->report_date;
        $SystemReport->ref_code    = 'SQCR' . date('ym') . '_' . time();
        $SystemReport->save();
        $this->dispatchBrowserEvent('close-modal');
        $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => 'SystemReport created successfully!']);
        return redirect()->SignedRoute('qualityReportItems', $SystemReport->ref_code);
    }

    public function editdata($code)
    {
        return redirect()->SignedRoute('qualityReportItems', $code);
    }

    public function resetInputs()
    {
        $this->reset(['report_date']);
    }

    public function updateData()
    {
        $this->validate([
            'report_date' => 'required',
        ]);
        $SystemReport              = SystemReport::find($this->edit_id);
        $SystemReport->report_date = $this->report_date;

        $SystemReport->update();
        $this->dispatchBrowserEvent('close-modal');
        $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => 'SystemReport updated successfully!']);
    }

    public function refresh()
    {
        return redirect(request()->header('Referer'));
    }

    public function deleteConfirmation($id)
    {
        if (Auth::user()->hasPermission(['manage-users'])) {
            $this->delete_id = $id;
            $this->dispatchBrowserEvent('delete-modal');
        } else {
            $this->dispatchBrowserEvent('cant-delete', ['type' => 'warning', 'message' => 'Oops! You do not have the necessary permissions to delete this resource!']);
        }
    }

    public function deleteData()
    {
        try {
            $SystemReport = SystemReport::where('creator_lab', auth()->user()->laboratory_id)->where('id', $this->delete_id)->first();
            $SystemReport->delete();
            $this->delete_id = '';
            $this->dispatchBrowserEvent('close-modal');
            $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => 'SystemReport deleted successfully!']);
        } catch (Exception $error) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => 'SystemReport can not be deleted!']);
        }
    }

    public function cancel()
    {
        $this->delete_id = '';
    }

    public function close()
    {
        $this->resetInputs();
    }

    public function render()
    {
        $data['systemReports'] = SystemReport::search($this->search)
            ->when($this->from_date != '' && $this->to_date != '', function ($query) {
                $query->whereBetween('created_at', [$this->from_date, $this->to_date]);
            })
            ->where('facility_id', auth()->user()->laboratory_id)
            ->orderBy($this->orderBy, $this->orderAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.reports.system-report-component', $data);
    }
}
