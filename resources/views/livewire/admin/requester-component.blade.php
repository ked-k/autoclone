<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header pt-0">
                <div class="row mb-2">
                    <div class="col-sm-12 mt-3">
                        <div class="d-sm-flex align-items-center">
                            <h5 class="mb-2 mb-sm-0">
                                Requesters
                            </h5>
                            <div class="ms-auto">
                                <a type="button" class="btn btn-outline-info" wire:click="refresh()"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                    data-bs-original-title="Refresh Table"><i class="bi bi-arrow-clockwise"></i></a>

                                <a type="button" class="btn btn-info" data-bs-toggle="modal"
                                    data-bs-target="#addRequester">Add Requester</a>
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
                                <option value="contact">Contact</option>
                                <option value="email">Email</option>
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
                                    <th>Requester</th>
                                    <th>Contact</th>
                                    <th>Email</th>
                                    <th>Facility</th>
                                    <th>Study/Project</th>
                                    <th>Status</th>
                                    <th>Date created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requesters as $key => $requester)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $requester->name }}</td>
                                        <td>{{ $requester->contact ? $requester->contact : 'N/A' }}</td>
                                        <td>{{ $requester->email ? $requester->email : 'N/A' }}</td>
                                        <td>{{ $requester->facility ? $requester->facility->name : 'N/A' }}</td>
                                        <td>{{ $requester->study ? $requester->study->name : 'N/A' }}</td>
                                        @if ($requester->is_active == 0)
                                            <td><span class="badge bg-danger">Inactive</span></td>
                                        @else
                                            <td><span class="badge bg-success">Active</span></td>
                                        @endif
                                        <td>{{ date('d-m-Y', strtotime($requester->created_at)) }}</td>
                                        <td class="table-action">
                                            @if ($requester->facility->is_active == 0 || $requester->study->is_active == 0)
                                                <a href="javascript: void(0);"
                                                    class="action-ico btn btn-outline-warning mx-1"
                                                    data-bs-toggle="tooltip" data-bs-placement="bottom" title=""
                                                    data-bs-original-title="Can not Edit"><i
                                                        class="bi bi-lock-fill"></i></a>
                                            @else
                                                <a href="javascript: void(0);"
                                                    class="action-ico btn btn-outline-info mx-1" data-bs-toggle="modal"
                                                    wire:click="editdata({{ $requester->id }})"
                                                    data-bs-target="#editrequester"><i
                                                        class="bi bi-pencil-square"></i></a>
                                                @if (Auth::user()->hasPermission(['master-access']))
                                                    <a href="javascript: void(0);"
                                                        wire:click="deleteConfirmation({{ $requester->id }})"
                                                        class="action-ico btn btn-outline-danger mx-1"> <i
                                                            class="bi bi-trash"></i></a>
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
                                {{ $requesters->links('vendor.livewire.bootstrap') }}
                            </div>
                        </div>
                    </div>
                </div> <!-- end tab-content-->

            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->

    {{-- ADD FACILITY --}}
    <div wire:ignore.self class="modal fade" id="addRequester" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Add New Requester</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div> <!-- end modal header -->
                <div class="modal-body">
                    <form wire:submit.prevent="storeData">
                        {{-- @csrf --}}
                        <div class="row">
                            <div class="mb-3 col-md-4">
                                <label for="requesterName" class="form-label">Name</label>
                                <input type="text" id="requesterName" class="form-control" name="name"
                                    wire:model.lazy="name">
                                @error('name')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="requestercontact" class="form-label">Contact</label>
                                <input type="text" id="requestercontact" class="form-control"
                                    wire:model.lazy="contact">
                                @error('contact')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="requesterEmail" class="form-label">Email</label>
                                <input type="email" id="requesterEmail" class="form-control" name="email"
                                    wire:model.lazy="email">
                                @error('email')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-5">
                                <label for="facility" class="form-label">Facility</label>
                                <select class="form-select" id="facility" wire:model="facility_id"
                                    wire:change="getStudies">
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

                            <div class="mb-3 col-md-5">
                                <label for="study_id" class="form-label">Study/project</label>
                                <select class="form-select" id="study_id" wire:model="study_id">
                                    @if ($facility_id && !$studies->isEmpty())
                                        <option selected value="">Select/None</option>
                                        @foreach ($studies as $study)
                                            <option value='{{ $study->id }}'>{{ $study->name }}</option>
                                        @endforeach
                                    @else
                                        <option selected value="">None</option>
                                    @endif
                                </select>
                                @error('study_id')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 col-md-2">
                                <label for="isActive" class="form-label">Status</label>
                                <select class="form-select" id="isActive" name="is_active" wire:model="is_active">
                                    <option selected value="">Select</option>
                                    <option value='1'>Active</option>
                                    <option value='0'>Inactive</option>
                                </select>
                                @error('is_active')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
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
                    <h5 class="modal-title" id="exampleModalLabel">Delete Requester</h5>
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

    <!-- EDIT requester Modal -->
    <div wire:ignore.self class="modal fade" id="editrequester" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Update Requester</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"
                        wire:click="close()"></button>
                </div> <!-- end modal header -->
                <div class="modal-body">
                    <form wire:submit.prevent="updateData">
                        <div class="row">
                            <div class="mb-3 col-md-4">
                                <label for="requesterName2" class="form-label">Name</label>
                                <input type="text" id="requesterName2" class="form-control" name="name"
                                    wire:model.lazy="name">
                                @error('name')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="requestercontact2" class="form-label">Contact</label>
                                <input type="text" id="requestercontact2" class="form-control"
                                    wire:model.lazy="contact">
                                @error('contact')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="requesterEmail2" class="form-label">Email</label>
                                <input type="email" id="requesterEmail2" class="form-control" name="email"
                                    wire:model.lazy="email">
                                @error('email')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-5">
                                <label for="facility2" class="form-label">Facility</label>
                                <select class="form-select" id="facility2" wire:model="facility_id"
                                    wire:change="getStudies">
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
                            <div class="mb-3 col-md-5">
                                <label for="study_id2" class="form-label">Study/project</label>
                                <select class="form-select" id="study_id2" wire:model="study_id">
                                    @if ($facility_id && !$studies->isEmpty())
                                        <option value="">Select/None</option>
                                        @foreach ($studies as $study)
                                            <option value='{{ $study->id }}'>{{ $study->name }}</option>
                                        @endforeach
                                    @else
                                        <option selected value="">None</option>
                                    @endif
                                </select>
                                @error('study_id')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-2">
                                <label for="isActive2" class="form-label">Status</label>
                                <select class="form-select" id="isActive2" name="is_active" wire:model="is_active">
                                    <option value='1'>Active</option>
                                    <option value='0'>Inactive</option>
                                </select>
                                @error('is_active')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
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

    @push('scripts')
        <script>
            window.addEventListener('close-modal', event => {
                $('#addRequester').modal('hide');
                $('#editrequester').modal('hide');
                $('#delete_modal').modal('hide');
                $('#show-delete-confirmation-modal').modal('hide');
            });

            window.addEventListener('edit-modal', event => {
                $('#editrequester').modal('show');
            });
            window.addEventListener('delete-modal', event => {
                $('#delete_modal').modal('show');
            });
        </script>
    @endpush
</div>
