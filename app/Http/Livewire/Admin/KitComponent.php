<?php

namespace App\Http\Livewire\Admin;

use App\Exports\KitsExport;
use App\Models\Kit;
use App\Models\Platform;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class KitComponent extends Component
{
    use WithPagination;

    public $perPage = 10;

    public $search = '';

    public $orderBy = 'name';

    public $orderAsc = true;

    public $name;

    public $platform_id;

    public $is_active;

    public $delete_id;
    public $edit_id;

    protected $paginationTheme = 'bootstrap';

    public function export()
    {
        return (new KitsExport())->download('kits.xlsx');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    protected $validationAttributes = [
        'is_active' => 'status',
    ];

    public function updated($fields)
    {
        $this->validateOnly($fields, [
            'name' => 'required',
            'is_active' => 'required',

        ]);
    }

    public function storeData()
    {
        $isExist = Kit::select('*')
        ->where([['name', $this->name], ['creator_lab', auth()->user()->laboratory_id], ['platform_id', $this->platform_id]])
        ->exists();
        if ($isExist) {
            $this->name = '';
            $this->dispatchBrowserEvent('close-modal');
            $this->dispatchBrowserEvent('alert', ['type' => 'warning',  'message' => 'Kit name already exists!']);
        } else {
            $this->validate([
                'name' => 'required',
                'is_active' => 'required',
            ]);

            $kit = new Kit();
            $kit->name = $this->name;
            $kit->platform_id = $this->platform_id;
            $kit->is_active = $this->is_active;
            $kit->save();

            $this->reset(['name', 'platform_id', 'is_active']);
            $this->dispatchBrowserEvent('close-modal');
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Kit created successfully!']);
        }
    }

    public function editdata($id)
    {
        $kit = Kit::where('id', $id)->first();
        $this->edit_id = $kit->id;
        $this->name = $kit->name;
        $this->platform_id = $kit->platform_id;
        $this->is_active = $kit->is_active;
        $this->dispatchBrowserEvent('edit-modal');
    }

    public function resetInputs()
    {
        $this->reset(['name', 'platform_id', 'is_active']);
    }

    public function updateData()
    {
        $this->validate([
            'name' => 'required',
            'is_active' => 'required',
        ]);
        $kit = Kit::find($this->edit_id);
        $kit->name = $this->name;
        $kit->platform_id = $this->platform_id;
        $kit->is_active = $this->is_active;
        $kit->update();

        $this->reset(['name', 'platform_id', 'is_active']);
        $this->dispatchBrowserEvent('close-modal');
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Kit updated successfully!']);
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
            $this->dispatchBrowserEvent('cant-delete', ['type' => 'warning',  'message' => 'Oops! You do not have the necessary permissions to delete this resource!']);
        }
    }

    public function deleteData()
    {
        try {
            $kit = Kit::where('creator_lab', auth()->user()->laboratory_id)->where('id', $this->delete_id)->first();
            $kit->delete();
            $this->delete_id = '';
            $this->dispatchBrowserEvent('close-modal');
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Kit deleted successfully!']);
        } catch(Exception $error) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => 'Kit can not be deleted!']);
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
        $kits = Kit::search($this->search)
        ->where('creator_lab', auth()->user()->laboratory_id)->with('platform')
        ->orderBy($this->orderBy, $this->orderAsc ? 'asc' : 'desc')
        ->paginate($this->perPage);

        $platforms = Platform::where('creator_lab', auth()->user()->laboratory_id)->latest()->get();

        return view('livewire.admin.kit-component', compact('kits', 'platforms'))->layout('layouts.app');
    }
}
