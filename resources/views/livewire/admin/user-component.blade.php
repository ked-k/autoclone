<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header pt-0">
                <div class="row mb-2">
                    <div class="col-sm-12 mt-3">
                        <div class="d-sm-flex align-items-center">
                            <h5 class="mb-2 mb-sm-0">
                                System Users
                            </h5>
                            <div class="ms-auto">
                                <a type="button" class="btn btn-outline-info" wire:click="refresh()"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                    data-bs-original-title="Refresh Table"><i class="bi bi-arrow-clockwise"></i></a>

                                <a type="button" class="btn btn-info" data-bs-toggle="modal"
                                    data-bs-target="#addUser">Add User</a>
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
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Contact</th>
                                    <th>Laboratory</th>
                                    <th>Designation</th>
                                    <th>Status</th>
                                    <th>Created at</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $key => $user)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $user->fullName }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->contact ? $user->contact : 'N/A' }}</td>
                                        <td>{{ $user->email ? $user->email : 'N/A' }}</td>
                                        <td>{{ $user->laboratory ? $user->laboratory->laboratory_name : 'N/A' }}</td>
                                        <td>{{ $user->designation ? $user->designation->name : 'N/A' }}</td>
                                        @if ($user->is_active == 0)
                                            <td><span class="badge bg-danger">Inactive</span></td>
                                        @else
                                            <td><span class="badge bg-success">Active</span></td>
                                        @endif
                                        <td>{{ date('d-m-Y', strtotime($user->created_at)) }}</td>
                                        <td class="table-action">
                                            <a href="javascript: void(0);" class="action-ico btn btn-outline-info mx-1">
                                                <i class="bi bi-pencil-square" data-bs-toggle="modal"
                                                    wire:click="editdata({{ $user->id }})"
                                                    data-bs-target="#edituser"></i></a>
                                            @if (Auth::user()->hasPermission(['master-access']))
                                                <a href="javascript: void(0);" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title=""
                                                    data-bs-original-title="Delete"
                                                    wire:click="deleteConfirmation({{ $user->id }})"
                                                    class="action-ico btn btn-outline-danger">
                                                    <i class="bi bi-trash"></i></a>
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
                                {{ $users->links('vendor.livewire.bootstrap') }}
                            </div>
                        </div>
                    </div>
                </div> <!-- end tab-content-->

            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->

    {{-- ADD USER --}}
    <div wire:ignore.self class="modal fade" id="addUser" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"
                        wire:click="close()"></button>
                </div> <!-- end modal header -->
                <div class="modal-body">
                    <form wire:submit.prevent="storeData">

                        <div class="row">
                            <div class="mb-3 col-md-4">
                                <label for="title" class="form-label">Title</label>
                                <select class="form-select select2" id="title" wire:model="title">
                                    <option value="" selected>Select</option>
                                    <option value="Mr">Mr</option>
                                    <option value="Mrs">Mrs</option>
                                    <option value="Ms">Ms</option>
                                    <option value="Miss">Miss</option>
                                    <option value="Dr">Dr</option>
                                    <option value="Eng">Eng</option>
                                    <option value="Prof">Prof</option>
                                </select>
                                @error('title')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="emp_no" class="form-label">Employee No</label>
                                <input type="text" id="emp_no" class="form-control" wire:model.lazy="emp_no">
                                @error('emp_no')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="surname" class="form-label">Surname</label>
                                <input type="text" id="surname" class="form-control" wire:model.lazy="surname">
                                @error('surname')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" id="first_name" class="form-control"
                                    wire:model.lazy="first_name">
                                @error('first_name')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="other_name" class="form-label">Other Name</label>
                                <input type="text" id="other_name" class="form-control"
                                    wire:model.lazy="other_name">
                                @error('other_name')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="usercontact" class="form-label">Contact</label>
                                <input type="text" id="usercontact" class="form-control"
                                    wire:model.lazy="contact">
                                @error('contact')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="userEmail" class="form-label">Email</label>
                                <input type="email" id="userEmail" class="form-control" wire:model.lazy="email">
                                @error('email')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="laboratory_id" class="form-label">Laboratory</label>
                                <select class="form-select select2" id="laboratory_id" wire:model="laboratory_id">
                                    <option selected value="">Select</option>
                                    @forelse ($laboratories as $laboratory)
                                        <option value='{{ $laboratory->id }}'>{{ $laboratory->laboratory_name }}
                                        </option>
                                    @empty
                                    @endforelse
                                </select>
                                @error('laboratory_id')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="designation_id" class="form-label">Designation</label>
                                <select class="form-select select2" id="designation_id" wire:model="designation_id">
                                    @if (!$designations->isEmpty())
                                        <option selected value="">Select/None</option>
                                        @foreach ($designations as $designation)
                                            <option value='{{ $designation->id }}'>{{ $designation->name }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option selected value="">None</option>
                                    @endif
                                </select>
                                @error('designation_id')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="avatar" class="form-label">Photo/Avatar</label>
                                <input type="file" id="avatar" class="form-control" wire:model="avatar">
                                @error('avatar')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 col-md-4">
                                <label for="signature" class="form-label">Signature</label>
                                <input type="file" id="signature" class="form-control" wire:model="signature">
                                @error('signature')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
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
                            <div class="mb-3 col-md-12">
                                <label for="password" class="form-label">Password</label>
                                <input type="text" id="password" class="form-control"
                                    placeholder="Auto-Generated" wire:model="password" readonly>
                                @error('password')
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
                    <h5 class="modal-title" id="exampleModalLabel">Delete User</h5>
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

    <!-- EDIT user Modal -->
    <div wire:ignore.self class="modal fade" id="edituser" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Update User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"
                        wire:click="close()"></button>
                </div> <!-- end modal header -->
                <div class="modal-body">
                    <form wire:submit.prevent="updateData">
                        <div class="row">
                            <div class="mb-3 col-md-4">
                                <label for="title" class="form-label">Title</label>
                                <select class="form-select select2" id="title" wire:model="title">
                                    <option value="" selected>Select</option>
                                    <option value="Mr">Mr</option>
                                    <option value="Mrs">Mrs</option>
                                    <option value="Ms">Ms</option>
                                    <option value="Miss">Miss</option>
                                    <option value="Dr">Dr</option>
                                    <option value="Eng">Eng</option>
                                    <option value="Prof">Prof</option>
                                </select>
                                @error('title')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="emp_no" class="form-label">Employee No</label>
                                <input type="text" id="emp_no" class="form-control" wire:model.lazy="emp_no">
                                @error('emp_no')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="surname" class="form-label">Surname</label>
                                <input type="text" id="surname" class="form-control" wire:model.lazy="surname">
                                @error('surname')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" id="first_name" class="form-control"
                                    wire:model.lazy="first_name">
                                @error('first_name')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="other_name" class="form-label">Other Name</label>
                                <input type="text" id="other_name" class="form-control"
                                    wire:model.lazy="other_name">
                                @error('other_name')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="usercontact" class="form-label">Contact</label>
                                <input type="text" id="usercontact" class="form-control"
                                    wire:model.lazy="contact">
                                @error('contact')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="userEmail" class="form-label">Email</label>
                                <input type="email" id="userEmail" class="form-control" wire:model.lazy="email">
                                @error('email')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="laboratory_id" class="form-label">Laboratory</label>
                                <select class="form-select select2" id="laboratory_id" wire:model="laboratory_id">
                                    <option selected value="">Select</option>
                                    @forelse ($laboratories as $laboratory)
                                        <option value='{{ $laboratory->id }}'>{{ $laboratory->laboratory_name }}
                                        </option>
                                    @empty
                                    @endforelse
                                </select>
                                @error('laboratory_id')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="designation_id" class="form-label">Designation</label>
                                <select class="form-select select2" id="designation_id" wire:model="designation_id">
                                    @if (!$designations->isEmpty())
                                        <option selected value="">Select/None</option>
                                        @foreach ($designations as $designation)
                                            <option value='{{ $designation->id }}'>{{ $designation->name }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option selected value="">None</option>
                                    @endif
                                </select>
                                @error('designation_id')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="avatar" class="form-label">Photo/Avatar</label>
                                <input type="file" id="avatar" class="form-control" wire:model="avatar">
                                @error('avatar')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 col-md-4">
                                <label for="signature" class="form-label">Signature</label>
                                <input type="file" id="signature" class="form-control" wire:model="signature">
                                @error('signature')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
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
                $('#addUser').modal('hide');
                $('#edituser').modal('hide');
                $('#delete_modal').modal('hide');
                $('#show-delete-confirmation-modal').modal('hide');
            });

            window.addEventListener('edit-modal', event => {
                $('#edituser').modal('show');
            });
            window.addEventListener('delete-modal', event => {
                $('#delete_modal').modal('show');
            });
        </script>
    @endpush
</div>
