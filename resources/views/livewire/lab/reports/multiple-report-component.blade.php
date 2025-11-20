<div>
    <div class="row">
        <div class="col-md-12">
            <h3 class="text-center">Multiple Report Generation</h3>
        </div>
        <div class="col-md-6 mb-3">
            <label for="comment" class="form-label required">{{ __('Study') }}</label>
            <select wire:model="study_id" id="study_id" class="form-select @error('study_id') is-invalid @enderror">
                <option value="">Select study</option>
                @foreach ($studies as $study)
                    <option value="{{ $study->id }}">{{ $study->name }}</option>
                @endforeach
            </select>
            @error('study_id')
                <div class="text-danger text-small">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label for="comment" class="form-label required">{{ __('Search Type') }}</label>

            <select wire:model="search_type" id="search_type"
                class="form-select @error('search_type') is-invalid @enderror">
                <option value="">Select Search Type</option>
                <option value="single">Single Result</option>
                <option value="participant">Participant</option>
                <option value="sample">Sample</option>
                <option value="lab_no">Lab Number</option>
                <option value="batch">Batch Number</option>
                <option value="custom_list">Custom ID List</option>
            </select>
        </div>

        <div class="col-md-12 mb-3">
            <label for="identifiers" class="form-label required">Enter look-ups comma-separated</label>
            <textarea id="identifiers" class="form-control" placeholder="Enter {{ $search_type }} comma-separated"
                wire:model="identifiers"></textarea>

        </div>
    </div>
    <button class="btn btn-success m-1 btn-sm" wire:click = "printResults">Generate Results</button>

    @if ($results && count($results) > 0)
        @include('livewire.lab.reports.inc.multiple-results')
    @else
        <div class="alert alert-info mt-3">
            No results found for the provided identifiers.
        </div>
    @endif
</div>
