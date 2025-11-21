<div>
    <!-- Header -->
    <div class="card" wire:key="referral-manager">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-exchange-alt"></i>
                Outgoing Referral Management
            </h5>
        </div>
        <div class="card-body">
            <!-- Request Code Input -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="requestCode" class="form-label">Referral Request Code</label>
                    <div class="input-group">
                        <input type="text"
                               wire:model="requestCode"
                               class="form-control"
                               placeholder="Enter request code (e.g., ExREF251018-001O)"
                               id="requestCode">
                        <button wire:click="loadReferralRequest"
                                wire:loading.attr="disabled"
                                class="btn btn-primary"
                                type="button">
                            <span wire:loading.remove>Load Request</span>
                            <span wire:loading>
                                <i class="fas fa-spinner fa-spin"></i> Loading...
                            </span>
                        </button>
                    </div>
                    @error('requestCode') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Loading State -->
            <div wire:loading wire:target="loadReferralRequest" class="text-center py-4">
                <i class="fas fa-spinner fa-spin fa-2x"></i>
                <p class="mt-2">Loading referral request...</p>
            </div>

            <!-- Error Message -->
            @if($error)
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> {{ $error }}
                    <button type="button" class="btn-close" wire:click="$set('error', null)"></button>
                </div>
            @endif

            <!-- Success Message -->
            @if($success)
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ $success }}
                    <button type="button" class="btn-close" wire:click="$set('success', null)"></button>
                </div>
            @endif

            <!-- Referral Request Details -->
            @if($referralRequest)
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Referral Request Details</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Request Code:</strong> {{ $referralRequest['request_no'] }}<br>
                                        <strong>Status:</strong>
                                        <span class="badge bg-warning">{{ $referralRequest['status'] }}</span><br>
                                        <strong>Samples Expected:</strong> {{ $referralRequest['no_of_samples'] }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Sample Type:</strong> {{ $referralRequest['sample_type'] }}<br>
                                        <strong>Pathogen:</strong> {{ $referralRequest['pathogen'] }}<br>
                                        <strong>Priority:</strong> {{ $referralRequest['priority'] }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Destination:</strong> {{ $referralRequest['destination_institution']['name']??'N/A' }}<br>
                                        <strong>Pending Samples:</strong>
                                        <span class="badge bg-info">{{ $pendingCount }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Existing Samples -->
                @if(!empty($existingSamples))
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6>Already Attached Samples ({{ count($existingSamples) }})</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Sample ID</th>
                                            <th>Type</th>
                                            <th>Collection Date</th>
                                            <th>Age/Gender</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($existingSamples as $sample)
                                            <tr>
                                                <td>{{ $sample['sample_id'] }}</td>
                                                <td>{{ $sample['specimen_type'] }}</td>
                                                <td>{{ \Carbon\Carbon::parse($sample['collection_date'])->format('M d, Y') }}</td>
                                                <td>{{ $sample['age'] }}y / {{ $sample['gender'] }}</td>
                                                <td>
                                                    <span class="badge bg-success">{{ $sample['status'] }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Available Samples Section -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Available Samples from LIMS</h6>
                            </div>
                            <div class="card-body">
                                <!-- Search and Filters -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <input type="text"
                                               wire:model.debounce.300ms="searchTerm"
                                               class="form-control"
                                               placeholder="Search samples...">
                                    </div>
                                    <div class="col-md-3">
                                        <select wire:model="filters.sample_type" class="form-control">
                                            <option value="">All Sample Types</option>
                                            <option value="Blood">Blood</option>
                                            <option value="Swab">Swab</option>
                                            <option value="Saliva">Saliva</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button wire:click="loadAvailableSamples" class="btn btn-outline-secondary">
                                            <i class="fas fa-sync-alt"></i> Refresh
                                        </button>
                                    </div>
                                </div>

                                <!-- Samples Table -->
                                @if(!empty($availableSamples))
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th width="50px">
                                                        <input type="checkbox"
                                                               wire:model="selectAll"
                                                               wire:click="toggleSelectAll">
                                                    </th>
                                                    <th>Sample ID</th>
                                                    <th>Lab No</th>
                                                    <th>Type</th>
                                                    <th>Collection Date</th>
                                                    <th>Age/Gender</th>
                                                    {{-- <th>Result</th> --}}
                                                    <th>Participant</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($availableSamples as $sample)
                                                    <tr>
                                                        <td>
                                                            <input type="checkbox"
                                                                   wire:model="selectedSamples"
                                                                   value="{{ $sample['id'] }}">
                                                        </td>
                                                        <td>{{ $sample['sample_id'] }}</td>
                                                        <td>{{ $sample['lab_no'] }}</td>
                                                        <td>{{ $sample['specimen_type'] }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($sample['collection_date'])->format('M d, Y') }}</td>
                                                        <td>{{ $sample['age'] }}y / {{ $sample['gender'] }}</td>
                                                        {{-- <td>
                                                            <span class="badge {{ $sample['test_result'] === 'Positive' ? 'bg-danger' : 'bg-success' }}">
                                                                {{ $sample['test_result'] }}
                                                            </span>
                                                        </td> --}}
                                                        <td>{{ $sample['participant']['participant_no'] }}</td>
                                                        <td>
                                                            <button wire:click="addSingleSample({{ $sample['id'] }})"
                                                                    class="btn btn-sm btn-primary">
                                                                Send
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Add Selected Button -->
                                    <div class="mt-3">
                                        <button wire:click="addSelectedSamples"
                                                wire:loading.attr="disabled"
                                                class="btn btn-success"
                                                {{ empty($selectedSamples) ? 'disabled' : '' }}>
                                            <span wire:loading.remove>
                                                <i class="fas fa-plus"></i>
                                                Add Selected Samples ({{ count($selectedSamples) }})
                                            </span>
                                            <span wire:loading>
                                                <i class="fas fa-spinner fa-spin"></i>
                                                Adding Samples...
                                            </span>
                                        </button>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-search fa-2x text-muted"></i>
                                        <p class="mt-2 text-muted">No samples found matching your criteria.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
