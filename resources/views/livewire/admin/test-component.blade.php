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
                                        Tests
                                    @else
                                        Update Test Information
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
                            @if (!$toggleForm) wire:submit.prevent="storeTest"
                        @else
                        wire:submit.prevent="updateTest" @endif>
                            <div class="row">
                                <div class="mb-2 col-md-2">
                                    <label for="category" class="form-label">{{ __('Category') }}</label>
                                    <select wire:model='category_id' class="form-select" id="category"
                                        wire:model="category_id">
                                        <option selected value="">Select</option>
                                        @foreach ($testCategories as $category)
                                            <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
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
                                <div class="mb-2 col-md-2">
                                    <label for="short_code" class="form-label">Short Code</label>
                                    <input type="text" id="short_code" class="form-control"
                                        wire:model.lazy="short_code">
                                    @error('short_code')
                                        <div class="text-danger text-small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-2 col-md-2">
                                    <div class="form-group">
                                        <label for="tat" class="form-label">{{ __('TAT') }}</label>
                                        <div class="input-group form-group mb-2">
                                            <input type="number" step="any" class="form-control" id="tat"
                                                wire:model.lazy='tat'>
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    Hours
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    @error('tat')
                                        <div class="text-danger text-small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-2 col-md-2">
                                    <div class="form-group">
                                        <label for="price" class="form-label">{{ __('Price') }}</label>
                                        <div class="input-group form-group mb-2">
                                            <input type="number" step="any" class="form-control" id="price"
                                                wire:model.lazy='price'>
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    UGX
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    @error('price')
                                        <div class="text-danger text-small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-2 col-md-2">
                                    <label for="reference_range_min" class="form-label">Min-Ref range</label>
                                    <input type="number" step="any" wire:model.lazy='reference_range_min'
                                        class="form-control" id="reference_range_min">
                                    @error('reference_range_min')
                                        <div class="text-danger text-small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-2 col-md-2">
                                    <label for="reference_range_max" class="form-label">Max-Ref range</label>
                                    <input type="number" step="any" wire:model.lazy='reference_range_max'
                                        class="form-control" id="reference_range_max">
                                    @error('reference_range_max')
                                        <div class="text-danger text-small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3 col-md-2">
                                    <label for="isActive" class="form-label">Status</label>
                                    <select class="form-select" id="isActive" wire:model="status">
                                        <option selected value="">Select</option>
                                        <option value='1'>Active</option>
                                        <option value='0'>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="text-danger text-small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="precautions" class="form-label">{{ __('Precautions') }}</label>
                                    <textarea name="precautions" id="precautions" rows="2" wire:model.lazy='precautions' class="form-control"
                                        placeholder="{{ __('Precautions') }}"></textarea>
                                    @error('precautions')
                                        <div class="text-danger text-small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <hr>

                                <div class="col-md-6">
                                    <h6>
                                        {{ __('Attach Result Type') }}
                                        {{-- Absolute results:{{ var_export($absolute_results) }} --}}
                                    </h6>
                                    <div class="row">
                                        <div
                                            class=" 
                                        @if ($result_type === 'Measurable' || $result_type === 'Absolute') col-md-4
                                        @else
                                        col-md-12 @endif
                                         mb-2">
                                            <label for="result_type" class="form-label">{{ __('Type') }}</label>
                                            <select name="result_type" id="result_type" class="form-select"
                                                wire:model="result_type">
                                                <option value="">Select</option>
                                                <option value="Text">Text</option>
                                                <option value="File">File/Attachment</option>
                                                <option value="Absolute">Absolute</option>
                                                <option value="Measurable">Measurable</option>
                                                <option value="Link">Link</option>
                                            </select>
                                        </div>
                                        @if ($result_type === 'Absolute')
                                            <div id="resultoption" class="col-md-8 mb-2">
                                                <label for="results" class="form-label">{{ __('Results') }}</label>
                                                <button class="btn btn-outline-success mb-1" type="button"
                                                    id="button-addon2" wire:click.prevent="addResult">+</button>
                                                @foreach ($dynamicResults as $index => $result)
                                                    <div class="input-group mb-3">
                                                        <input type="text" class="form-control"
                                                            placeholder="Enter Result"
                                                            aria-label="Add another possible result"
                                                            aria-describedby="button-addon2"
                                                            wire:model="dynamicResults.{{ $index }}.result">
                                                        <button class="btn btn-outline-danger" type="button"
                                                            id="button-addon2"
                                                            wire:click.prevent="removeResult({{ $index }})">Delete</button>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                        @if ($result_type === 'Measurable')
                                            <div id="uom" class="col-md-8 mb-2">
                                                <label for="measurable_result_uom"
                                                    class="form-label">{{ __('Unit of Measure') }}</label>
                                                <div class="input-group form-group">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">
                                                            {{ __('Unit') }}
                                                        </span>
                                                    </div>
                                                    <input type="text" class="form-control"
                                                        id="measurable_result_uom"
                                                        wire:model.lazy="measurable_result_uom">
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h6>
                                        {{ __('Test Comments') }}
                                        {{-- Comments:{{ var_export($comments) }} --}}
                                    </h6>
                                    <div class="row">
                                        <div id="test-comments" class="col-md-12">
                                            <label class="form-label">{{ __('Comments') }}</label>
                                            <button class="btn btn-outline-success mb-1" type="button"
                                                id="button-addon2" wire:click.prevent="addComment">+</button>
                                            @foreach ($dynamicComments as $index => $comment)
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter Comment"
                                                        aria-label="Add another possible Comment"
                                                        aria-describedby="button-addon2"
                                                        wire:model="dynamicComments.{{ $index }}.comment">
                                                    <button class="btn btn-outline-danger" type="button"
                                                        id="button-addon2"
                                                        wire:click.prevent="removeComment({{ $index }})">Delete</button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
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
                                    <option value="price">Price</option>
                                    <option value="id">Latest</option>
                                    <option value="status">Status</option>
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
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Action</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($tests as $key => $test)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $test->name }}</td>
                                            <td>{{ $test->category->category_name }}</td>
                                            <td>{{ $test->price }}</td>
                                            @if ($test->status == 1)
                                                <td><span class="badge bg-success">Active</span></td>
                                            @else
                                                <td><span class="badge bg-danger">Suspended</span></td>
                                            @endif
                                            <td class="table-action">
                                                <a href="javascript: void(0);"
                                                    class="action-ico btn btn-outline-info mx-1"
                                                    wire:click="editTest({{ $test->id }})"
                                                    data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                    title="" data-bs-original-title="Edit Test"
                                                    class="action-ico btn btn-outline-danger mx-1"> <i
                                                        class="bi bi-pencil-square"></i></a>
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
                                    {{ $tests->links('vendor.livewire.bootstrap') }}
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
