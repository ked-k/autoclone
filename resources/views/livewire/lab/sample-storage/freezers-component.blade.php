<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pt-0">
                    <div class="row mb-2">
                        <div class="col-sm-12 mt-3">
                            <div class="d-sm-flex align-items-center">
                                <h5 class="mb-2 mb-sm-0">

                                    @if (!$toggleForm)
                                        Freezers
                                    @else
                                        Update Freezer Information
                                    @endif
                                </h5>
                                <div class="ms-auto">
                                    <a type="button" class="btn btn-outline-info me-2" wire:click="refresh()"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                        data-bs-original-title="Refresh Table"><i class="bi bi-arrow-clockwise"></i></a>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-outline-info">More...</button>
                                        <button type="button"
                                            class="btn btn-outline-info split-bg-primary dropdown-toggle dropdown-toggle-split"
                                            data-bs-toggle="dropdown"> <span class="visually-hidden">Toggle
                                                Dropdown</span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end">
                                            <a class="dropdown-item" href="javascript:;" wire:click="close()">Reset
                                                form</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-0">
                        <form
                            @if (!$toggleForm) wire:submit.prevent="storeFreezer"
                        @else
                        wire:submit.prevent="updateFreezer" @endif>
                            <div class="row">
                                <div class="mb-2 col-md-2">
                                    <label for="freezer_location_id" class="form-label">{{ __('Location') }}</label>
                                    <select wire:model='freezer_location_id' class="form-select"
                                        id="freezer_location_id">
                                        <option selected value="">Select</option>
                                        @foreach ($freezerLocations as $freezerLocation)
                                            <option value="{{ $freezerLocation->id }}">{{ $freezerLocation->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('freezer_location_id')
                                        <div class="text-danger text-small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-2 col-md-4">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" id="name" class="form-control" wire:model.lazy="name">
                                    @error('name')
                                        <div class="text-danger text-small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3 col-md-2">
                                    <label for="type" class="form-label">Type</label>
                                    <select class="form-select select2" id="type" wire:model="type">
                                        <option selected value="">Select</option>
                                        <option value='Fridge'>Freezer</option>
                                        <option value='Fridge'>Fridge</option>
                                    </select>
                                    @error('type')
                                        <div class="text-danger text-small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-2 col-md-2">
                                    <div class="form-group">
                                        <label for="temp" class="form-label">{{ __('Temperature') }}</label>
                                        <div class="input-group form-group mb-2">
                                            <input type="text" class="form-control" id="temperature"
                                                wire:model.lazy='temp'>
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    ⁰C
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    @error('temp')
                                        <div class="text-danger text-small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3 col-md-2">
                                    <label for="isActive" class="form-label">Status</label>
                                    <select class="form-select select2" id="isActive" wire:model="is_active">
                                        <option selected value="">Select</option>
                                        <option value='1'>Active</option>
                                        <option value='0'>Inactive</option>
                                    </select>
                                    @error('is_active')
                                        <div class="text-danger text-small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="description" class="form-label">{{ __('Description') }}</label>
                                    <textarea name="description" id="description" rows="2" wire:model.lazy='description' class="form-control"
                                        placeholder="{{ __('Description') }}"></textarea>
                                    @error('description')
                                        <div class="text-danger text-small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="modal-footer text-start mt-4">
                                    @if (!$toggleForm)
                                        <x-button>{{ __('Save') }}</x-button>
                                    @else
                                        <x-button>{{ __('Update') }}</x-button>
                                    @endif
                                </div>

                            </div>
                            <!-- end row-->
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    <x-table-utilities>
                        <div>
                            <div class="d-flex align-items-center ml-4 me-2">
                                <label for="orderBy" class="text-nowrap mr-2 mb-0">OrderBy</label>
                                <select wire:model="orderBy" class="form-select">
                                    <option value="name">Name</option>
                                    <option value="temp">Temp</option>
                                    <option value="id">Latest</option>
                                    <option value="is_active">Status</option>
                                </select>
                            </div>
                        </div>
                    </x-table-utilities>
                    <div class="tab-content">
                        <div class="table-responsive">
                            <table id="datableButton" class="table table-striped mb-0 w-100 sortable">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Name</th>
                                        <th>Location</th>
                                        <th>Type</th>
                                        <th>Temp</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Action</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($freezers as $key => $freezer)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $freezer->name }}</td>
                                            <td>{{ $freezer->location->name }}</td>
                                            <td>{{ $freezer->type }}</td>
                                            <td>{{ $freezer->temp }}</td>
                                            <td>{{ $freezer->description ?? 'N/A' }}</td>
                                            @if ($freezer->is_active == 1)
                                                <td><span class="badge bg-success">Active</span></td>
                                            @else
                                                <td><span class="badge bg-danger">Suspended</span></td>
                                            @endif
                                            <td class="table-action">
                                                <a href="javascript: void(0);"
                                                    class="action-ico btn btn-outline-info mx-1"
                                                    wire:click="editFreezer({{ $freezer->id }})"
                                                    data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                    title="" data-bs-original-title="Edit Test"
                                                    class="action-ico btn btn-outline-danger mx-1"> <i
                                                        class="bi bi-pencil-square"></i></a>
                                                @if (Auth::user()->hasPermission(['master-access']))
                                                    <a href="javascript:;"
                                                        class="action-ico btn btn-outline-danger mx-1"
                                                        wire:click="deleteConfirmation({{ $freezer->id }})"><i
                                                            class="bi bi-trash-fill"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                        </div> <!-- end preview-->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="btn-group float-end">
                                    {{ $freezers->links('vendor.livewire.bootstrap') }}
                                </div>
                            </div>
                        </div>
                    </div> <!-- end tab-content-->
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->



        {{-- //DELETE CONFIRMATION MODAL --}}
        <div wire:ignore.self class="modal fade" id="delete_modal" tabindex="-1" data-backdrop="static"
            data-keyboard="false" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Delete Test</h5>
                        <button type="button" class="btn-close" wire:click="cancel()" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-4 pb-4">
                        <h6>Are you sure you want to delete this Record?</h6>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-sm btn-success" wire:click="cancel()" data-bs-dismiss="modal"
                            aria-label="Close">Cancel</button>
                        <button class="btn btn-sm btn-danger" wire:click="deleteData()">Yes! Delete</button>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                window.addEventListener('close-modal', event => {
                    $('#delete_modal').modal('hide');
                });

                window.addEventListener('delete-modal', event => {
                    $('#delete_modal').modal('show');
                });
            </script>
        @endpush
    </div>
</div>
