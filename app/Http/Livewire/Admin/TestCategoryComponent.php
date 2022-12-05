<?php

namespace App\Http\Livewire\Admin;

//use App\Models\TestCategory;

use App\Exports\TestCategoriesExport;
use App\Models\TestCategory;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class TestCategoryComponent extends Component
{
    use WithPagination;

    public $perPage = 10;

    public $search = '';

    public $orderBy = 'category_name';

    public $orderAsc = true;

    public $category_name;

    public $description;

    public $edit_id;

    public $delete_id;

    protected $paginationTheme = 'bootstrap';

    public function export()
    {
        return (new TestCategoriesExport())->download('Test_categories.xlsx');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updated($fields)
    {
        $this->validateOnly($fields, [
            'category_name' => 'required|unique:test_categories',
            'description' => 'required',

        ]);
    }

    public function storeData()
    {
        $this->validate([
            'category_name' => 'required|unique:test_categories',
            'description' => 'required',
        ]);
        $TestCategory = new TestCategory();
        $TestCategory->category_name = $this->category_name;
        $TestCategory->description = $this->description;
        $TestCategory->save();

        $this->description = '';
        $this->category_name = '';
        $this->dispatchBrowserEvent('close-modal');
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Test Category data created successfully!']);
    }

    public function editdata($id)
    {
        $TestCategory = TestCategory::where('id', $id)->first();
        $this->edit_id = $TestCategory->id;
        $this->category_name = $TestCategory->category_name;
        $this->description = $TestCategory->description;
        $this->dispatchBrowserEvent('edit-modal');
    }

    public function refresh()
    {
        return redirect(request()->header('Referer'));
    }

    public function resetInputs()
    {
        $this->description = '';
        $this->category_name = '';
    }

    public function updateData()
    {
        $this->validate([
            'category_name' => 'required|unique:test_categories,category_name,'.$this->edit_id.'',
            'description' => 'required',
        ]);
        $TestCategory = TestCategory::find($this->edit_id);
        $TestCategory->category_name = $this->category_name;
        $TestCategory->description = $this->description;
        $TestCategory->update();

        $this->description = '';
        $this->category_name = '';
        $this->dispatchBrowserEvent('close-modal');
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Test Category data updated successfully!']);
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
            $TestCategory = TestCategory::where('creator_lab', auth()->user()->laboratory_id)->where('id', $this->delete_id)->first();
            $TestCategory->delete();
            $this->delete_id = '';
            $this->dispatchBrowserEvent('close-modal');
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Category data deleted successfully!']);
        } catch(\Exception $error) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => 'Category data can not be deleted!']);
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
        $categories = TestCategory::search($this->search)
        ->where('creator_lab', auth()->user()->laboratory_id)
        ->orderBy($this->orderBy, $this->orderAsc ? 'asc' : 'desc')
        ->paginate($this->perPage);

        return view('livewire.admin.test-category', compact('categories'))->layout('layouts.app');
    }
}
