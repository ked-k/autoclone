<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header pt-0">
                <div class="row mb-2">
                    <div class="col-sm-12 mt-3">
                        <div class="d-sm-flex align-items-center">
                            <h5 class="mb-2 mb-sm-0">
                                Projects/Studies
                            </h5>
                            <div class="ms-auto">
                                <a type="button" class="btn btn-outline-info" wire:click="refresh()"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                    data-bs-original-title="Refresh Table"><i class="bi bi-arrow-clockwise"></i></a>
                                @if (!$studies->isEmpty())
                                    <a type="button" class="btn btn-info mx-1" data-bs-toggle="modal"
                                        data-bs-target="#associate">Associate</a>
                                @endif
                                <a type="button" class="btn btn-info" data-bs-toggle="modal"
                                    data-bs-target="#addStudy">Add Study</a>
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
                                <option value="name">Name</option>
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
                                    <th>Study</th>
                                    <th>Description</th>
                                    <th>Facility</th>
                                    <th>Status</th>
                                    <th>Date created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($studies->only($associated_studies) as $key => $study)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $study->name }}</td>
                                        <td>{{ $study->description ? $study?->description : 'N/A' }}</td>
                                        <td>{{ $study->facility ? $study?->facility?->name : 'N/A' }}</td>
                                        @if ($study->is_active == 0)
                                            <td><span class="badge bg-danger">Inactive</span></td>
                                        @else
                                            <td><span class="badge bg-success">Active</span></td>
                                        @endif
                                        <td>{{ date('d-m-Y', strtotime($study->created_at)) }}</td>
                                        <td class="table-action">
                                            @if ($study?->facility?->is_active == 0)
                                                <a href="javascript: void(0);"
                                                    class="action-ico btn btn-outline-warning mx-1"
                                                    data-bs-toggle="tooltip" data-bs-placement="bottom" title=""
                                                    data-bs-original-title="Can not Edit"><i
                                                        class="bi bi-lock-fill"></i></a>
                                            @else
                                                <a href="javascript: void(0);"
                                                    class="action-ico btn btn-outline-info mx-1" data-bs-toggle="modal"
                                                    wire:click="editdata({{ $study->id }})"
                                                    data-bs-target="#editstudy">
                                                    <i class="bi bi-pencil-square"></i></a>
                                                @if (Auth::user()->hasPermission(['master-access']))
                                                    <a href="javascript: void(0);"
                                                        wire:click="deleteConfirmation({{ $study->id }})"
                                                        class="action-ico btn btn-outline-danger mx-1">
                                                        <i class="bi bi-trash"></i></a>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div> <!-- end preview-->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="btn-group float-end">
                                {{ $studies->links('vendor.livewire.bootstrap') }}
                            </div>
                        </div>
                    </div>
                </div> <!-- end tab-content-->

            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->

    {{-- ADD STUDY --}}
    <div wire:ignore.self class="modal fade" id="addStudy" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Add New Study</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div> <!-- end modal header -->
                <div class="modal-body">
                    <form wire:submit.prevent="storeData">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="studyName" class="form-label">Study/Project Name(Acronym)</label>
                                    <input type="text" id="studyName" class="form-control" name="name"
                                        wire:model.lazy="name">
                                    @error('name')
                                        <div class="text-danger text-small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea type="text" id="description" class="form-control" name="description" wire:model.lazy="description"></textarea>
                                    @error('description')
                                        <div class="text-danger text-small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="facility_id" class="form-label">Facility</label>
                                    <select class="form-select select2" id="facility_id" wire:model="facility_id">
                                        <option selected value="">Select</option>
                                        @forelse ($facilities as $facility)
                                            <option value='{{ $facility->id }}'>{{ $facility->name }}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                    @error('facility_id')
                                        <div class="text-danger text-small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="isActive" class="form-label">Status</label>
                                    <select class="form-select select2" id="isActive" name="is_active"
                                        wire:model="is_active">
                                        <option selected value="">Select</option>
                                        <option value='1'>Active</option>
                                        <option value='0'>Inactive</option>
                                    </select>
                                    @error('is_active')
                                        <div class="text-danger text-small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> <!-- end col -->
                        </div>
                        <!-- end row-->
                        <div class="modal-footer">
                            <x-button class="btn-success">{{ __('Save') }}</x-button>
                            <x-button type="button" class="btn btn-danger" wire:click="close()"
                                data-bs-dismiss="modal">{{ __('Close') }}</x-button>
                        </div>
                    </form>
                </div>
            </div> <!-- end modal content-->
        </div> <!-- end modal dialog-->
    </div> <!-- end modal-->

    {{-- //DELETE CONFIRMATION MODAL --}}
    <div wire:ignore.self class="modal fade" id="delete_modal" tabindex="-1" data-backdrop="static"
        data-keyboard="false" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Delete Study</h5>
                    <button type="button" class="btn-close" wire:click="cancel()" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body pt-4 pb-4">
                    <h6>Are you sure you want to delete this Record?</h6>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-primary" wire:click="cancel()" data-bs-dismiss="modal"
                        aria-label="Close">Cancel</button>
                    <button class="btn btn-sm btn-danger" wire:click="deleteData()">Yes! Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- EDIT study Modal -->
    <div wire:ignore.self class="modal fade" id="editstudy" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Update Study</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"
                        wire:click="close()"></button>
                </div> <!-- end modal header -->
                <div class="modal-body">
                    <form wire:submit.prevent="updateData">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="studyName2" class="form-label">Study/Project Name(Acronym)</label>
                                    <input type="text" id="studyName2" class="form-control" name="name"
                                        wire:model.lazy="name">
                                    @error('name')
                                        <div class="text-danger text-small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description2" class="form-label">Description</label>
                                    <textarea type="text" id="description2" class="form-control" name="description" wire:model.lazy="description"></textarea>
                                    @error('description')
                                        <div class="text-danger text-small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="facility2" class="form-label">Facility</label>
                                    <select class="form-select select2" id="facility2" wire:model="facility_id">
                                        @if ($facility_id == '')
                                            <option selected value="">None</option>
                                            @forelse ($facilities as $facility)
                                                <option value='{{ $facility->id }}'>{{ $facility->name }}</option>
                                            @empty
                                            @endforelse
                                        @else
                                            @forelse ($facilities as $facility)
                                                <option value='{{ $facility->id }}'>{{ $facility->name }}</option>
                                            @empty
                                                <option selected value="">None</option>
                                            @endforelse
                                        @endif
                                    </select>
                                    @error('facility_id')
                                        <div class="text-danger text-small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="isActive2" class="form-label">Status</label>
                                    <select class="form-select select2" id="isActive2" name="is_active"
                                        wire:model="is_active">
                                        <option selected value="">Select</option>
                                        <option value='1'>Active</option>
                                        <option value='0'>Inactive</option>
                                    </select>
                                    @error('is_active')
                                        <div class="text-danger text-small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> <!-- end col -->
                        </div>
                        <!-- end row-->
                        <div class="modal-footer">
                            <x-button>{{ __('Update') }}</x-button>
                            <x-button type="button" class="btn btn-danger" wire:click="close()"
                                data-bs-dismiss="modal">{{ __('Close') }}</x-button>
                        </div>
                    </form>
                </div>
            </div> <!-- end modal content-->
        </div> <!-- end modal dialog-->
    </div> <!-- end modal-->


    {{-- ASSOCIATE STUDIES TO LAB --}}
    <div wire:ignore.self class="modal fade" id="associate" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Associate Studies to Lab</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div> <!-- end modal header -->
                <div class="modal-body">
                    <form wire:submit.prevent="associateStudy">
                        <div class="row">
                            @forelse ($studies as $study)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="{{ $study?->id }}"
                                            id="associated_studies{{ $study->id }}" checked
                                            wire:model="associated_studies">
                                        <label class="form-check-label"
                                            for="associated_studies{{ $study->id }}"><strong
                                                class="text-info">{{ $study?->name }}</strong>{{ ' (' . $study?->facility?->name . ')' }}</label>
                                    </div>
                                </div>
                            @empty
                            @endforelse
                        </div>
                        <!-- end row-->
                        <div class="modal-footer">
                            <x-button class="btn-success">{{ __('Save') }}</x-button>
                            <x-button type="button" class="btn btn-danger" wire:click="close()"
                                data-bs-dismiss="modal">{{ __('Close') }}</x-button>
                        </div>
                    </form>
                </div>
            </div> <!-- end modal content-->
        </div> <!-- end modal dialog-->
    </div> <!-- end modal-->
    @push('scripts')
        <script>
            window.addEventListener('close-modal', event => {
                $('#addStudy').modal('hide');
                $('#editstudy').modal('hide');
                $('#delete_modal').modal('hide');
                $('#associate').modal('hide');
                $('#show-delete-confirmation-modal').modal('hide');
            });

            window.addEventListener('edit-modal', event => {
                $('#editstudy').modal('show');
            });
            window.addEventListener('delete-modal', event => {
                $('#delete_modal').modal('show');
            });
        </script>
    @endpush
</div>
