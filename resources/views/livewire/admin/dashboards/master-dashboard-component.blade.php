<div>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 row-cols-xxl-4">
        <div class="col">
            <div class="card radius-10 border-start border-purple border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">Participants</p>
                            <h4 class="my-1">{{ $participantCount }}</h4>
                            {{-- <p class="mb-0 font-13 text-success"><i class="bi bi-caret-up-fill"></i> <br>Registered</p> --}}
                        </div>
                        <div class="widget-icon-large bg-gradient-purple text-white ms-auto"><i class="bi bi-people"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 border-start border-info border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">Samples Handled</p>
                            <h4 class="my-1">{{ $samplesCount }}</h4>
                            {{-- <p class="mb-0 font-13 text-success"><i class="bi bi-caret-up-fill"></i> 4.6 from last week</p> --}}
                        </div>
                        <div class="widget-icon-large bg-gradient-info text-white ms-auto"><i class="bx bxs-vial"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 border-start border-success border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">Tests Performed</p>
                            <h4 class="my-1">{{ $testsPerformedCount }}</h4>
                            {{-- <p class="mb-0 font-13 text-danger"><i class="bi bi-caret-down-fill"></i> 2.7 from last month</p> --}}
                        </div>
                        <div class="widget-icon-large bg-gradient-success text-white ms-auto"><i
                                class="bx bxs-flask"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 border-start border-warning border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">Active Laboratories</p>
                            <h4 class="my-1">{{ $laboratoryCount }}</h4>
                        </div>
                        <div class="widget-icon-large bg-gradient-warning text-white ms-auto"><i
                                class="bx bx-clinic"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->

    <div class="card radius-10">
        <div class="card-header bg-transparent">
            <div class="row g-3 align-items-center">
                <div class="col">
                    <h5 class="mb-0">Summaries</h5>
                </div>
                <div class="col">
                    <select class="form-select select2" wire:model="laboratory_id">
                        <option selected value="0">All</option>
                        @forelse ($laboratories as $laboratory)
                            <option value='{{ $laboratory->id }}'>{{ $laboratory->laboratory_name }}</option>
                        @empty
                        @endforelse
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-lg-4 col-xl-4 d-flex">
                    <div class="card mb-0 radius-10 border shadow-none w-100">
                        <div class="card-body">
                            <h5 class="card-title">General</h5>
                            <ul class="list-group list-group-flush mt-3">
                                <li class="list-group-item border-top">
                                    <div class="d-flex align-items-center gap-2">
                                        <div>Facilities</div>
                                        <div class="ms-auto"><strong
                                                class="text-success">{{ $facilityActiveCount }}</strong>|<strong
                                                class="text-danger">{{ $facilitySuspendedCount }}</strong></div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="d-flex align-items-center gap-2">
                                        <div>Studies</div>
                                        <div class="ms-auto"><strong
                                                class="text-success">{{ $studyActiveCount }}</strong>|<strong
                                                class="text-danger">{{ $studySuspendedCount }}</strong></div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="d-flex align-items-center gap-2">
                                        <div>Requesters</div>
                                        <div class="ms-auto"><strong
                                                class="text-success">{{ $requesterActiveCount }}</strong>|<strong
                                                class="text-danger">{{ $requesterSuspendedCount }}</strong></div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="d-flex align-items-center gap-2">
                                        <div>Phlebotomists</div>
                                        <div class="ms-auto"><strong
                                                class="text-success">{{ $collectorActiveCount }}</strong>|<strong
                                                class="text-danger">{{ $collectorSuspendedCount }}</strong></div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="d-flex align-items-center gap-2">
                                        <div>Couriers</div>
                                        <div class="ms-auto"><strong
                                                class="text-success">{{ $courierActiveCount }}</strong>|<strong
                                                class="text-danger">{{ $courierSuspendedCount }}</strong></div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 col-xl-4 d-flex">
                    <div class="card mb-0 radius-10 border shadow-none w-100">
                        <div class="card-body">
                            <h5 class="card-title">Samples</h5>
                            <ul class="list-group list-group-flush mt-3">
                                <li class="list-group-item border-top">
                                    <div class="d-flex align-items-center gap-2">
                                        <div>Today</div>
                                        <div class="ms-auto"><strong
                                                class="text-success">{{ $samplesTodayCount }}</strong></div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="d-flex align-items-center gap-2">
                                        <div>This Week</div>
                                        <div class="ms-auto"><strong
                                                class="text-success">{{ $samplesThisWeekCount }}</strong></div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="d-flex align-items-center gap-2">
                                        <div>This Month</div>
                                        <div class="ms-auto"><strong
                                                class="text-success">{{ $samplesThisMonthCount }}</strong></div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="d-flex align-items-center gap-2">
                                        <div>This Year</div>
                                        <div class="ms-auto"><strong
                                                class="text-success">{{ $samplesThisYearCount }}</strong></div>
                                    </div>
                                </li>
                                <li class="list-group-item list-group-item-secondary">Total Sample Batches :
                                    {{ $batchesCount }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 col-xl-4 d-flex">
                    <div class="card mb-0 radius-10 border shadow-none w-100">
                        <div class="card-body">
                            <h5 class="card-title">Tests</h5>
                            <ul class="list-group list-group-flush mt-3">
                                <li class="list-group-item border-top">
                                    <div class="d-flex align-items-center gap-2">
                                        <div>Today</div>
                                        <div class="ms-auto"><strong
                                                class="text-success">{{ $testsTodayCount }}</strong></div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="d-flex align-items-center gap-2">
                                        <div>This Week</div>
                                        <div class="ms-auto"><strong
                                                class="text-success">{{ $testsThisWeekCount }}</strong></div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="d-flex align-items-center gap-2">
                                        <div>This Month</div>
                                        <div class="ms-auto"><strong
                                                class="text-success">{{ $testsThisMonthCount }}</strong></div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="d-flex align-items-center gap-2">
                                        <div>This Year</div>
                                        <div class="ms-auto"><strong
                                                class="text-success">{{ $testsThisYearCount }}</strong></div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!--end row-->
            </div>
            <livewire:admin.dashboards.master-dashboard-charts-component />
        </div>
    </div>
</div>
