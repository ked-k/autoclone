<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pt-0">
                    <div class="row mb-2">
                        <div class="col-sm-12 mt-3">
                            <div class="d-sm-flex align-items-center">
                                <h5 class="mb-2 mb-sm-0">
                                    Test Categories
                                </h5>
                                <div class="ms-auto">
                                    <a type="button" class="btn btn-outline-info" wire:click="refresh()"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                        data-bs-original-title="Refresh Table"><i class="bi bi-arrow-clockwise"></i></a>

                                    <a type="button" class="btn btn-info" data-bs-toggle="modal"
                                        data-bs-target="#modalAdd">Add Category</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <x-table-utilities>
                        <div>
                            <div class="d-flex align-items-center ml-4 me-2">
                                <label for="orderBy" class="text-nowrap mr-2 mb-0">OrderBy</label>
                                <select wire:model="orderBy" class="form-select">
                                    <option value="category_name">Name</option>
                                    <option value="id">Latest</option>
                                </select>
                            </div>
                        </div>
                    </x-table-utilities>
                    <div class="table-responsive">
                        <table class="table table-striped mb-0 w-100 sortable" id="datableButton">
                            <thead>
                                <tr>
                                    <td>Category</td>
                                    <td>Description</td>
                                    <td>Action</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $category)
                                    <tr>
                                        <td>{{ $category->category_name }}</td>
                                        <td>{{ $category->description }}</td>
                                        <td>
                                            <a href="javascript:;" class="action-ico btn btn-outline-info mx-1"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                wire:click="editdata({{ $category->id }})" data-target="#edit_modal"
                                                title="Edit"><i class="bi bi-pencil-square"></i></a>
                                            @if (Auth::user()->hasPermission(['master-access']))
                                                <a href="javascript:;" class="action-ico btn btn-outline-danger mx-1"
                                                    data-bs-toggle="tooltip"
                                                    wire:click="deleteConfirmation({{ $category->id }})"
                                                    title="Delete"><i class="bi bi-trash-fill"></i></a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="btn-group float-end">
                                {{ $categories->links('vendor.livewire.bootstrap') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="modalAdd" tabindex="-1" aria-labelledby="exampleModalLabel"
        role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add a new category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        wire:click="close()"></button>
                </div>
                <form wire:submit.prevent="storeData">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Category name</label>
                            <input type="text" name="category_name" id="category_name"
                                wire:model.lazy="category_name" class="form-control">
                            @error('category_name')
                                <div class="text-danger text-small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">Description</label>
                            <input type="text" name="description" id="description" wire:model.lazy="description"
                                class="form-control">
                            @error('description')
                                <div class="text-danger text-small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <x-button>{{ __('Save') }}</x-button>
                        <x-button type="button" class="btn btn-danger" wire:click="close()" data-bs-dismiss="modal">
                            {{ __('Close') }}</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div wire:ignore.self id="edit_modal" class="modal fade" tabindex="-1" aria-labelledby="exampleModalLabel"
        role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="updateData">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name" class="form-label">Category name</label>
                            <input type="text" name="category_name" id="category_name"
                                wire:model.lazy="category_name" class="form-control">
                            @error('category_name')
                                <div class="text-danger text-small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="email" class="form-label">Description</label>
                            <input type="text" name="description" id="description" wire:model.lazy="description"
                                class="form-control">
                            @error('description')
                                <div class="text-danger text-small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <x-button>{{ __('Save') }}</x-button>
                        <x-button type="button" class="btn btn-danger" wire:click="close()"
                            data-bs-dismiss="modal">{{ __('Close') }}</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->

    <div wire:ignore.self class="modal fade" id="delete_modal" tabindex="-1" data-backdrop="static"
        data-keyboard="false" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Delete category</h5>
                    <button type="button" class="btn-close" wire:click="cancel()" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body pt-4 pb-4">
                    <h6>Are you sure? You want to delete this data!</h6>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-primary" wire:click="cancel()" data-bs-dismiss="modal"
                        aria-label="Close">Cancel</button>
                    <button class="btn btn-sm btn-danger" wire:click="deleteData()">Yes! Delete</button>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            window.addEventListener('close-modal', event => {
                $('#modalAdd').modal('hide');
                $('#edit_modal').modal('hide');
                $('#delete_modal').modal('hide');
                $('#show-delete-confirmation-modal').modal('hide');
            });
            window.addEventListener('edit-modal', event => {
                $('#edit_modal').modal('show');
            });
            window.addEventListener('delete-modal', event => {
                $('#delete_modal').modal('show');
            });
        </script>
    @endpush
</div>
