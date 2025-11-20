<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pt-0">
                    <div class="row mb-2">
                        <div class="col-sm-12 mt-3">
                            <div class="d-sm-flex align-items-center">
                                <h5 class="mb-2 mb-sm-0">
                                    Test Result Reports
                                </h5>
                                <div class="ms-auto">
                                    @if (count($combinedSamplesList) >= 1)
                                        <a href="javascript:;" class="btn btn-sm btn-info me-2"
                                            wire:click='combinedTestReport'><i class="bi bi-list"></i>
                                            Combined Test Report
                                        </a>
                                    @endif
                                    @if (count($combinedResultsList) >= 2)
                                        <a class="btn btn-sm btn-info me-2" target="_blank" href="javascript:void()"
                                            wire:click ="printMultiple" {{-- href="{{ route('print-result-multi', ['session_id' => session()->getId()]) }}" --}}>
                                            <i class="bi bi-printer"></i>Selected Multiple Test Report
                                            ({{ count($combinedResultsList) }})
                                        </a>
                                    @elseif(count($resultIds) > 0 && count($resultIds) < 201)
                                        <a class="btn btn-sm btn-info me-2" target="_blank" href="javascript:void()"
                                            wire:click ="printMultiple" {{-- href="{{ route('print-result-multi', ['session_id' => session()->getId()]) }}" --}}>
                                            <i class="bi bi-printer"></i>All Multiple Test Report
                                            ({{ count($resultIds) }})
                                        </a>
                                    @elseif(count($resultIds) > 201)
                                        <small class="text-danger">
                                            The selected number of results ({{ count($resultIds) }}) is greater the 200,
                                            please apply some filters like Date, Sample Type, Test, Facility, Study to
                                            reduce on the number
                                        </small>
                                    @endif

                                    <a type="button" class="btn btn-outline-info" wire:click="refresh()"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                        data-bs-original-title="Refresh Table"><i class="bi bi-arrow-clockwise"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if (count($combinedSamplesList) >= 1)
                        You have selected <strong class="text-success">{{ count($combinedSamplesList) }}</strong>
                        sample(s) for the combined test report (<a href="javascript:;" class="text-info"
                            wire:click="$set('combinedSamplesList',[])">Clear All</a>)
                    @endif
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="mb-3 col-md-2">
                            <label for="facility_id" class="form-label">Facility</label>
                            <select class="form-select select2" data-model="facility_id" id="facility_id"
                                wire:model="facility_id">
                                <option selected value="0">All</option>
                                @forelse ($facilities as $facility)
                                    <option value='{{ $facility->id }}'>{{ $facility->name }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                        <div class="mb-3 col-md-2">
                            <label for="study" class="form-label">Study</label>
                            <select class="form-select select2" id="study_id" data-model="study_id"
                                wire:model="study_id">
                                <option selected value="0">All</option>
                                @forelse ($studies as $study)
                                    <option value='{{ $study->id }}'>{{ $study->name }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                        <div class="mb-3 col-md-2">
                            <label for="sampleType" class="form-label">Sample Type</label>
                            <select class="form-select select2" id="sampleType" data-model="sampleType"
                                wire:model='sampleType'>
                                <option selected value="0">All</option>
                                @foreach ($sampleTypes as $sampleType)
                                    <option value='{{ $sampleType->id }}'>
                                        {{ $sampleType->type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-md-2">
                            <label for="test_id" class="form-label">Test</label>
                            <select class="form-select select2" id="test_id" data-model="test_id"
                                wire:model='test_id'>
                                <option selected value="0">All</option>
                                @foreach ($tests as $test)
                                    <option value='{{ $test->id }}'>
                                        {{ $test->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3 col-md-2">
                            <label for="performed_by" class="form-label">Performed By</label>
                            <select class="form-select" id="performed_by" wire:model='performed_by'>
                                @if (Auth::user()->hasPermission('manager-access|master-access'))
                                    <option selected value="0">All</option>
                                    @foreach ($users as $user)
                                        <option value='{{ $user->id }}'>
                                            {{ $user->fullName }}</option>
                                    @endforeach
                                @else
                                    <option selected value="{{ auth()->user()->id }}">
                                        {{ auth()->user()->fullName }}</option>
                                @endif
                            </select>
                        </div>
                        <div class="mb-3 col-md-2">
                            <label for="reviewed_by" class="form-label">Reviewed By</label>
                            <select class="form-select" id="reviewed_by" wire:model='reviewed_by'>
                                <option selected value="0">All</option>
                                @foreach ($users as $user)
                                    <option value='{{ $user->id }}'>
                                        {{ $user->fullName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-md-2">
                            <label for="approved_by" class="form-label">Approved By</label>
                            <select class="form-select" id="approved_by" wire:model='approved_by'>
                                <option selected value="0">All</option>
                                @foreach ($users as $user)
                                    <option value='{{ $user->id }}'>
                                        {{ $user->fullName }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3 col-md-2">
                            <label for="from_date" class="form-label">Start Date</label>
                            <input id="from_date" type="date" class="form-control" wire:model="from_date">
                        </div>
                        <div class="mb-3 col-md-2">
                            <label for="to_date" class="form-label">End Date</label>
                            <input id="to_date" type="date" class="form-control" wire:model="to_date">
                        </div>
                        <div class="mb-3 col-md-2">
                            <label for="orderBy" class="text-nowrap mr-2 mb-0">Downloaded</label>
                            <select wire:model="downloaded" class="form-select">
                                <option value="">All</option>
                                <option value="1">Never</option>
                                <option value="2">2 Times</option>
                                <option value="3">3 Times</option>
                                <option value="4">More than 3 Times</option>
                            </select>
                        </div>
                    </div>
                    <x-table-utilities>
                        <div>
                            <div class="d-flex align-items-center ml-4 me-2">
                                <label for="orderBy" class="text-nowrap mr-2 mb-0">OrderBy</label>
                                <select wire:model="orderBy" class="form-select">
                                    <option value="id">Latest</option>
                                    <option value="approved_at">Date Approved</option>
                                    <option value="reviewed_at">Date Reviewed</option>
                                    <option value="created_at">Result Date</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-flex align-items-center ml-4 me-2">
                            <label for="orderBy" class="text-nowrap mr-2 mb-0">Status</label>
                            <select wire:model="status" class="form-select">
                                <option value="Approved">Approved</option>
                                <option value="Reviewed">Reviewed</option>
                                <option value="Rejected">Rejected</option>
                            </select>
                        </div>

                    </x-table-utilities>

                    <div class="tab-content">
                        <div class="table-responsive">
                            <table id="datableButton" class="table table-striped mb-0 w-100 sortable">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Batch</th>
                                        <th>Tracker</th>
                                        <th>Study</th>
                                        <th>PID</th>
                                        <th>Sample</th>
                                        <th>Sample ID</th>
                                        <th>Lab No</th>
                                        <th>Test</th>
                                        <th>TAT(HR<->MIN)</th>
                                        <th>Requester</th>
                                        <th>Requested At</th>
                                        <th>Received At</th>
                                        <th>Result Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($testResults as $key => $testResult)
                                        <tr
                                            class="
                                        @if (
                                            $testResult->test->tat != 0 &&
                                                $testResult->sample->created_at->diffInHours($testResult->created_at) > $testResult->test->tat) bg-light-danger @endif
                                        ">
                                            <td>{{ $key + 1 }}</td>

                                            <td>
                                                <a href="{{ URL::signedRoute('batch-search-results', ['sampleReception' => $testResult->sample->sampleReception->id]) }}"
                                                    class="text-secondary"
                                                    target="_blank">{{ $testResult->sample->sampleReception->batch_no }}
                                                </a>
                                            </td>
                                            <td>
                                                @if ($testResult->amended_state)
                                                    <a href="javascript:void(0)"
                                                        wire:click='viewAmended({{ $testResult->id }})'
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#amendedResults"><strong class="text-warning"
                                                            title="SHOW AMENDED">{{ $testResult->tracker }}</strong>
                                                    </a>
                                                @else
                                                    <a href="{{ URL::signedRoute('report-search-results', ['testResult' => $testResult->id]) }}"
                                                        target="_blank"><strong
                                                            class="text-info">{{ $testResult->tracker }}</strong>
                                                    </a>
                                                @endif

                                            </td>
                                            <td>
                                                {{ $testResult->sample->study->name ?? 'N/A' }}
                                            </td>
                                            <td>
                                                <a href="{{ URL::signedRoute('participant-search-results', ['participant' => $testResult->sample->participant->id]) }}"
                                                    class="text-secondary"
                                                    target="_blank">{{ $testResult->sample->participant->identity }}
                                                </a>
                                            </td>

                                            <td>
                                                {{ $testResult->sample->sampleType->type }}
                                                <input type="checkbox" value="{{ $testResult->sample->id }}"
                                                    class="me-2 float-end" wire:model="combinedSamplesList">
                                            </td>
                                            <td>
                                                {{ $testResult->sample->sample_identity }}
                                            </td>
                                            <td class="text-success fw-bold">
                                                {{ $testResult->sample->lab_no ?? 'N/A' }}
                                            </td>

                                            <td>
                                                {{ $testResult->test->name }}
                                                <input type="checkbox" value="{{ $testResult->id }}"
                                                    class="me-2 float-end" wire:model="combinedResultsList">
                                            </td>
                                            <td>
                                                <span
                                                    class="text-danger fw-bold">{{ $testResult->sample->created_at->diffInHours($testResult->created_at) }}</span>
                                                ({{ $testResult->sample->created_at->diffInMinutes($testResult->created_at) . 'min' }})
                                            </td>

                                            <td>
                                                {{ $testResult?->sample?->requester?->name??'N/A' }}
                                            </td>
                                            <td>
                                                {{ date('d-m-Y', strtotime($testResult->sample->date_requested)) }}
                                            </td>
                                            <td>
                                                {{ date('d-m-Y H:i', strtotime($testResult->sample->sampleReception->date_delivered)) }}
                                            </td>
                                            <td>
                                                {{ $testResult->created_at }}
                                            </td>
                                            <td>
                                                <span class="badge bg-success">{{ $testResult->status }}</span>
                                            </td>
                                            <td class="action-ico">
                                                @if ($status == 'Approved')
                                                    <a target="_blank"
                                                        href="{{ route('result-report', $testResult->id) }}"
                                                        type="button"
                                                        class=" d-none action-ico btn btn-outline-info me-2"
                                                        wire:click='incrementDownloadCount({{ $testResult->id }})'><i
                                                            class="bi bi-arrow-down-square"></i></a>

                                                    <a target="_blank"
                                                        href="{{ route('print-result-report', $testResult->id) }}"
                                                        type="button"
                                                        class="action-ico btn btn-outline-success btn-sm"
                                                        wire:click='incrementDownloadCount({{ $testResult->id }})'><i
                                                            class="bi bi-printer"></i>
                                                        <small
                                                            class="badge bg-info">{{ $testResult->download_count }}</small>
                                                    </a>
                                                @else
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
                                    {{ $testResults->links() }}
                                </div>
                            </div>
                        </div>
                    </div> <!-- end tab-content-->
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->

        {{-- VIEW amendement details modal --}}
        @include('livewire.lab.lists.show-amended-results')
        <!-- end modal dialog-->
    </div>

    @push('scripts')
        <script>
            window.addEventListener('loadCombinedSampleTestReport', event => {
                window.open(`${event.detail.url}`, '_blank').focus();
            });
        </script>
        @include('livewire.layout.select-2')
    @endpush
</div>
