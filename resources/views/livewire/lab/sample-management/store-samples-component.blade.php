<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pt-0">
                    <div class="row mb-2">
                        <div class="col-sm-12 mt-3">
                            <div class="d-sm-flex align-items-center">
                                <h5 class="mb-2 mb-sm-0">
                                    <h6 class="modal-title" id="staticBackdropLabel">Store Sample (<span
                                            class="text-danger">{{ $sample_identity }}</span>) with Lab_No <span
                                            class="text-info">{{ $lab_no }}</span></h6>
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="storeSample">
                        <div class="row">
                            <div class="mb-2 col-md-2">
                                <label for="barcode" class="form-label">Sample Barcode</label>
                                <input type="text" wire:model.lazy="barcode"class="form-control" placeholder="{{ __('barcode') }}">

                                @error('barcode')
                                    <div class="text-danger text-small">
                                        {{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-2 col-md-3">
                                <label for="freezer_id" class="form-label">{{ __('Freezer ->(Room)') }}</label>
                                <select wire:model='freezer_id' class="form-select" id="freezer_id">
                                    <option selected value="">Select</option>
                                    @foreach ($freezers as $freezer)
                                        <option value="{{ $freezer->id }}">
                                            {{ $freezer->name }} ({{ $freezer->location->name }})</option>
                                    @endforeach
                                </select>
                                @error('freezer_id')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-2 col-md-2">
                                <label for="section_id" class="form-label">{{ __('Section/Shelf') }}</label>
                                <select wire:model='section_id' class="form-select" id="section_id">
                                    <option selected value="">Select</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                </select>
                                @error('section_id')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-2 col-md-1">
                                <label for="rack_id" class="form-label">{{ __('Column/Rack') }}</label>
                                <select wire:model='rack_id' class="form-select" id="rack_id">
                                    <option selected value="">Select</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                </select>
                                @error('rack_id')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-2 col-md-1">
                                <label for="drawer_id" class="form-label">{{ __('Drawer') }}</label>
                                <select wire:model='drawer_id' class="form-select" id="drawer_id">
                                    <option selected value="">Select</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                </select>
                                @error('drawer_id')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-2 col-md-1">
                                <label for="box_id" class="form-label">{{ __('Box') }}</label>
                                <select wire:model='box_id' class="form-select" id="box_id">
                                    <option selected value="">Select</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                </select>
                                @error('box_id')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-2 col-md-1">
                                <label for="box_column" class="form-label">{{ __('Well Column') }}</label>
                                <select wire:model='box_column' class="form-select" id="box_column">
                                    <option selected value="">Select</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                    <option value="D">D</option>
                                    <option value="E">E</option>
                                    <option value="F">F</option>
                                    <option value="G">G</option>
                                    <option value="H">H</option>
                                    <option value="I">I</option>
                                    <option value="J">J</option>
                                    <option value="K">K</option>
                                    <option value="L">L</option>
                                </select>
                                @error('box_column')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-2 col-md-1">
                                <label for="box_row" class="form-label">{{ __('Well Row') }}</label>
                                <select wire:model='box_row' class="form-select" id="box_row">
                                    <option selected value="">Select</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                </select>
                                @error('box_row')
                                    <div class="text-danger text-small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-2 col-md-6">
                                <label for="comment" class="form-label">Comment</label>
                                <textarea wire:model.lazy="comment" rows="3" class="form-control" placeholder="{{ __('comment') }}"></textarea>

                                @error('comment')
                                    <div class="text-danger text-small">
                                        {{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <x-button>{{ __('Save') }}</x-button>
                        </div>
                    </form>
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
</div>
