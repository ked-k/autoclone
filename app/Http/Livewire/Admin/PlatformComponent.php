<?php

namespace App\Http\Livewire\Admin;

use App\Exports\PlatformsExport;
use App\Models\Platform;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class PlatformComponent extends Component
{
    use WithPagination;

    public $perPage = 10;

    public $search = '';

    public $orderBy = 'name';

    public $orderAsc = true;

    public $name;

    public $range;

    public $is_active;

    public $delete_id;

    protected $paginationTheme = 'bootstrap';

    public function export()
    {
        return (new PlatformsExport())->download('platforms.xlsx');
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
        $isExist = Platform::select('*')
        ->where([['name', $this->name], ['creator_lab', auth()->user()->laboratory_id]])
        ->exists();
        if ($isExist) {
            $this->name = '';
            $this->dispatchBrowserEvent('close-modal');
            $this->dispatchBrowserEvent('alert', ['type' => 'warning',  'message' => 'Platiform name already exists!']);
        } else {
            $this->validate([
                'name' => 'required',
                'is_active' => 'required',
            ]);

            $platform = new Platform();
            $platform->name = $this->name;
            $platform->range = $this->range;
            $platform->is_active = $this->is_active;
            $platform->save();

            $this->reset(['name', 'range', 'is_active']);
            $this->dispatchBrowserEvent('close-modal');
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Platform created successfully!']);
        }
    }

    public function editdata($id)
    {
        $platform = Platform::where('id', $id)->first();
        $this->edit_id = $platform->id;
        $this->name = $platform->name;
        $this->range = $platform->range;
        $this->is_active = $platform->is_active;
        $this->dispatchBrowserEvent('edit-modal');
    }

    public function resetInputs()
    {
        $this->reset(['name', 'range', 'is_active']);
    }

    public function updateData()
    {
        $this->validate([
            'name' => 'required',
            'is_active' => 'required',
        ]);
        $platform = Platform::find($this->edit_id);
        $platform->name = $this->name;
        $platform->range = $this->range;
        $platform->is_active = $this->is_active;
        $platform->update();

        $this->reset(['name', 'range', 'is_active']);
        $this->dispatchBrowserEvent('close-modal');
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Platform updated successfully!']);
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
            $platform = Platform::where('creator_lab', auth()->user()->laboratory_id)->where('id', $this->delete_id)->first();
            $platform->delete();
            $this->delete_id = '';
            $this->dispatchBrowserEvent('close-modal');
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Platform deleted successfully!']);
        } catch(Exception $error) {
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Platform can not be deleted!']);
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
        $platforms = Platform::search($this->search)
        ->where('creator_lab', auth()->user()->laboratory_id)
        ->orderBy($this->orderBy, $this->orderAsc ? 'asc' : 'desc')
        ->paginate($this->perPage);

        return view('livewire.admin.platform-component', compact('platforms'))->layout('layouts.app');
    }
}
