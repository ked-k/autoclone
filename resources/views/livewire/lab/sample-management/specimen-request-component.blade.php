<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pt-0">
                    <div class="row mb-2">
                        <div class="col-sm-12 mt-3">
                            <div class="d-sm-flex align-items-center">
                                <h5 class="mb-2 mb-sm-0">
                                    {{-- {{ $participant_id }} --}}
                                    <span class="text-info">{{ $source_facility }}</span> Specimen Request for Batch
                                    <strong class="text-success">{{ $batch_no }}</strong>
                                    (<strong class="text-info">{{ $batch_samples_handled }}</strong>/<strong
                                        class="text-danger">{{ $batch_sample_count }}</strong>)
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
                                            @if ($tabToggleBtn)
                                                <a class="dropdown-item" href="javascript:;"
                                                    wire:click="toggleTab()">Toggle Tabs</a>
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
                    <div class="row mb-0">

                        <div class="card-bod">
                            <ul class="nav nav-tabs nav-primary" role="tablist">

                                @if ($activeParticipantTab)
                                    <li class="nav-item" role="tab">
                                        <a class="nav-link {{ $activeParticipantTab ? 'active' : '' }}"
                                            data-bs-toggle="tab" href="#participant" role="tab"
                                            aria-selected="true">
                                            <div class="d-flex align-items-center">
                                                <div class="tab-icon"><i class='bi bi-person font-18 me-1'></i>
                                                </div>
                                                <div class="tab-title">Participant</div>
                                            </div>
                                        </a>
                                    </li>
                                @endif

                                @if (!$activeParticipantTab)
                                    <li class="nav-item" role="tab">
                                        <a class="nav-link {{ !$activeParticipantTab ? 'active' : '' }}"
                                            data-bs-toggle="tab" href="#sample-tests" role="tab"
                                            aria-selected="false">
                                            <div class="d-flex align-items-center">
                                                <div class="tab-icon"><i class='bx bx-vial font-18 me-1'></i>
                                                </div>
                                                <div class="tab-title">Sample/Tests</div>
                                            </div>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                            <div class="tab-content py-3">
                                <div class="tab-pane fade {{ $activeParticipantTab ? 'show active' : '' }}"
                                    id="participant" role="tabpanel">
                                    <form
                                        @if (!$toggleForm) wire:submit.prevent="storeParticipant"
                                    @else
                                    wire:submit.prevent="updateParticipant" @endif>
                                        <div class="row mx-auto">
                                            <div class="mb-3 col-md-2">
                                                <label for="entry_type" class="form-label">Entry Type</label>
                                                <select class="form-select" id="entry_type" wire:model="entry_type">
                                                    <option selected value="Participant">Participant/Isolate</option>
                                                    <option value="Client">Client</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                                @error('entry_type')
                                                    <div class="text-danger text-small">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            @if ($entry_type == 'Participant' || $entry_type == 'Client')

                                                <div class="mb-3 col-md-2">
                                                    <label for="identity" class="form-label">Participant ID<span
                                                            class="text-danger">*</span>
                                                        @if ($participantMatch)
                                                            <span class="text-success">Matched</span>
                                                        @endif

                                                    </label>
                                                    <input type="text" id="identity"
                                                        class="form-control text-uppercase"
                                                        onkeyup="this.value = this.value.toUpperCase();" size="14"
                                                        wire:model.lazy="identity"
                                                        @if ($entry_type == 'Client') disabled @endif>
                                                    @error('identity')
                                                        <div class="text-danger text-small">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            @endif
                                            @if ($entry_type == 'Participant' || $entry_type == 'Client')
                                                <div class="mb-3 col-md-1">
                                                    <label for="age" class="form-label">Age<span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" id="age" class="form-control"
                                                        wire:model.lazy="age">
                                                    @error('age')
                                                        <div class="text-danger text-small">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3 col-md-2">
                                                    <label for="gender" class="form-label">Gender<span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select select2" data-toggle="select2"
                                                        id="gender" wire:model="gender">
                                                        <option selected value="">Select</option>
                                                        <option value='Male'>Male</option>
                                                        <option value='Female'>Female</option>
                                                    </select>
                                                    @error('gender')
                                                        <div class="text-danger text-small">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3 col-md-3">
                                                    <label for="address" class="form-label">Address<span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" id="address"
                                                        class="form-control text-uppercase"
                                                        onkeyup="this.value = this.value.toUpperCase();"
                                                        wire:model.lazy="address">
                                                    @error('address')
                                                        <div class="text-danger text-small">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3 col-md-2">
                                                    <label for="contact" class="form-label">Contact<span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" id="contact"
                                                        class="form-control text-uppercase"
                                                        onkeyup="this.value = this.value.toUpperCase();"
                                                        wire:model.lazy="contact">
                                                    @error('contact')
                                                        <div class="text-danger text-small">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3 col-md-3">
                                                    <label for="nok_contact" class="form-label">Next of Kin
                                                        Contact<span class="text-danger">*</span></label>
                                                    <input type="text" id="nok_contact"
                                                        class="form-control text-uppercase"
                                                        onkeyup="this.value = this.value.toUpperCase();"
                                                        wire:model.lazy="nok_contact">
                                                    @error('nok_contact')
                                                        <div class="text-danger text-small">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3 col-md-3">
                                                    <label for="nok_address" class="form-label">Next of Kin
                                                        Address</label>
                                                    <input type="text" id="nok_address"
                                                        class="form-control text-uppercase"
                                                        onkeyup="this.value = this.value.toUpperCase();"
                                                        wire:model.lazy="nok_address">
                                                    @error('nok_address')
                                                        <div class="text-danger text-small">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-1 col-md-6">
                                                    <label for="clinical_notes" class="form-label">Clinical
                                                        Notes</label>
                                                    <textarea type="text" id="clinical_notes" class="form-control" wire:model.lazy="clinical_notes"></textarea>
                                                    @error('clinical_notes')
                                                        <div class="text-danger text-small">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            @endif
                                        </div>
                                        @if ($entry_type == 'Participant' || $entry_type == 'Client')
                                            <div class="row mx-auto">
                                                <h6> <strong class="text-success">Optional Participant
                                                        Information</strong>
                                                </h6>
                                                <hr>
                                                <div class="mb-3 col-md-1">
                                                    <label for="title" class="form-label">Title</label>
                                                    <select class="form-select select2" data-toggle="select2"
                                                        id="title" wire:model="title">
                                                        <option selected value="">Select</option>
                                                        <option value="Mr.">Mr.</option>
                                                        <option value="Ms.">Ms.</option>
                                                        <option value="Miss.">Miss.</option>
                                                        <option value="Dr.">Dr.</option>
                                                        <option value="Eng.">Eng.</option>
                                                        <option value="Prof.">Prof.</option>
                                                    </select>
                                                    @error('title')
                                                        <div class="text-danger text-small">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3 col-md-3">
                                                    <label for="nin_number" class="form-label">NIN Number</label>
                                                    <input type="text" id="nin_number"
                                                        class="form-control text-uppercase"
                                                        onkeyup="this.value = this.value.toUpperCase();"
                                                        size="14" wire:model.lazy="nin_number">
                                                    @error('nin_number')
                                                        <div class="text-danger text-small">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3 col-md-3">
                                                    <label for="surname" class="form-label">Surname</label>
                                                    <input type="text" id="surname"
                                                        class="form-control text-uppercase"
                                                        onkeyup="this.value = this.value.toUpperCase();"
                                                        wire:model.lazy="surname">
                                                    @error('surname')
                                                        <div class="text-danger text-small">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3 col-md-3">
                                                    <label for="first_name" class="form-label">First Name</label>
                                                    <input type="text" id="last_name"
                                                        class="form-control text-uppercase"
                                                        onkeyup="this.value = this.value.toUpperCase();"
                                                        wire:model.lazy="first_name">
                                                    @error('first_name')
                                                        <div class="text-danger text-small">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3 col-md-2">
                                                    <label for="other_name" class="form-label">Other Name</label>
                                                    <input type="text" id="other_name"
                                                        class="form-control text-uppercase"
                                                        onkeyup="this.value = this.value.toUpperCase();"
                                                        wire:model.lazy="other_name">
                                                    @error('other_name')
                                                        <div class="text-danger text-small">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3 col-md-2">
                                                    <label for="dob" class="form-label">DoB</label>
                                                    <input type="date" id="dob" class="form-control"
                                                        wire:model.lazy="dob">
                                                    @error('dob')
                                                        <div class="text-danger text-small">{{ $message }}</div>
                                                    @enderror

                                                </div>

                                                <div class="mb-3 col-md-2">
                                                    <label for="nationality" class="form-label">Nationality</label>
                                                    <select class="form-select select2" data-toggle="select2"
                                                        id="nationality" wire:model="nationality">
                                                        <option selected value="">Select</option>
                                                        @include('layouts.countries')
                                                    </select>
                                                    @error('nationality')
                                                        <div class="text-danger text-small">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3 col-md-2">
                                                    <label for="district" class="form-label">District</label>
                                                    <select class="form-select select2" data-toggle="select2"
                                                        id="district" wire:model="district">
                                                        <option value="" selected>Select</option>
                                                        @include('layouts.districts')
                                                    </select>
                                                    @error('district')
                                                        <div class="text-danger text-small">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3 col-md-2">
                                                    <label for="email" class="form-label">Email</label>
                                                    <input type="email" id="email" class="form-control"
                                                        wire:model.lazy="email">
                                                    @error('email')
                                                        <div class="text-danger text-small">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3 col-md-2">
                                                    <label for="birth_place" class="form-label">Birth Place</label>
                                                    <input type="text" id="birth_place"
                                                        class="form-control text-uppercase"
                                                        onkeyup="this.value = this.value.toUpperCase();"
                                                        wire:model.lazy="birth_place">
                                                    @error('birth_place')
                                                        <div class="text-danger text-small">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3 col-md-2">
                                                    <label for="religious-affiliation" class="form-label">Religious
                                                        Affiliation</label>
                                                    <input type="text" id="religious-affiliation"
                                                        class="form-control text-uppercase"
                                                        onkeyup="this.value = this.value.toUpperCase();"
                                                        wire:model.lazy="religious_affiliation">
                                                    @error('religious_affiliation')
                                                        <div class="text-danger text-small">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3 col-md-3">
                                                    <label for="occupation" class="form-label">Occupation</label>
                                                    <input type="text" id="occupation"
                                                        class="form-control text-uppercase"
                                                        onkeyup="this.value = this.value.toUpperCase();"
                                                        wire:model.lazy="occupation">
                                                    @error('occupation')
                                                        <div class="text-danger text-small">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3 col-md-3">
                                                    <label for="civil_status" class="form-label">Civil Status</label>
                                                    <select class="form-select select2" data-toggle="select2"
                                                        id="civil_status" wire:model="civil_status">
                                                        <option selected value="">Select</option>
                                                        <option value='Single'>Single</option>
                                                        <option value='Married'>Married</option>
                                                        <option value='Unmarried'>Unmarried</option>
                                                        <option value='Divorced'>Divorced</option>
                                                        <option value='Widowed'>Widowed</option>
                                                    </select>
                                                    @error('civil_status')
                                                        <div class="text-danger text-small">{{ $message }}</div>
                                                    @enderror

                                                </div>

                                                <div class="mb-3 col-md-3">
                                                    <label for="nok" class="form-label">Next of Kin</label>
                                                    <input type="text" id="nok"
                                                        class="form-control text-uppercase"
                                                        onkeyup="this.value = this.value.toUpperCase();"
                                                        wire:model.lazy="nok">
                                                    @error('nok')
                                                        <div class="text-danger text-small">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3 col-md-3">
                                                    <label for="nok_relationship" class="form-label">NoK
                                                        Relationship</label>
                                                    <select class="form-select select2" data-toggle="select2"
                                                        id="nok_relationship" wire:model.lazy="nok_relationship">
                                                        <option selected value="">Select</option>
                                                        @include('layouts.nokRelationships')
                                                    </select>
                                                    @error('nok_relationship')
                                                        <div class="text-danger text-small">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endif
                                        <div class="modal-footer">

                                            @if (!$toggleForm)
                                                @if ($participantMatch)
                                                    <a wire:click.prevent="setParticipantId({{ $matched_participant_id }})"
                                                        class="btn btn-success">{{ __('Load') }}</a>
                                                @else
                                                    <x-button class="btn-success">{{ __('Save') }}</x-button>
                                                @endif
                                            @else
                                                <x-button class="btn-success">{{ __('Update') }}</x-button>
                                            @endif

                                        </div>
                                        <!-- end row-->
                                    </form>
                                </div>

                                <div class="tab-pane fade {{ !$activeParticipantTab ? 'show active' : '' }}"
                                    id="sample-tests" role="tabpanel">
                                    <form
                                        @if (!$toggleForm) wire:submit.prevent="storeSampleInformation"
                                        @else
                                        wire:submit.prevent="updateSampleInformation" @endif>
                                        <div class="row mx-auto">
                                            <div class="mb-3 col-md-3">
                                                <label for="requested_by" class="form-label">Requested By</label>
                                                <select class="form-select" id="requested_by"
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
                                            @if ($entry_type=='Participant')
                                            <div class="mb- col-md-1">
                                                <div class="form-check mt-4">
                                                    <input class="form-check-input" type="checkbox" value="1"
                                                        id="is_isolate" checked wire:model="is_isolate">
                                                    <label class="form-check-label text-success" for="is_isolate"><strong>Isolate?</strong></label>
                                                </div>
                                                @error('is_isolate')
                                                    <div class="text-danger text-small">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            @endif
                                           
                                            <div class="mb-3 col-md-3">
                                                <label for="collected_by" class="form-label">Collected By</label>
                                                <select class="form-select" id="collected_by"
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

                                            <div class="mb-3 @if ($entry_type=='Participant') col-md-2 @else col-md-3 @endif">
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
                                                    <select class="form-select" id="study_id" wire:model="study_id">
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
                                                <select class="form-select select2" data-toggle="select2"
                                                    id="sample_is_for" wire:model="sample_is_for">
                                                    <option selected value="">Select</option>
                                                    <option value='Testing'>Testing</option>
                                                    <option value='Aliquoting'>Aliquoting</option>
                                                    <option value='Deffered'>Deffered Testing</option>
                                                    {{-- <option value='Processing and Storage'>Processing & Storage</option>
                                                    <option value='Direct Storage'>Direct Storage</option> --}}
                                                </select>
                                                @error('sample_is_for')
                                                    <div class="text-danger text-small">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3 col-md-3">
                                                <label for="priority" class="form-label">Priority<span
                                                        class="text-danger">*</span></label>
                                                <select class="form-select select2" data-toggle="select2"
                                                    id="priority" wire:model="priority">
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
                                            @if ($entry_type=='Participant')
                                            <div class="mb-3 col-md-2">
                                                <label for="visit" class="form-label">Participant Visit
                                                    @if ($lastVisit)
                                                        (<strong class="text-info"> last:
                                                        </strong>{{ $lastVisit }})
                                                    @endif
                                                </label>
                                                <input id="visit" type="number" class="form-control"
                                                    wire:model.lazy="visit">
                                                @error('visit')
                                                    <div class="text-danger text-small">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            @endif
                                        
                                            <div class="mb-3 col-md-8">
                                                <label for="sampleType" class="form-label">Sample<span
                                                        class="text-danger">*</span></label>
                                                <select class="form-select" id="sampleType"
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
                                        <div wire:loading.delay>
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
                                                        <div class="form-check form-check-inline mb-1 test-list"
                                                            id="test-list">
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
                                            <div class="row mx-auto" wire:loading.class='invisible'>
                                                <div class="text-danger col-md-12">No associated tests! Please select
                                                    sample type</div>
                                            </div>
                                        @endif

                                        <div class="modal-footer">
                                            @if (!$toggleForm)
                                                @if ($entry_type == 'Participant')
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox"
                                                            value="1" id="same_participant" checked
                                                            wire:model="same_participant">
                                                        <label class="form-check-label"
                                                            for="same_participant">Multiple
                                                            sample entry for the same participant?</label>
                                                    </div>
                                                @endif

                                                <x-button class="btn-success">{{ __('Save') }}</x-button>
                                            @else
                                                <x-button class="btn-success">{{ __('Update') }}</x-button>
                                            @endif
                                        </div>
                                        <!-- end row-->
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if (!$samples->isEmpty())
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="table-responsive">
                                <table id="datableButton" class="table table-striped mb-0 w-100 sortable">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Batch No</th>
                                            <th>Entry Type</th>
                                            <th>Part ID</th>
                                            <th>Age</th>
                                            <th>Gender</th>
                                            <th>Contact</th>
                                            <th>Address</th>
                                            <th>Sample</th>
                                            <th>Sample ID</th>
                                            <th>Lab No</th>
                                            <th>Study</th>
                                            <th>Requested By</th>
                                            <th>Collected By</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($samples as $key => $sample)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>
                                                    {{ $batch_no }}
                                                </td>
                                                <td>
                                                    {{ $sample->participant->entry_type }}
                                                </td>
                                                <td>
                                                    @if ($sample->participant)
                                                        @if ($sample->request_acknowledged_by || $sample->participant->entry_type == 'Other')
                                                            {{ $sample->participant->identity }}
                                                        @else
                                                            <a href="javascript: void(0);" class="action-ico"
                                                                wire:click="editParticipant({{ $sample->participant->id }})">{{ $sample->participant->identity }}</a>
                                                        @endif
                                                    @else
                                                        <a href="javascript: void(0);" class="action-ico"
                                                            wire:click="editParticipant({{ $sample->participant->id }})">{{ $sample->participant->identity }}</a>
                                                    @endif
                                                </td>

                                                <td>{{ $sample->participant->age ?? 'N/A' }}</td>
                                                <td>{{ $sample->participant->gender ?? 'N/A' }}</td>
                                                <td>{{ $sample->participant->contact ?? 'N/A' }}</td>
                                                <td>{{ $sample->participant->address ?? 'N/A' }}</td>
                                                <td>
                                                    @if ($sample)
                                                        @if ($sample->request_acknowledged_by)
                                                            {{ $sample->sampleType->type }}
                                                        @else
                                                            <a href="javascript: void(0);" class="action-ico"
                                                                wire:click="editSampleInformation({{ $sample->id }})">{{ $sample->sampleType ? $sample->sampleType->type : 'N/A' }}</a>
                                                        @endif
                                                    @else
                                                        {{ __('N/A') }}
                                                        <a href="javascript: void(0);" class="action-ico"
                                                            wire:click="setParticipantId({{ $sample->participant->id }})">Add</a>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($sample)
                                                        {{ $sample->sample_identity }}
                                                    @else
                                                        {{ __('N/A') }}
                                                    @endif
                                                </td>

                                                <td>
                                                    @if ($sample)
                                                        <strong class="text-success">{{ $sample->lab_no }}</strong>
                                                    @else
                                                        {{ __('N/A') }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($sample && $sample->study)
                                                        {{ $sample->study->name }}
                                                    @else
                                                        {{ __('N/A') }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($sample && $sample->requester)
                                                        {{ $sample->requester->name }}
                                                    @else
                                                        {{ __('N/A') }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($sample && $sample->collector)
                                                        {{ $sample->collector->name ?? 'N/A' }}
                                                    @else
                                                        {{ __('N/A') }}
                                                    @endif
                                                </td>
                                                <td class="table-action">
                                                    @if ($sample)
                                                        {{ __('N/A') }}
                                                    @else
                                                        <a href="javascript: void(0);"
                                                            wire:click="deleteConfirmation({{ $sample->id }})"
                                                            class="action-ico btn btn-outline-danger mx-1">
                                                            <i class="bi bi-trash"></i></a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                        @endforelse
                                    </tbody>
                                </table>
                            </div> <!-- end preview-->
                        </div> <!-- end tab-content-->
                    </div> <!-- end card body-->
                @endif
            </div> <!-- end card -->
        </div><!-- end col-->

        {{-- //DELETE CONFIRMATION MODAL --}}
        <div wire:ignore.self class="modal fade" id="delete_modal" tabindex="-1" data-backdrop="static"
            data-keyboard="false" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog" role="confirm-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Delete Participant</h5>
                        <button type="button" class="btn-close" wire:click="cancel()" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-4 pb-4">
                        <h6>This will delete this <strong class="text-danger">Participant together with associated
                                Sample Data</strong> for this particular batch! Do you want to continue?</h6>
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
                    $('#delete_modal').modal('hide');
                    $('#show-delete-confirmation-modal').modal('hide');
                });

                window.addEventListener('delete-modal', event => {
                    $('#delete_modal').modal('show');
                });
            </script>
        @endpush
    </div>
</div>
