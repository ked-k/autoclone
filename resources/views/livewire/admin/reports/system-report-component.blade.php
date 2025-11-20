<div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header pt-0">
                    <div class="row mb-2">
                        <div class="col-sm-12 mt-3">
                            <div class="d-sm-flex align-items-center">
                                <h5 class="mb-2 mb-sm-0">
                                    Quality Reports
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
                </div>

                <div class="card-body">
                    <form wire:submit.prevent="storeData">
                        <div class="row">
                            <div class="col-md-8 form-group mb-3">
                                <label for="name" class="form-label">Report Date</label>
                                <input type="date" max="{{ date('d-m-Y') }}" id="report_date"
                                    wire:model.lazy="report_date" class="form-control">
                                @error('report_date')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <x-button class="mt-3">{{ __('Create Report') }}</x-button>
                            </div>
                        </div>
                    </form>
                    <hr>
                    <x-table-utilities>
                        <div>
                            <div class="d-flex align-items-center ml-4 me-2">
                                <label for="orderBy" class="text-nowrap mr-2 mb-0">OrderBy</label>
                                <select wire:model="orderBy" class="form-select">
                                    <option value="type">Type</option>
                                    <option value="id">Latest</option>
                                    <option value="status">Status</option>
                                </select>
                            </div>
                        </div>
                    </x-table-utilities>
                    <div class="table-responsive">
                        <table class="table table-striped mb-0 w-100 sortable" id="datableButton">
                            <thead>
                                <tr>
                                    <td>No.</td>
                                    <td>Reference</td>
                                    <td>Report Date</td>
                                    <td>Created By</td>
                                    <td>Status</td>
                                    <td>Action</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($systemReports as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->ref_code }}</td>
                                        <td>{{ $item->report_date }}</td>
                                        <td>{{ count($item->possible_aliquots ?? []) }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $item->status }}</span>
                                        </td>
                                        <td>
                                            <a href="javascript:;" class="action-ico btn btn-outline-info mx-1"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                wire:click="editdata('{{ $item->ref_code }}'')"
                                                data-target="#edit_modal" title="Edit"><i
                                                    class="bi bi-pencil-square"></i></a>
                                            @if (Auth::user()->hasPermission(['master-access']))
                                                <a href="javascript:;" class="action-ico btn btn-outline-danger mx-1"
                                                    data-bs-toggle="tooltip"
                                                    wire:click="deleteConfirmation({{ $item->id }})"
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
                                {{ $systemReports->links('vendor.livewire.bootstrap') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->

    <div wire:ignore.self class="modal fade" id="delete_modal" tabindex="-1" data-backdrop="static"
        data-keyboard="false" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Delete Sample</h5>
                    <button type="button" class="btn-close" wire:click="cancel()" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body pt-4 pb-4">
                    <h6>Are you sure? You want to delete this data!</h6>
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
