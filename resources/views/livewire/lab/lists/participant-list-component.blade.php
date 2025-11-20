<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pt-0">
                    <div class="row mb-2">
                        <div class="col-sm-12 mt-3">
                            <div class="d-sm-flex align-items-center">
                                <h5 class="mb-2 mb-sm-0">
                                    Participants
                                </h5>
                                <div class="ms-auto">
                                    <a href="javascript:;" wire:click='export' class="btn btn-secondary me-2"><i
                                            class="bi bi-file-earmark-fill"></i> Export</a>
                                    <a type="button" class="btn btn-outline-info" wire:click="refresh()"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                        data-bs-original-title="Refresh Table"><i class="bi bi-arrow-clockwise"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    @if ($filter)
                        <div class="row mb-0">
                            <form>
                                <div class="row">
                                    <div class="mb-3 col-md-3">
                                        <label for="facility_id" class="form-label">Facility</label>
                                        <select class="form-select select2" id="facility_id" data-model="facility_id"
                                            wire:model="facility_id">
                                            <option selected value="0">All</option>
                                            @forelse ($facilities as $facility)
                                                <option value='{{ $facility->id }}'>{{ $facility->name }}</option>
                                            @empty
                                            @endforelse
                                        </select>
                                        @error('facility_id')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="study" class="form-label">Study</label>
                                        <select class="form-select select2" id="study_id" data-model="study_id"
                                            wire:model="study_id">
                                            <option selected value="0">All</option>
                                            @forelse ($studies as $study)
                                                <option value='{{ $study->id }}'>{{ $study->name }}</option>
                                            @empty
                                            @endforelse
                                        </select>
                                        @error('study_id')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col-md-2">
                                        <label for="entryType" class="form-label">Entry Type</label>
                                        <select class="form-select" id="entryType" wire:model="entryType">
                                            <option selected value="">All</option>
                                            @forelse ($entryTypes as $entryType)
                                                <option value='{{ $entryType->entry_type }}'>
                                                    {{ $entryType->entry_type }}
                                                </option>
                                            @empty
                                            @endforelse
                                        </select>
                                        @error('entryType')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col-md-2">
                                        <label for="from_date" class="form-label">Start Date</label>
                                        <input id="from_date" type="date" class="form-control"
                                            wire:model="from_date">
                                        @error('from_date')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col-md-2">
                                        <label for="to_date" class="form-label">End Date</label>
                                        <input id="to_date" type="date" class="form-control" wire:model="to_date">
                                        @error('to_date')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col-md-2">
                                        <label for="perPage" class="form-label">Per Page</label>
                                        <select wire:model="perPage" class="form-select" id="perPage">
                                            <option value="10">10</option>
                                            <option value="20">20</option>
                                            <option value="30">30</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                            <option value="200">200</option>
                                            <option value="500">500</option>
                                            <option value="1000">1000</option>
                                        </select>
                                    </div>

                                    <div class="mb-3 col-md-2">
                                        <label for="orderBy" class="form-label">OrderBy</label>
                                        <select wire:model="orderBy" class="form-select">
                                            <option value="identity">PID</option>
                                            <option value="age">Age</option>
                                            <option value="address">Address</option>
                                            <option value="contact">Contact</option>
                                            <option value="id">Latest</option>
                                        </select>
                                    </div>

                                    <div class="mb-3 col-md-2">
                                        <label for="orderAsc" class="form-label">Order</label>
                                        <select wire:model="orderAsc" class="form-select" id="orderAsc">
                                            <option value="">Asc</option>
                                            <option value="0">Desc</option>
                                        </select>
                                    </div>
                                    <div class=" col ms-auto position-relative">
                                        <label for="search" class="form-label">Search</label>
                                        <input wire:model.debounce.300ms="search" class="form-control "
                                            type="text" placeholder="search">
                                    </div>
                                </div>
                                <!-- end row-->
                            </form>
                        </div>
                    @endif
                    @if ($toggleForm)
                        <form wire:submit.prevent="updateParticipant">
                            <div class="row mx-auto">
                                <div class="mb-3 col-md-2">
                                    <label for="entry_type" class="form-label">Entry Type</label>
                                    <select class="form-select select2" readonly id="entry_type"
                                        wire:model="entry_type">
                                        <option selected value="Participant">Participant/Isolate</option>
                                        <option selected value="CRS Patient">CRS Patient</option>
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

                                        </label>
                                        <input type="text" id="identity" class="form-control" size="14"
                                            wire:model.lazy="identity"
                                            @if ($entry_type == 'Client') disabled @endif>
                                        @error('identity')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endif
                                @if ($entry_type == 'CRS Patient')
                                    <div class="col">
                                        <label for="patno" class="form-label">CRS Pat No.<span
                                                class="text-danger">*</span>
                                        </label>
                                        <input type="text" id="pat_no" class="form-control" size="14"
                                            wire:model.lazy="patno">
                                        @error('patno')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-2 col-md-2">
                                        <label for="identity" class="form-label">Participant ID<span
                                                class="text-danger">*</span>
                                        </label>
                                        <input type="text" id="identity" class="form-control" size="14"
                                            wire:model.lazy="identity">
                                        @error('identity')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endif
                                @if ($entry_type == 'Participant' || $entry_type == 'Client' || $entry_type == 'CRS Patient')
                                    <div class="mb-3 col-md-1">
                                        <label for="age" class="form-label">Age<span
                                                class="text-danger">*</span></label>
                                        <input type="text" id="age" class="form-control"
                                            wire:model.lazy="age">
                                        @error('age')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col-md-1">
                                        <label for="age" class="form-label">Months</label>
                                        <input type="text" id="months" class="form-control"
                                            wire:model.lazy="months">
                                        @error('months')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col-md-2">
                                        <label for="gender" class="form-label">Gender<span
                                                class="text-danger">*</span></label>
                                        <select class="form-select select2" id="gender" wire:model="gender">
                                            <option selected value="">Select</option>
                                            <option value='Male'>Male</option>
                                            <option value='Female'>Female</option>
                                            <option value='N/A'>N/A</option>
                                        </select>
                                        @error('gender')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col">
                                        <label for="address" class="form-label">Address<span
                                                class="text-danger">*</span></label>
                                        <input type="text" id="address" class="form-control text-uppercase"
                                            onkeyup="this.value = this.value.toUpperCase();"
                                            wire:model.lazy="address">
                                        @error('address')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-2 col-md-2">
                                        <label for="contact" class="form-label">Contact<span
                                                class="text-danger">*</span></label>
                                        <input type="text" id="contact" class="form-control text-uppercase"
                                            onkeyup="this.value = this.value.toUpperCase();"
                                            wire:model.lazy="contact">
                                        @error('contact')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="nok_contact" class="form-label">Next of Kin
                                            Contact<span class="text-danger">*</span></label>
                                        <input type="text" id="nok_contact" class="form-control text-uppercase"
                                            onkeyup="this.value = this.value.toUpperCase();"
                                            wire:model.lazy="nok_contact">
                                        @error('nok_contact')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="nok_address" class="form-label">Next of Kin
                                            Address</label>
                                        <input type="text" id="nok_address" class="form-control text-uppercase"
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
                            @if ($entry_type == 'Participant' || $entry_type == 'Client' || $entry_type == 'CRS Patient')
                                <div class="row mx-auto">
                                    <h6> <strong class="text-success">Optional Participant
                                            Information</strong>
                                    </h6>
                                    <hr>
                                    <div class="mb-3 col-md-1">
                                        <label for="title" class="form-label">Title</label>
                                        <select class="form-select select2" id="title" wire:model="title">
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
                                        <input type="text" id="nin_number" class="form-control text-uppercase"
                                            onkeyup="this.value = this.value.toUpperCase();" size="14"
                                            wire:model.lazy="nin_number">
                                        @error('nin_number')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3 col-md-3">
                                        <label for="surname" class="form-label">Surname</label>
                                        <input type="text" id="surname" class="form-control text-uppercase"
                                            onkeyup="this.value = this.value.toUpperCase();"
                                            wire:model.lazy="surname">
                                        @error('surname')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="first_name" class="form-label">First Name</label>
                                        <input type="text" id="last_name" class="form-control text-uppercase"
                                            onkeyup="this.value = this.value.toUpperCase();"
                                            wire:model.lazy="first_name">
                                        @error('first_name')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col-md-2">
                                        <label for="other_name" class="form-label">Other Name</label>
                                        <input type="text" id="other_name" class="form-control text-uppercase"
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
                                        <select class="form-select select2" id="nationality"
                                            wire:model="nationality">
                                            <option selected value="">Select</option>
                                            @include('layouts.countries')
                                        </select>
                                        @error('nationality')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col-md-2">
                                        <label for="district" class="form-label">District</label>
                                        <select class="form-select select2" id="district" wire:model="district">
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
                                        <input type="text" id="birth_place" class="form-control text-uppercase"
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
                                        <input type="text" id="occupation" class="form-control text-uppercase"
                                            onkeyup="this.value = this.value.toUpperCase();"
                                            wire:model.lazy="occupation">
                                        @error('occupation')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3 col-md-3">
                                        <label for="civil_status" class="form-label">Civil Status</label>
                                        <select class="form-select select2" id="civil_status"
                                            wire:model="civil_status">
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
                                        <input type="text" id="nok" class="form-control text-uppercase"
                                            onkeyup="this.value = this.value.toUpperCase();" wire:model.lazy="nok">
                                        @error('nok')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3 col-md-3">
                                        <label for="nok_relationship" class="form-label">NoK
                                            Relationship</label>
                                        <select class="form-select select2" id="nok_relationship"
                                            wire:model.lazy="nok_relationship">
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

                                <button wire:click="close()" type="reset"
                                    class="btn btn-outline-danger r-15">Close</button>
                                <x-button class="btn-success">{{ __('Update') }}</x-button>

                            </div>
                            <!-- end row-->
                        </form>
                    @endif
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="table-responsive">
                            <table id="datableButton" class="table table-striped mb-0 w-100 sortable">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>PID</th>
                                        <th>Entry Type</th>
                                        <th>Age</th>
                                        <th>Gender</th>
                                        <th>Contact</th>
                                        <th>Address</th>
                                        <th>Kin Contact</th>
                                        <th>Kin Address</th>
                                        <th>Facility</th>
                                        <th>Study</th>
                                        {{-- <th>Sample Count</th>
                                        <th>Test Count</th> --}}
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($participants as $key => $participant)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>

                                            <td>
                                                <a href="{{ URL::signedRoute('participant-search-results', ['participant' => $participant->id]) }}"
                                                    class="text-secondary"
                                                    target="_blank">{{ $participant->identity }}
                                                </a>

                                            </td>
                                            <td>
                                                {{ $participant->entry_type }}
                                            </td>
                                            <td>
                                                @if ($participant->age != null)
                                                    {{ $participant->age }} yrs
                                                @elseif ($participant->months != null)
                                                    &nbsp; {{ $participant->months }} Months
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                {{ $participant->gender ?? 'N/A' }}
                                            </td>

                                            <td>
                                                {{ $participant->contact ?? 'N/A' }}
                                            </td>

                                            <td>
                                                {{ $participant->address ?? 'N/A' }}
                                            </td>
                                            <td>

                                                {{ $participant->nok_contact ?? 'N/A' }}
                                            </td>


                                            <td>
                                                {{ $participant->nok_address ?? 'N/A' }}

                                            </td>

                                            <td>
                                                {{ $participant->facility->name }}
                                            </td>
                                            <td>
                                                {{ $participant->study->name ?? 'N/A' }}
                                            </td>
                                            {{-- <td>
                                                {{ $participant->sample->name }}
                                            </td> --}}
                                            {{-- <td>
                                                {{ $participant->sample_count }}
                                            </td>
                                            <td>
                                                {{ $participant->test_result_count}}
                                            </td> --}}
                                            <td>
                                                @if ($participant->created_by == auth()->user()->id || Auth::user()->hasPermission(['review-results']))
                                                    <button class="action-btn action-btn--info"
                                                        wire:click="editParticipant({{ $participant->id }})"><i
                                                            class="bi bi-pencil"></i></button>
                                                    <a class="action-btn action-btn--primary" target="_blank"
                                                        href="{{ URL::signedRoute('participant-search-results', ['participant' => $participant->id]) }}"><i
                                                            class="bi bi-eye"></i></a>
                                                @endif
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
                                    {{ $participants->links('vendor.livewire.bootstrap') }}
                                </div>
                            </div>
                        </div>
                    </div> <!-- end tab-content-->
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->

    </div>
    @push('scripts')
        @include('livewire.layout.select-2')
    @endpush
</div>
