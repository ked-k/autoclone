<div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="crad-header">
                    @include('livewire.admin.reports.inc.system-report-header')
                </div>
                @if ($report)
                @endif
                <div class="card-body">
                    <form wire:submit.prevent="storeData">
                        <div class="row">
                            <div class="col-md-4 form-group mb-3">
                                <label for="module" class="form-label">Module</label>
                                <select id="module" wire:model.lazy="module" class="form-control">
                                    <option value="">Select</option>
                                    <option value="Sample Management">Sample Management</option>
                                    <option value="Participant Management">Participant Management</option>
                                    <option value="Result Management">Result Management</option>
                                    <option value="Requester Information">Requester Information</option>
                                    <option value="Courier Information">Courier Information</option>
                                </select>
                                @error('module')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label for="score" class="form-label">Score <small class="text-warning">Out of
                                        10</small></label>
                                <input type="number" max="10" step="any" id="score"
                                    wire:model.lazy="score" class="form-control">
                                @error('score')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label for="score" class="form-label">Result</label>
                                <select id="result" wire:model.lazy="result" class="form-control">
                                    <option value="">Select</option>
                                    <option value="Passed">Passed</option>
                                    <option value="Average">Average</option>
                                    <option value="Failed">Failed</option>
                                </select>
                                @error('result')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div wire:ignore class="col-md-10 form-group mb-3">
                                <label for="details" class="form-label">Extra Details</label>
                                <textarea wire:model="details" class="form-control required" name="details" id="details"></textarea>
                                {{-- <x-error-message :value="__('message')" /> --}}

                            </div>
                            <div class="col-md-2">
                                <x-button class="mt-3">{{ __('Save') }}</x-button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0 w-100 sortable" id="datableButton">
                            <thead>
                                <tr>
                                    <td>No.</td>
                                    <td>Module</td>
                                    <td>Score</td>
                                    <td>Result</td>
                                    <td>Details</td>
                                    <td>Action</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reportItems as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->module }}</td>
                                        <td>{{ $item->score ??0 }}</td>
                                        <td>{{ $item->result }}</td>
                                        <td>{!! $item->details !!}</td>
                                        <td>
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
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            ClassicEditor
                .create(document.querySelector('#details'))
                .then(editor => {
                    editor.model.document.on('change:data', () => {
                        @this.set('details', editor.getData());
                    })
                })
                .catch(error => {
                    console.error(error);
                });
        </script>
    @endpush
</div>
