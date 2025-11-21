<div>
    <!-- Fetch Referral Form -->
    <div class="card">
        <div class="card-header">
            <h5>Fetch Referral Request</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <input type="text" wire:model="requestCode" class="form-control" placeholder="Enter Request Code">
                </div>
                <div class="col-md-4">
                    <button wire:click="fetchReferral" class="btn btn-primary">Fetch Referral</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reception Form -->
    @if($referralData)
    @if($showReceptionForm && !$sampleReception)
    <div class="card mt-3">
        <div class="card-header">
            <h5>Create Reception Batch</h5>
        </div>
        <div class="card-body">
            <form wire:submit.prevent="createReceptionBatch">
                <div class="row">
                    <div class="col-md-6">
                        <label>Request No:</label>
                        <input type="text" class="form-control" value="{{ $referralData['request_no'] }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label>Referral From:</label>
                        <input type="text" class="form-control" value="{{ $referralData['requester_institution']['name'] }}" readonly>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-4">
                        <label>Date Delivered *</label>
                        <input type="datetime-local" wire:model="date_delivered" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>Samples Delivered *</label>
                        <input type="number" wire:model="samples_delivered" class="form-control" readonly>
                    </div>
                    <div class="col-md-4">
                        <label>Samples Accepted *</label>
                        <input type="number" wire:model="samples_accepted" class="form-control">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <label>Received By *</label>
                        <input type="text" wire:model="received_by" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label>Courier Signed *</label>
                        <input type="text" wire:model="courier_signed" class="form-control">
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-success">Create Reception Batch</button>
                </div>
            </form>
        </div>
    </div>
    @elseif($sampleReception)
      <div class="row row-cols-1 row-cols-xl-2 row-cols-xxl-3">
                            <div class="col-md-7">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-box bg-light-primary border-0">
                                        <i class="bi bi-prescription text-success"></i><i
                                            class='bx bxs-vial text-success'></i>
                                    </div>
                                    <div class="info">
                                        <p class="mb-1"><strong>Batch No</strong> : {{ $sampleReception->batch_no }}
                                            @if ($sampleReception->batch_status == 'Pending')
                                                <span class="badge bg-warning">{{ $sampleReception->batch_status }}</span>
                                            @elseif($sampleReception->batch_status == 'Processing')
                                                <span class="badge bg-info">{{ $sampleReception->batch_status }}</span>
                                            @else
                                                <span class="badge bg-success">{{ $sampleReception->batch_status }}</span>
                                            @endif
                                        </p>
                                        <p class="mb-1"><strong>Date Delivered</strong> :
                                            {{$sampleReception->date_delivered }}</p>
                                        <p class="mb-1"><strong>Source Facility</strong> :
                                            {{ $sampleReception->facility->name }}</p>
                                        <p class="mb-1"><strong>Samples Delivered</strong> :
                                            {{ $sampleReception->samples_delivered }}</p>
                                        <p class="mb-1"><strong>Samples Accepted</strong> :
                                            {{ $sampleReception->samples_accepted }}</p>
                                        <p class="mb-1"><strong class="text-danger">Samples
                                                Rejected</strong> : {{ $sampleReception->samples_rejected }}</p>
                                        <p class="mb-1"><strong>Samples Handled</strong> :
                                            {{  $sampleReception->samples_handled}}</p>
                                        <p class="mb-1"><strong>Reviewed By</strong> :
                                            {{ $sampleReception->reviewer ? $sampleReception->reviewer->fullName : 'N/A' }}</p>
                                        <p class="mb-1"><strong>Date Reviewed</strong> :
                                            {{ $sampleReception->date_reviewed}}</p>
                                        <div>
                                            <h6 class="text-success">Comment</h6>
                                            <p>{{ $sampleReception->comment ?? 'N/A' }}</p>
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
                                                <p class="mb-1"><strong>Name</strong> : {{ $sampleReception->courier->nam }}</p>
                                                <p class="mb-1"><strong>Contact</strong> : {{ $sampleReception->courier->contact}}
                                                </p>
                                                <p class="mb-1"><strong>Email</strong> : {{ $sampleReception->courier->email}}</p>
                                                <p class="mb-1"><strong>Signed?</strong> : {{ $sampleReception->courier_signed == 1 ? 'Yes' : 'No' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
    @endif
    @endif

    @if($receptionCreated)
    <div class="alert alert-success mt-3">
        Reception batch created! Batch No: <strong>{{ $batchNo }}</strong>
        {{-- <a href="{{ route('referral.accession', $batchNo) }}" class="btn btn-primary float-end">
            Accession Samples
        </a> --}}
    </div>
    <div class="mt-3">
    <div class="card">
        <div class="card-header">
            <h5>Accession Referral Samples - Batch: {{ $batchNo }}</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Sample ID</th>
                        <th>PID</th>
                        <th>Specimen Type</th>
                        <th>Age/Gender</th>
                        <th>Pathogen</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($samples as $index => $sample)
                    <tr>
                        <td>{{ $sample['identifier'] ?? $sample['sample_id'] }}</td>
                        <td>{{ $sample['sample_id'] }}</td>
                        <td>{{ $sample['specimen_type'] }}</td>
                        <td>{{ $sample['age'] }}/{{ $sample['gender'] }}</td>
                        <td>{{ $sample['pathogen'] }}</td>
                        <td>
                                <span class="badge bg-info">{{ $sample['status'] }}</span>
                        </td>
                        <td>
                            @if(in_array($sample['status'] ,['Pending','Delivered','Rejected','Dispatched','Received']))
                            <button wire:click="openAccessionModal({{ $index }})"
                                    class="btn btn-sm btn-primary"
                                    >
                                Accession
                            </button>
                            @else
                                 <a href="{{ URL::signedRoute('sample-search-results', ['sample' => $sample['identifier']]) }}"
                                                        type="button" class="btn btn-sm btn-outline-success"
                                                        data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                        title="" data-bs-original-title="View Details"
                                                        target="_blank"><i class="bi bi-eye"></i>
                                                    </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Accession Modal -->
    @if($showAccessionModal)
    <div class="modal fade show" style="display: block;" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Accession Sample: {{ $selectedSample['identifier'] ?? $selectedSample['sample_id'] }}</h5>
                    <button type="button" wire:click="$set('showAccessionModal', false)" class="btn-close"></button>
                </div>
                <div class="modal-body">
                    <!-- Your existing sample accession form here -->
                    <form wire:submit.prevent="saveSample">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Sample Identity *</label>
                                <input type="text" wire:model="sample_identity" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label>Sample Type *</label>
                                <select wire:model="sample_type_id" class="form-control">
                                    <option value="">Select Sample Type</option>
                                    @foreach($sampleTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->type }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label>Age</label>
                                <input type="number" wire:model="age" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label>Gender</label>
                                <select wire:model="gender" class="form-control">
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Volume</label>
                                <input type="number" wire:model="volume" class="form-control" step="0.1">
                            </div>
                        </div>

                                                               <div class="row mx-auto">
                                            <div class="mb-3 col-md-3">
                                                <label for="requested_by" class="form-label">Requested By</label>
                                                <select class="form-select select2" id="requested_by"
                                                    wire:model="requested_by">
                                                    <option selected value="">Select</option>
                                                    @forelse ($requesters as $requester)
                                                        <option value='{{ $requester->id }}'>
                                                            {{ $requester->name . '(' . $requester->study->name . ')' }}
                                                        </option>
                                                    @empty
                                                    @endforelse
                                                </select>
                                                @error('requested_by')
                                                    <div class="text-danger text-small">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3 col-md-3">
                                                <label for="date_requested" class="form-label">Date Requested</label>
                                                <input id="date_requested" type="date" class="form-control"
                                                    wire:model.lazy="date_requested">
                                                @error('date_requested')
                                                    <div class="text-danger text-small">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            @if ($entry_type == 'Participant')
                                                <div class="mb- col-md-1">
                                                    <div class="form-check mt-4">
                                                        <input class="form-check-input" type="checkbox"
                                                            value="1" id="is_isolate" checked
                                                            wire:model="is_isolate">
                                                        <label class="form-check-label text-success"
                                                            for="is_isolate"><strong>Isolate?</strong></label>
                                                    </div>
                                                    @error('is_isolate')
                                                        <div class="text-danger text-small">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            @endif

                                            <div class="mb-3 col-md-3">
                                                <label for="collected_by" class="form-label">Collected By</label>
                                                <select class="form-select select2" id="collected_by"
                                                    wire:model="collected_by"
                                                    @if ($is_isolate) disabled @endif>
                                                    <option selected value="">Select</option>
                                                    @forelse ($collectors as $collector)
                                                        <option value='{{ $collector->id }}'>{{ $collector->name }}
                                                        </option>
                                                    @empty
                                                    @endforelse
                                                </select>
                                                @error('collected_by')
                                                    <div class="text-danger text-small">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div
                                                class="mb-3 @if ($entry_type == 'Participant') col-md-2 @else col-md-3 @endif">
                                                <label for="date_collected" class="form-label">Collection
                                                    Date/Time</label>
                                                <input id="date_collected" type="datetime-local" class="form-control"
                                                    wire:model.lazy="date_collected"
                                                    @if ($is_isolate) disabled @endif>
                                                @error('date_collected')
                                                    <div class="text-danger text-small">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            @if ($entry_type != 'Client')

                                                <div class="mb-3 col-md-3">
                                                    <label for="study_id" class="form-label">Study</label>
                                                    <select class="form-select select2" id="study_id"
                                                        wire:model="study_id">
                                                        <option selected value="">Select</option>
                                                        @forelse ($studies as $study)
                                                            <option value='{{ $study->id }}'>{{ $study->name }}
                                                            </option>
                                                        @empty
                                                        @endforelse
                                                    </select>
                                                    @error('study_id')
                                                        <div class="text-danger text-small">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                            @endif

                                            <div class="mb-3 col-md-3">
                                                <label for="sample_identity" class="form-label">Sample ID</label>
                                                <input id="sample_identity" type="text" class="form-control"
                                                    wire:model.lazy="sample_identity">
                                                @error('sample_identity')
                                                    <div class="text-danger text-small">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3 col-md-3">
                                                <label for="sample_is_for" class="form-label">Sample is For?<span
                                                        class="text-danger">*</span></label>
                                                <select class="form-select select2" id="sample_is_for"
                                                    wire:model="sample_is_for">
                                                    <option selected value="">Select</option>
                                                    <option value='Testing'>Testing</option>
                                                    <option value='Aliquoting'>Aliquoting</option>
                                                    <option value='Deffered'>Deffered Testing</option>
                                                    <option value='Storage'>Storage</option>
                                                </select>
                                                @error('sample_is_for')
                                                    <div class="text-danger text-small">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3 col-md-3">
                                                <label for="priority" class="form-label">Priority<span
                                                        class="text-danger">*</span></label>
                                                <select class="form-select select2" id="priority"
                                                    wire:model="priority">
                                                    <option selected value="">Select</option>
                                                    <option value='Normal'>Normal</option>
                                                    <option value='Urgent'>Urgent</option>
                                                </select>
                                                @error('priority')
                                                    <div class="text-danger text-small">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mx-auto">
                                            <h6> <strong class="text-success">Sample Delivered</strong>
                                            </h6>
                                            <hr>
                                            @if ($entry_type == 'Participant')
                                                <div class="mb-3 col-md-2">
                                                    <label for="visit" class="form-label">Participant Visit
                                                        @if ($lastVisit)
                                                            (<strong class="text-info"> last:
                                                            </strong>{{ $lastVisit }})
                                                        @endif
                                                    </label>
                                                    <input id="visit" type="text" class="form-control"
                                                        wire:model.lazy="visit">
                                                    @error('visit')
                                                        <div class="text-danger text-small">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            @endif

                                            <div class="mb-3 col-md-8">
                                                <label for="sampleType" class="form-label">Sample<span
                                                        class="text-danger">*</span></label>
                                                <select class="form-select select2" id="sampleType"
                                                    wire:model='sample_type_id'>
                                                    <option selected value="">Select</option>
                                                    @foreach ($sampleTypes as $sampleType)
                                                        <option value='{{ $sampleType->id }}'>
                                                            {{ $sampleType->type }}</option>
                                                    @endforeach
                                                </select>
                                                @error('sample_type_id')
                                                    <div class="text-danger text-small">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-2 col-md-2">
                                                <div class="form-group">
                                                    <label for="volume"
                                                        class="form-label">{{ __('Volume Collected') }}</label>
                                                    <div class="input-group form-group mb-2">
                                                        <input type="number" step="any" class="form-control"
                                                            wire:model.lazy='volume'>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">
                                                                ml
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                @error('volume')
                                                    <div class="text-danger text-small">{{ $message }}</div>
                                                @enderror
                                            </div>

                                        </div>

                                        @if ($sample_is_for == 'Testing' || $sample_is_for == 'Deffered')
                                            <div wire:loading.delay wire:target="updatedSampleTypeId">
                                                <div class="spinner-border text-info" role="status"> <span
                                                        class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
                                            @if (!$tests->isEmpty())
                                                <div class="row mx-auto" wire:loading.class='invisible'>
                                                    <h6> <strong class="text-success">Test(s) Requested</strong>
                                                    </h6>
                                                    <hr>
                                                    <div class="col-md-12">
                                                        @foreach ($tests as $test)
                                                            <div class="form-check form-check-inline mb-1">
                                                                <label class="form-check-label"
                                                                    for="test{{ $test->id }}">{{ $test->name }}</label>
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="test{{ $test->id }}"
                                                                    value="{{ $test->id }}"
                                                                    wire:model='tests_requested'>
                                                            </div>
                                                        @endforeach
                                                        @error('tests_requested')
                                                            <div class="text-danger text-small">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            @else
                                                <div class="row mx-auto" wire:loading.class='invisible'
                                                    wire:target="updatedSampleTypeId">
                                                    <div class="text-danger col-md-12">No associated tests! Please
                                                        select
                                                        sample type</div>
                                                </div>
                                            @endif
                                        @elseif($sample_is_for == 'Aliquoting')
                                            <div wire:loading.delay wire:target="updatedSampleTypeId">
                                                <div class="spinner-border text-info" role="status"> <span
                                                        class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
                                            @if (!$aliquots->isEmpty())

                                                <div class="row mx-auto" wire:loading.class='invisible'>
                                                    <h6> <strong class="text-success">Aliquot(s) Requested</strong>
                                                    </h6>
                                                    <hr>
                                                    <div class="col-md-12">
                                                        @foreach ($aliquots as $aliquot)
                                                            <div class="form-check form-check-inline mb-1">
                                                                <label class="form-check-label"
                                                                    for="aliquot{{ $aliquot->id }}">{{ $aliquot->type }}</label>
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="aliquot{{ $aliquot->id }}"
                                                                    value="{{ $aliquot->id }}"
                                                                    wire:model='aliquots_requested'>
                                                            </div>
                                                        @endforeach
                                                        @error('aliquots_requested')
                                                            <div class="text-danger text-small">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            @else
                                                <div class="row mx-auto" wire:loading.class='invisible'
                                                    wire:target="updatedSampleTypeId">
                                                    <div class="text-danger col-md-12">No associated possible aliquots!
                                                        Please select sample type</div>
                                                </div>
                                            @endif
                                        @else
                                        @endif

                        <div class="mt-3">
                            <button type="submit" class="btn btn-success">Save Sample</button>
                            <button type="button" wire:click="$set('showAccessionModal', false)" class="btn btn-secondary">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
    @endif
</div>
