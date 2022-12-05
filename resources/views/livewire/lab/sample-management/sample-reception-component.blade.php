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
                                        Sample Reception
                                    @else
                                        Update Sample Reception Data for
                                        <strong class="text-success">{{ $batch_no }}</strong>
                                    @endif
                                </h5>
                                <div class="ms-auto">

                                    <a type="button" class="btn btn-outline-info" wire:click="refresh()"
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
                                            @if (Auth::user()->hasPermission(['create-reception-info']))
                                                <a class="dropdown-item" href="javascript:;" data-bs-toggle="modal"
                                                    data-bs-target="#addFacility">Add Facility</a>
                                                <a class="dropdown-item" href="javascript:;" data-bs-toggle="modal"
                                                    data-bs-target="#addCourier">Add Courier</a>
                                            @endif
                                            <a class="dropdown-item" href="javascript:;" wire:click="close()">Reset
                                                form</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>

                    @if (Auth::user()->hasPermission(['create-reception-info']))
                        <div class="row mb-0">
                            <form
                                @if (!$toggleForm) wire:submit.prevent="storeData"
                        @else
                        wire:submit.prevent="updateData" @endif>
                                <div class="row">
                                    <div class="mb-3 col-md-2">
                                        <label for="date_delivered" class="form-label">Date/Time Delivered</label>
                                        <input id="date_delivered" type="datetime-local" class="form-control"
                                            wire:model.lazy="date_delivered">
                                        @error('date_delivered')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col-md-2">
                                        <label for="delivered" class="form-label">Samples Delivered</label>
                                        <input type="number" id="delivered" class="form-control"
                                            wire:model.lazy="samples_delivered">
                                        @error('samples_delivered')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="facility_id" class="form-label">Facility</label>
                                        <select class="form-select" id="facility_id" wire:model="facility_id"
                                            wire:change="getCouriers()">
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

                                    <div class="mb-3 col-md-3">
                                        <label for="courier_id" class="form-label">Courier</label>
                                        <select class="form-select" id="courier_id" wire:model="courier_id">
                                            <option selected value="">Select</option>
                                            @forelse ($couriers as $courier)
                                                <option value='{{ $courier->id }}'>{{ $courier->name }}</option>
                                            @empty
                                            @endforelse
                                        </select>
                                        @error('courier_id')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col-md-2">
                                        <label for="accepted" class="form-label">Verified & Accepted</label>
                                        <input type="number" id="accepted" class="form-control"
                                            wire:model="samples_accepted">
                                        @error('samples_accepted')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-1 col-md-2">
                                        <label for="rejected" class="form-label">Rejected</label>
                                        <input type="number" id="rejected" class="form-control"
                                            wire:model="samples_rejected" readonly>
                                        @error('samples_rejected')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-1 col-md-3">
                                        <label for="received_by" class="form-label">Received By</label>
                                        <select class="form-select" id="received_by" wire:model="received_by">
                                            <option selected value="">Select</option>
                                            @forelse ($users as $user)
                                                <option value='{{ $user->id }}'>{{ $user->fullName }}</option>
                                            @empty
                                            @endforelse
                                        </select>
                                        @error('received_by')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb- col-md-1">
                                        <div class="form-check mt-4">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                id="courier_signed" checked wire:model="courier_signed">
                                            <label class="form-check-label" for="courier_signed">Did Courier
                                                Sign?</label>
                                        </div>
                                        @error('courier_signed')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-1 col-md-4">
                                        <label for="comment" class="form-label">Comment</label>
                                        <textarea type="text" id="comment" class="form-control" wire:model.lazy="comment"></textarea>
                                        @error('comment')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-2 text-start mt-4">
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
                    @endif
                </div>

                <div class="card-body">
                    <x-table-utilities display='d-none'>
                        <div>
                            <div class="d-flex align-items-center ml-4 me-2">
                                <label for="orderBy" class="text-nowrap mr-2 mb-0">OrderBy</label>
                                <select wire:model="orderBy" class="form-select">
                                    <option value="batch_no">Batch No</option>
                                    <option value="date_delivered">Delivery Date</option>
                                    <option value="samples_delivered">Samples Delivered</option>
                                    <option value="samples_accepted">Samples Accepted</option>
                                    <option value="id">Latest</option>
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
                                        <th>Batch No</th>
                                        <th>Date Delivered</th>
                                        <th>Delivered</th>
                                        <th>Reffering Facility</th>
                                        <th>Courier</th>
                                        <th>Accepted</th>
                                        <th>Rejected</th>
                                        <th>Received By</th>
                                        <th>Date Received</th>
                                        <th>Accessioned</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($sampleReceptions as $key => $sampleReception)
                                        @if ($sampleReception->samples_accepted != $sampleReception->samples_handled)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>
                                                    @if ($sampleReception->samples_handled == 0)
                                                        <a href="javascript: void(0);" class="action-ico"
                                                            wire:click="editdata({{ $sampleReception->id }})">{{ $sampleReception->batch_no }}
                                                        </a>
                                                    @else
                                                        {{ $sampleReception->batch_no }}
                                                    @endif

                                                </td>
                                                <td>{{ date('d-m-Y H:i', strtotime($sampleReception->date_delivered)) }}
                                                </td>
                                                <td>{{ $sampleReception->samples_delivered }}</td>
                                                <td>{{ $sampleReception->facility->name }}</td>
                                                <td>{{ $sampleReception->courier->name }}</td>
                                                <td>{{ $sampleReception->samples_accepted }}</td>
                                                <td>{{ $sampleReception->samples_rejected }}</td>
                                                <td>{{ $sampleReception->receiver->fullName }}</td>
                                                <td>{{ $sampleReception->created_at }}</td>
                                                <td>{{ $sampleReception->samples_handled }}</td>
                                                @if ($sampleReception->status == 'Reviewed')
                                                    <td><span
                                                            class="badge bg-info">{{ $sampleReception->status }}</span>
                                                    </td>
                                                @endif
                                                <td class="table-action">
                                                    <a href="javascript:;"
                                                        class="action-ico btn btn-outline-success mx-1"
                                                        data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                        title="" data-bs-original-title="View details"
                                                        aria-label="Views" data-bs-toggle="modal"
                                                        wire:click="showData({{ $sampleReception->id }})"
                                                        data-bs-target="#show-data"><i class="bi bi-eye-fill"></i></a>
                                                    @if (Auth::user()->hasPermission(['accession-samples']))
                                                        <a href="{{ route('specimen-request', $sampleReception->batch_no) }}"
                                                            data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                            title="" data-bs-original-title="Accession Samples"
                                                            class="action-ico btn btn-outline-info mx-1"> <i
                                                                class="bi bi-pencil-square"></i></a>
                                                    @endif

                                                    @if ($sampleReception->samples_handled == 0 && Auth::user()->hasPermission(['create-reception-info']))
                                                        <a href="javascript: void(0);" data-bs-toggle="tooltip"
                                                            data-bs-placement="bottom" title=""
                                                            data-bs-original-title="Delete Record"
                                                            wire:click="deleteConfirmation({{ $sampleReception->id }})"
                                                            class="action-ico btn btn-outline-danger mx-1">
                                                            <i class="bi bi-trash"></i></a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                        </div> <!-- end preview-->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="btn-group float-end">
                                    {{ $sampleReceptions->links() }}
                                </div>
                            </div>
                        </div>
                    </div> <!-- end tab-content-->
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->

        {{-- VIEW BATCH DETAILS --}}
        <div wire:ignore.self class="modal fade" id="show-data" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Batch (<span
                                class="text-info">{{ $batch_no }}</span>) Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"
                            wire:click="close()"></button>
                    </div> <!-- end modal header -->
                    <div class="modal-body">
                        <div class="row row-cols-1 row-cols-xl-2 row-cols-xxl-3">
                            <div class="col-md-7">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-box bg-light-primary border-0">
                                        <i class="bi bi-prescription text-success"></i><i
                                            class='bx bxs-vial text-success'></i>
                                    </div>
                                    <div class="info">
                                        <p class="mb-1"><strong>Batch No</strong> : {{ $batch_no }}
                                            @if ($batch_status == 'Pending')
                                                <span class="badge bg-warning">{{ $batch_status }}</span>
                                            @elseif($batch_status == 'Processing')
                                                <span class="badge bg-info">{{ $batch_status }}</span>
                                            @else
                                                <span class="badge bg-success">{{ $batch_status }}</span>
                                            @endif
                                        </p>
                                        <p class="mb-1"><strong>Date Delivered</strong> :
                                            {{ $delivery_date }}</p>
                                        <p class="mb-1"><strong>Source Facility</strong> :
                                            {{ $facility_name }}</p>
                                        <p class="mb-1"><strong>Samples Delivered</strong> :
                                            {{ $delivered_samples }}</p>
                                        <p class="mb-1"><strong>Samples Accepted</strong> :
                                            {{ $accepted }}</p>
                                        <p class="mb-1"><strong class="text-danger">Samples
                                                Rejected</strong> : {{ $rejected }}</p>
                                        <p class="mb-1"><strong>Received By</strong> : {{ $receiver }}
                                        </p>
                                        <p class="mb-1"><strong>Samples Handled</strong> :
                                            {{ $handled }}</p>
                                        <p class="mb-1"><strong>Reviewed By</strong> :
                                            {{ $reviewer }}</p>
                                        <p class="mb-1"><strong>Date Reviewed</strong> :
                                            {{ $review_date }}</p>
                                        <div>
                                            <h6 class="text-success">Comment</h6>
                                            <p>{{ $comment??'N/A' }}</p>
                                        </div>
                                </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="card border shadow-none radius-10">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="icon-box bg-light-primary border-0">
                                                <i class="bi bi-truck text-success"></i>
                                            </div>
                                            <div class="info">
                                                <h6 class="mb-2">Courier</h6>
                                                <p class="mb-1"><strong>Name</strong> : {{ $courier_name }}</p>
                                                <p class="mb-1"><strong>Contact</strong> : {{ $courier_contact }}
                                                </p>
                                                <p class="mb-1"><strong>Email</strong> : {{ $courier_email }}</p>
                                                <p class="mb-1"><strong>Signed?</strong> : {{ $signed_by_courier }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!--end row-->

                    </div>
                </div> <!-- end modal content-->
            </div> <!-- end modal dialog-->
        </div> <!-- end modal-->

        {{-- //DELETE CONFIRMATION MODAL --}}
        @if (Auth::user()->hasPermission(['create-reception-info']))
            <div wire:ignore.self class="modal fade" id="delete_modal" tabindex="-1" data-backdrop="static"
                data-keyboard="false" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Delete Reception Data</h5>
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

            {{-- ADD FACILITY --}}
            <div wire:ignore.self class="modal fade" id="addFacility" data-bs-backdrop="static"
                data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">Add New Facility</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"
                                wire:click="close()"></button>
                        </div> <!-- end modal header -->
                        <div class="modal-body">
                            <form wire:submit.prevent="storeFacility">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="facilityName" class="form-label">Facility Name</label>
                                            <input type="text" id="facilityName" class="form-control"
                                                name="facilityname" wire:model.lazy="facilityname">
                                            @error('facilityname')
                                                <div class="text-danger text-small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="type" class="form-label">Type</label>
                                            <select class="form-select" id="type" wire:model="facility_type">
                                                <option selected value="">Select</option>
                                                <option value='Institution'>Institution</option>
                                                <option value='Health Facility'>Health Facility</option>
                                            </select>
                                            @error('facility_type')
                                                <div class="text-danger text-small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="parent" class="form-label">Parent</label>
                                            <select class="form-select" id="parent"
                                                wire:model="facility_parent_id">
                                                <option selected value="">None</option>
                                                @forelse ($facilities as $facility)
                                                    <option value='{{ $facility->id }}'>{{ $facility->name }}
                                                    </option>
                                                @empty
                                                @endforelse
                                            </select>
                                            @error('facility_parent_id')
                                                <div class="text-danger text-small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="isActive" class="form-label">Status</label>
                                            <select class="form-select" id="isActive" wire:model="facility_status">
                                                <option selected value="">Select</option>
                                                <option value='1'>Active</option>
                                                <option value='0'>Inactive</option>
                                            </select>
                                            @error('facility_status')
                                                <div class="text-danger text-small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div> <!-- end col -->
                                </div>
                                <!-- end row-->
                                <div class="modal-footer">
                                    <x-button>{{ __('Save') }}</x-button>
                                    <x-button type="button" class="btn btn-danger" wire:click="close()"
                                        data-bs-dismiss="modal">{{ __('Close') }}</x-button>
                                </div>
                            </form>
                        </div>
                    </div> <!-- end modal content-->
                </div> <!-- end modal dialog-->
            </div> <!-- end modal-->

            {{-- ADD COURIER --}}
            <div wire:ignore.self class="modal fade" id="addCourier" data-bs-backdrop="static"
                data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">Add New Courier</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"
                                wire:click="close()"></button>
                        </div> <!-- end modal header -->
                        <div class="modal-body">
                            <form wire:submit.prevent="storeCourier">
                                <div class="row">
                                    <div class="mb-3 col-md-4">
                                        <label for="courierName" class="form-label">Name</label>
                                        <input type="text" id="courierName" class="form-control"
                                            name="couriername" wire:model.lazy="couriername">
                                        @error('couriername')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label for="couriercontact" class="form-label">Contact</label>
                                        <input type="text" id="couriercontact" class="form-control"
                                            wire:model.lazy="couriercontact">
                                        @error('couriercontact')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label for="courierEmail" class="form-label">Email</label>
                                        <input type="email" id="courierEmail" class="form-control"
                                            wire:model.lazy="courieremail">
                                        @error('courieremail')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col-md-5">
                                        <label for="facility" class="form-label">Facility</label>
                                        <select class="form-select" id="facility" wire:model="courierfacility"
                                            wire:change="getStudies">
                                            <option selected value="">Select</option>
                                            @forelse ($facilities as $facility)
                                                <option value='{{ $facility->id }}'>{{ $facility->name }}</option>
                                            @empty
                                            @endforelse
                                        </select>
                                        @error('courierfacility')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3 col-md-5">
                                        <label for="study_id" class="form-label">Study/project</label>
                                        <select class="form-select" id="study_id" wire:model="courierstudy">
                                            @if ($courierfacility && !$studies->isEmpty())
                                                <option selected value="">Select/None</option>
                                                @foreach ($studies as $study)
                                                    <option value='{{ $study->id }}'>{{ $study->name }}</option>
                                                @endforeach
                                            @else
                                                <option selected value="">None</option>
                                            @endif
                                        </select>
                                        @error('courierstudy')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3 col-md-2">
                                        <label for="isActive2" class="form-label">Status</label>
                                        <select class="form-select" id="isActive2" wire:model="courierstatus">
                                            <option selected value="">Select</option>
                                            <option value='1'>Active</option>
                                            <option value='0'>Inactive</option>
                                        </select>
                                        @error('courierstatus')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <!-- end row-->
                                <div class="modal-footer">
                                    <x-button>{{ __('Save') }}</x-button>
                                    <x-button type="button" class="btn btn-danger" wire:click="close()"
                                        data-bs-dismiss="modal">{{ __('Close') }}</x-button>
                                </div>
                            </form>
                        </div>
                    </div> <!-- end modal content-->
                </div> <!-- end modal dialog-->
            </div> <!-- end modal-->
        @endif

        @push('scripts')
            <script>
                window.addEventListener('close-modal', event => {
                    $('#show-data').modal('hide');
                    $('#addFacility').modal('hide');
                    $('#addCourier').modal('hide');
                    $('#delete_modal').modal('hide');
                    $('#show-delete-confirmation-modal').modal('hide');
                });

                window.addEventListener('delete-modal', event => {
                    $('#delete_modal').modal('show');
                });

                window.addEventListener('show-modal', event => {
                    $('#show-data').modal('show');
                });
            </script>
        @endpush
    </div>
</div>
