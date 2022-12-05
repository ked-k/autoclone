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
                                        data-bs-original-title="Refresh Table"><i
                                            class="bi bi-arrow-clockwise"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-0">
                        <form>
                            <div class="row">
                                <div class="mb-3 col-md-3">
                                    <label for="facility_id" class="form-label">Facility</label>
                                    <select class="form-select" id="facility_id" wire:model="facility_id">
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
                                    <select class="form-select" id="study" wire:model="study_id">
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
                                            <option value='{{ $entryType->entry_type }}'>{{ $entryType->entry_type }}
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
                                        <option value="identity">Participant ID</option>
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
                            </div>
                            <!-- end row-->
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        <div class="table-responsive">
                            <table id="datableButton" class="table table-striped mb-0 w-100 sortable">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Participant ID</th>
                                        <th>Entry Type</th>
                                        <th>Age</th>
                                        <th>Gender</th>
                                        <th>Contact</th>
                                        <th>Address</th>
                                        <th>Kin Contact</th>
                                        <th>Kin Address</th>
                                        <th>Facility</th>
                                        <th>Study</th>
                                        <th>Sample Count</th>
                                        <th>Test Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($participants as $key => $participant)
                                        <tr class="{{$activeRow==$participant->id?'bg-info':''}}" wire:click="$set('activeRow',{{$participant->id}})">
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
                                                {{ $participant->age??'N/A' }}
                                            </td>
                                            <td>
                                                {{ $participant->gender??'N/A' }}
                                            </td>
                                           
                                            <td>
                                                {{ $participant->contact??'N/A' }}
                                            </td>
                                            
                                            <td>
                                                {{ $participant->address??'N/A' }}
                                            </td>
                                            <td>
                                         
                                                {{ $participant->nok_contact??'N/A' }}
                                            </td>
                                             
                                           
                                            <td>
                                                {{ $participant->nok_address??'N/A' }}
                                                
                                            </td>
                                        
                                            <td>
                                                {{ $participant->facility->name }}
                                            </td>
                                            <td>
                                                {{ $participant->study->name??'N/A' }}
                                            </td>
                                            {{-- <td>
                                                {{ $participant->sample->name }}
                                            </td> --}}
                                            <td>
                                                {{ $participant->sample_count }}
                                            </td>
                                            <td>
                                                {{ $participant->test_result_count}}
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
</div>

