<div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header pt-0">
                    <div class="row mb-2">
                        <div class="col-sm-12 mt-3">
                            <div class="d-sm-flex align-items-center">
                                <h5 class="mb-2 mb-sm-0">
                                    Samples/Specimens
                                </h5>
                                <div class="ms-auto">
                                    <a type="button" class="btn btn-outline-info" wire:click="refresh()"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                        data-bs-original-title="Refresh Table"><i class="bi bi-arrow-clockwise"></i></a>

                                    <a type="button" class="btn btn-info" data-bs-toggle="modal"
                                        data-bs-target="#modalAdd">Add Sample</a>
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
                                    <td>Sample</td>
                                    <td>Possible Tests</td>
                                    <td>Status</td>
                                    <td>Action</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sampleType as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->type }}</td>
                                        <td>{{ count($item->possible_tests ?? []) }}</td>
                                        <td>
                                            @if ($item->status == 1)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Suspended</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="javascript:;" class="action-ico btn btn-outline-info mx-1"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                wire:click="editdata({{ $item->id }})" data-target="#edit_modal"
                                                title="Edit"><i class="bi bi-pencil-square"></i></a>
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
                                {{ $sampleType->links('vendor.livewire.bootstrap') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- New sampletype Modal -->
    <div wire:ignore.self class="modal fade" id="modalAdd" tabindex="-1" aria-labelledby="exampleModalLabel"
        role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        Add a new Sample Type
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        wire:click="close()"></button>
                </div>
                <form wire:submit.prevent="storeData">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 form-group mb-3">
                                <label for="name" class="form-label">Sample Type</label>
                                <input type="text" id="type" wire:model.lazy="type" class="form-control">
                                @error('type')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <h6>Attach Possible Tests</h6>
                            <hr>
                            <div class=" col-md-12 form-group mt-2">
                                @foreach ($tests as $test)
                                    <div class="form-check form-check-inline mb-1 test-list" id="test-list">
                                        <input class="form-check-input" type="checkbox" id="testtype{{ $test->id }}"
                                            name="possible_tests[]" value="{{ $test->id }}"
                                            wire:model='possible_tests'>
                                        <label class="form-check-label"
                                            for="testtype{{ $test->id }}">{{ $test->name }}</label>
                                    </div>
                                @endforeach
                                {{-- test:{{ var_export($possible_tests) }} --}}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <x-button>
                            {{ __('Save') }}
                        </x-button>

                        <x-button type="button" class="btn btn-danger" wire:click="close()" data-bs-dismiss="modal">
                            {{ __('Close') }}</x-button>
                    </div>
                </form>
            </div>

        </div>
    </div>


    <!-- Edit sampletype Modal -->
    <div wire:ignore.self id="edit_modal" class="modal fade" tabindex="-1" aria-labelledby="exampleModalLabel"
        role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Sample Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        wire:click="close()"></button>
                </div>
                <form wire:submit.prevent="updateData">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="type" class="form-label">Sample Type</label>
                                <input type="text" id="type" wire:model.lazy="type" class="form-control">
                                @error('type')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" wire:model="status">
                                    <option value="">select</option>
                                    <option value="1" style="color: green" selected>Active</option>
                                    <option value="0" style="color: red">Suspended</option>
                                </select>
                            </div>
                            <h6>Attach Possible Tests</h6>
                            <hr>
                            <div class=" col-md-12 ">
                                @foreach ($tests as $test)
                                    <div class="form-check form-check-inline mb-1 test-list" id="test-list">
                                        <input class="form-check-input" type="checkbox"
                                            id="testtype{{ $test->id }}" name="possible_tests[]"
                                            value="{{ $test->id }}" wire:model='possible_tests'>
                                        <label class="form-check-label"
                                            for="testtype{{ $test->id }}">{{ $test->name }}</label>
                                    </div>
                                @endforeach
                                {{-- test:{{ var_export($possible_tests) }} --}}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <x-button class="btn-success">{{ __('Save') }}</x-button>
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
                    <h5 class="modal-title" id="exampleModalLabel">Delete Sample</h5>
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
