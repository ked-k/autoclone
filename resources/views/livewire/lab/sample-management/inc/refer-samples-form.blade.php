<div class="col-md-10">
    <div class="row">
        <div class="col-md-6 mb-2">
            <label>Test</label>
            <select wire:model="test_id" class="form-control">
                <option value="">-- Select Test --</option>
                @foreach ($tests as $test)
                    <option value="{{ $test->id }}">{{ $test->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6 mb-2">
            <label>Referral Type</label>
            <select wire:model="referral_type" class="form-control">
                <option value="External">External</option>
                <option value="Internal">Internal</option>
            </select>
        </div>
        <div class="col-md-6 mb-2">
            <label>Referral Lab</label>
            <select wire:model="referralLab_id" class="form-control">
                <option value="">-- Select Laboratory --</option>
                @foreach ($labs as $lab)
                    <option value="{{ $lab->id }}">{{ $lab->name ?? $lab->laboratory_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6 mb-2">
            <label>Reason for Referral</label>
            <select wire:model="reason_id" class="form-control">
                <option value="">-- Select Reason --</option>
                @foreach ($reasons as $reason)
                    <option value="{{ $reason->id }}">{{ $reason->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6 mb-2">
            <label>Referral Code</label>
            <input type="text" wire:model="referral_code" class="form-control" />
        </div>

        <div class="col-md-6 mb-2">
            <label>Courier</label>
            <input type="text" wire:model="courier" class="form-control" />
        </div>

        {{-- <div class="col-md-6 mb-2">
            <label>Storage Condition</label>
            <input type="text" wire:model="storage_condition" class="form-control" placeholder="e.g. 2-8°C" />
        </div>

        <div class="col-md-6 mb-2">
            <label>Transport Medium</label>
            <input type="text" wire:model="transport_medium" class="form-control" />
        </div>

        <div class="col-md-6 mb-2">
            <label>Sample Integrity</label>
            <input type="text" wire:model="sample_integrity" class="form-control" />
        </div>

        <div class="col-md-6 mb-2">
            <label>Temperature on Dispatch (°C)</label>
            <input type="number" step="0.01" wire:model="temperature_on_dispatch" class="form-control" />
        </div> --}}

        <div class="col-md-6 mb-2">
            <label>Date Referred</label>
            <input type="datetime-local" wire:model="date_referred" class="form-control" />
        </div>

        <div class="col-md-6 mb-2">
            <label>Referral Reason Description (Optional)</label>
            <textarea wire:model="reason" class="form-control"></textarea>
        </div>

        <div class="col-md-12 mb-2">
            <label>Additional Notes</label>
            <textarea wire:model="additional_notes" class="form-control"></textarea>
        </div>
    </div>
</div>
