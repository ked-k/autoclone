<div>
    <!--start sidebar -->
    <aside class="sidebar-wrapper">
        <div class="iconmenu">
            <div class="nav-toggle-box">
                <div class="nav-toggle-icon"><i class="bi bi-list"></i></div>
            </div>
            <ul class="nav nav-pills flex-column">
                <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard">
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pills-dashboards" type="button"><i
                            class="bi bi-house-door-fill"></i></button>
                </li>

                <li class="nav-item {{ request()->segment(1) == 'samplemgt' || $navItem == 'samplemgt' ? 'active show' : '' }}"
                    data-bs-toggle="tooltip" data-bs-placement="right" title="Sample Management">
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pills-samples" type="button"><i
                            class="bi bi-prescription"></i><i class='bx bxs-vial'></i></button>
                </li>
                <li class="nav-item {{ request()->segment(1) == 'samplestg' || $navItem == 'samplestg' ? 'active show' : '' }}"
                    data-bs-toggle="tooltip" data-bs-placement="right" title="Sample Storage">
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#sample-storage" type="button"><i
                            class="bx bx-archive"></i></button>
                </li>
                <li class="nav-item {{ request()->segment(1) == 'report' || $navItem == 'report' ? 'active show' : '' }}"
                    data-bs-toggle="tooltip" data-bs-placement="right" title="Sample Reports">
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#sample-reports" type="button"><i
                            class="bx bx-file"></i></button>
                </li>

                @if (Auth::user()->hasPermission(['manage-users']))
                    <li class="nav-item {{ request()->segment(2) == 'usermgt' ? 'active show' : '' }}"
                        data-bs-toggle="tooltip" data-bs-placement="right" title="User Management">
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pills-user-management"
                            type="button">
                            <i class='bi bi-person-workspace'></i></button>
                    </li>
                @endif
                @if (Auth::user()->hasPermission(['access-settings']))
                    <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="General Settings">
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pills-management"
                            type="button"><i class="bi bi-gear-fill"></i></button>
                    </li>
                @endif

                <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="My Account">
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pills-user-profile"
                        type="button"><i class="bi bi-person-fill"></i></button>
                </li>
            </ul>
        </div>

        <div class="textmenu">
            <div class="brand-logo">
                <a
                    href="
                @if (Auth::user()->hasPermission(['manager-access'])) {{ route('manager-dashboard') }}
                @elseif (Auth::user()->hasPermission(['master-access']))
                {{ route('master-dashboard') }}
                @elseif (Auth::user()->hasPermission(['normal-access']))
                {{ route('user-dashboard') }} @endif
                "><img
                        src="{{ asset('autolab-assets/images/brand-logo-2.png') }}" width="140" alt="" /></a>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade" id="pills-dashboards">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-0">Home</h5>
                            </div>
                        </div>
                        @if (Auth::user()->hasPermission(['manager-access']))
                            <a href="{{ route('manager-dashboard') }}" class="list-group-item"><i
                                    class="bi bi-house-door-fill"></i>Manager Dashboard</a>
                        @endif
                        @if (Auth::user()->hasPermission(['master-access']))
                            <a href="{{ route('master-dashboard') }}" class="list-group-item"><i
                                    class="bi bi-house-door-fill"></i>Master Dashboard</a>
                        @endif
                        @if (Auth::user()->hasPermission(['normal-access']))
                            <a href="{{ route('user-dashboard') }}" class="list-group-item"><i
                                    class="bi bi-house-door-fill"></i>User Dashboard</a>
                        @endif
                    </div>
                </div>

                <div class="tab-pane fade {{ request()->segment(1) == 'samplemgt' || $navItem == 'samplemgt' ? 'active show' : '' }}"
                    id="pills-samples">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-0">Sample Mgt</h5>
                            </div>
                        </div>
                        @if (Auth::user()->hasPermission(['accession-samples']))
                            <a href="{{ route('samplereception') }}" class="list-group-item"><i
                                    class="bi bi-box2"></i>Reception<x-count-badge>{{ $batchesCount }}</x-count-badge>
                            </a>
                            <a href="{{ route('nimsamplereception') }}" class="list-group-item"><i
                            class="bi bi-box2"></i>Nims Reception</a>
                            <a href="javascript: void(0);"
                                class="list-group-item {{ Request::routeIs('specimen-request') ? 'active' : '' }}"><i
                                    class="bi bi-receipt"></i>Accessioning</a>
                        @endif

                        @if (Auth::user()->hasPermission(['assign-test-requests']))
                            <a href="{{ route('test-request-assignment') }}" class="list-group-item"><i
                                    class="bi bi-file-medical"></i>Assign
                                Tasks<x-count-badge>{{ $testRequestsCount }}</x-count-badge></a>
                        @endif

                        @if (Auth::user()->hasPermission(['enter-results']))
                            <a href="{{ route('test-request') }}" class="list-group-item"><i
                                    class="bi bi-list-task"></i>My
                                Tasks<x-count-badge>{{ $testAssignedCount + $AliquotingAssignedCount }}</x-count-badge>
                            </a>

                            <a href="javascript: void(0);"
                                class="list-group-item {{ Request::routeIs('attach-test-results') ? 'active' : '' }}"><i
                                    class="bi bi-file-earmark-medical"></i>Entering Results</a>
                            <a href="javascript: void(0);"
                                class="list-group-item {{ Request::routeIs('attach-aliquots') ? 'active' : '' }}"><i
                                    class="bi bi-hourglass-split"></i>Aliquoting</a>
                        @endif

                        @if (Auth::user()->hasPermission(['enter-results']))
                            <a href="{{ route('rejected-results') }}" class="list-group-item"><i
                                    class="bi bi-exclamation-triangle-fill text-danger"></i>Rejected Results
                                <span class="badge bg-danger pill float-end">{{ $rejectedResultsCount }}</span>
                            </a>
                        @endif

                        @if (Auth::user()->hasPermission(['review-results']))
                            <a href="{{ route('test-review') }}"
                                class="list-group-item
                            {{ request()->segment(2) == 'resultReview' || $link == 'review' ? 'active' : '' }}
                            "><i
                                    class="bi bi-check-square"></i>Result
                                Review<x-count-badge>{{ $testsPendindReviewCount }}</x-count-badge></a>
                        @endif

                        @if (Auth::user()->hasPermission(['approve-results']))
                            <a href="{{ route('test-approval') }}"
                                class="list-group-item
                            {{ request()->segment(2) == 'resultApproval' || $link == 'approve' ? 'active' : '' }}
                            "><i
                                    class="bi bi-check2-square"></i>Result
                                Approval<x-count-badge>{{ $testsPendindApprovalCount }}</x-count-badge></a>
                        @endif

                        @if (Auth::user()->hasPermission(['review-results']))
                            <a href="{{ route('tests-rejected') }}" class="list-group-item"><i
                                    class="bi bi-exclamation-triangle-fill text-warning"></i>All Rejected Results
                                <span class="badge bg-warning pill float-end">{{ $testsRejectedCount }}</span>
                            </a>
                        @endif

                        @if (Auth::user()->hasPermission(['review-results']))
                            <a href="{{ route('amended-results', 'all') }}" class="list-group-item"><i
                                    class="bi bi-pencil"></i>Amended Results</a>
                        @elseif (Auth::user()->hasPermission(['enter-results']))
                            <a href="{{ route('amended-results', 'my') }}" class="list-group-item"><i
                                    class="bi bi-pencil"></i>Amended Results</a>
                        @endif

                        @if (Auth::user()->hasPermission(['view-result-reports']))
                            <a href="{{ route('test-reports') }}" class="list-group-item"><i
                                    class="bi bi-file-earmark-text"></i>Result
                                Reports<x-count-badge>{{ $testReportsCount }}</x-count-badge></a>
                        @endif

                        @if (Auth::user()->hasPermission(['view-participant-info']))
                            {{-- <a href="{{ route('samples-list') }}" class="list-group-item"><i
                                    class="bx bxs-vial"></i>Samples<x-count-badge>{{ $samplesCount }}
                                </x-count-badge></a> --}}
                            <div class="list-group-item">
                                <a class="d-flex justify-content-between text-light" data-bs-toggle="collapse"
                                    href="#sampleReportsDropdown" role="button" aria-expanded="false"
                                    aria-controls="sampleReportsDropdown">
                                    <span><i class="bx bxs-vial"></i> Samples</span>
                                    <i class="bi bi-chevron-down small"></i>
                                </a>
                                <div class="collapse mt-2" id="sampleReportsDropdown">
                                    <ul class="nav flex-column ms-3 text-light">
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('samples-list') }}"></i>All
                                                Samples<x-count-badge>{{ $samplesCount }}
                                                </x-count-badge></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link"
                                                href="{{ route('samples-pending-list') }}"></i>Pending
                                                Samples<x-count-badge>
                                                </x-count-badge></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link"
                                                href="{{ route('multiple-result-list') }}"></i>Multiple
                                                Results</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('tests-count-report') }}"></i>Referred
                                                Samples
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link"
                                                href="{{ route('tests-study-count-report') }}"></i>Rejected Samples
                                            </a>
                                        </li>

                                    </ul>
                                </div>
                            </div>

                            {{-- <div class="list-group-item">
                                <a class="d-flex justify-content-between text-light" data-bs-toggle="collapse"
                                    href="#tatReportsDropdown" role="button" aria-expanded="false"
                                    aria-controls="tatReportsDropdown">
                                    <span><i class="bx bxs-vial"></i> Reports</span>
                                    <i class="bi bi-chevron-down small"></i>
                                </a>
                                <div class="collapse mt-2" id="tatReportsDropdown">
                                    <ul class="nav flex-column ms-3 text-light">
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('tests-tat-report') }}"></i>TAT
                                                Report</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('result-tat-report') }}"></i>Test TAT
                                                Request
                                            </a>
                                        </li>

                                    </ul>
                                </div>
                            </div> --}}
                            {{-- <a href="{{ route('samples-count') }}" class="list-group-item"><i
                                    class="bx bxs-vial"></i>Sample Reports<x-count-badge>{{ $samplesCount }}
                                </x-count-badge></a> --}}

                            <a href="{{ route('participants') }}" class="list-group-item"><i
                                    class="bi bi-people"></i>Participants <x-count-badge>{{ $participantCount }}
                                </x-count-badge></a>
                        @endif
                        @if (Auth::user()->hasPermission(['view-result-reports']))
                            <a href="{{ route('tests-performed-list') }}" class="list-group-item"><i
                                    class="bx bxs-flask"></i>Tests Performed <x-count-badge>{{ $testsPerformedCount }}
                                </x-count-badge></a>
                            {{-- <li
                                class="nav-item list-group-item {{ request()->segment(3) == 'lists' ? 'menuitem-active' : '' }}">
                                <a class="list-group-item" href="#listing" data-bs-toggle="collapse" role="button"
                                    aria-expanded="false" aria-controls="listing">
                                    <i class="bx bxs-file"></i> Unit Lists
                                </a>
                                <div class="collapse " id="listing">
                                    <ul class="nav flex-column">
                                        <!--end nav-item-->
                                        <li>
                                            <a href="" class="nav-link ">Departments</a>
                                        </li>
                                        <li>
                                            <a href="" class="nav-link ">Projects</a>
                                        </li>
                                    </ul>
                                    <!--end nav-->
                                </div>
                                <!--end sidebarAnalytics-->
                            </li> --}}
                            {{-- <div class="list-group-item">
                                <a class="d-flex align-items-center justify-content-between text-light"
                                    data-bs-toggle="collapse" href="#sampleReportsDropdown" role="button"
                                    aria-expanded="false" aria-controls="sampleReportsDropdown">
                                    <span><i class="bi bi-clipboard-data"></i> Sample Reports</span>
                                    <i class="bi bi-chevron-down small"></i>
                                </a>
                                <div class="collapse mt-2" id="sampleReportsDropdown">
                                    <ul class="nav flex-column ms-3">
                                        <li class="nav-item">
                                            <a href="" class="nav-link">Daily
                                                Report</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="" class="nav-link">Monthly
                                                Report</a>
                                        </li>
                                    </ul>
                                </div>
                            </div> --}}
                        @endif
                    </div>
                </div>
                <div class="tab-pane fade" id="sample-storage">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-0">Sample Storage</h5>
                            </div>
                        </div>
                        @if (Auth::user()->hasPermission(['access-settings']))
                            <a href="{{ route('freezer-location') }}" class="list-group-item"><i
                                    class="bi bi-geo-alt-fill"></i>Freezer Locations</a>
                            <a href="{{ route('freezers') }}" class="list-group-item"><i
                                    class="bi bi-thermometer-snow"></i>Freezers</a>
                        @endif
                    </div>
                </div>


                <div class="tab-pane fade {{ request()->segment(1) == 'report' ? 'active show' : '' }}"
                    id="sample-reports">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-0">Reports</h5>
                            </div>
                        </div>
                        <a class="list-group-item" href="{{ route('tests-tat-report') }}">
                            <i class="bi bi-file"></i>TAT Group Report
                        </a>
                        <a class="list-group-item" href="{{ route('result-tat-report') }}"><i
                                class="bi bi-file"></i>Test Result TAT</a>
                        {{-- <a class="nav-link" href="{{ route('tests-count-report') }}"></i>Test Q
                                Request
                            </a> --}}
                        <a class="list-group-item" href="{{ route('tests-study-count-report') }}"><i
                                class="bi bi-file"></i>Test counts

                        </a>
                        <a class="list-group-item" href="{{ route('result-tat-done-report') }}"><i
                                class="bi bi-file"></i>Lab Yearly Tests

                        </a>


                    </div>
                </div>

                @if (Auth::user()->hasPermission(['manage-users']))
                    <div class="tab-pane fade {{ request()->segment(2) == 'usermgt' ? 'active show' : '' }}"
                        id="pills-user-management">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-0">User Management</h5>
                                </div>
                            </div>
                            <a href="{{ route('facilityInformation.index') }}" class="list-group-item"><i
                                    class="bi bi-hospital"></i>Facility Profile</a>
                            <a href="{{ route('usermanagement') }}" class="list-group-item"><i
                                    class="bi bi-person"></i>Users<x-count-badge>{{ $usersCount }}</x-count-badge>
                            </a>
                            <a href="{{ route('laboratories') }}" class="list-group-item"><i
                                    class="bx bx-clinic"></i>Laboratories<x-count-badge>{{ $laboratoryCount }}
                                </x-count-badge></a>
                            <a href="{{ route('designations') }}" class="list-group-item"><i
                                    class="bi bi-person-square"></i>Designations<x-count-badge>{{ $designationCount }}
                                </x-count-badge></a>
                            <a href="{{ route('user-roles.index') }}"
                                class="list-group-item {{ request()->segment(3) == 'user-roles' ? 'active' : '' }}"><i
                                    class="bi bi-person-check"></i>Roles<x-count-badge>{{ $rolesCount }}
                                </x-count-badge>
                            </a>
                            <a href="{{ route('user-permissions.index') }}"
                                class="list-group-item {{ request()->segment(3) == 'user-permissions' ? 'active' : '' }}"><i
                                    class="bi bi-person-x"></i>Permissions<x-count-badge>{{ $permissionsCount }}
                                </x-count-badge></a>
                            <a href="{{ route('user-roles-assignment.index') }}"
                                class="list-group-item {{ request()->segment(3) == 'user-roles-assignment' ? 'active' : '' }}"><i
                                    class="bi bi-card-checklist"></i>Role Assiginment</a>
                            <a href="{{ route('logs') }}" class="list-group-item"><i
                                    class="bi bi-list-check"></i>Login
                                Activity</a>
                            <a href="{{ route('useractivity') }}" class="list-group-item"><i
                                    class="bi bi-list-columns"></i>User
                                Activity</a>
                        </div>
                    </div>
                @endif

                @if (Auth::user()->hasPermission(['access-settings']))
                    <div class="tab-pane fade"
                        class="tab-pane fade {{ request()->segment(2) == 'settings' ? 'active show' : '' }}"
                        id="pills-management">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-0">Settings</h5>
                                </div>
                            </div>
                            <a href="{{ route('facilities') }}" class="list-group-item"><i
                                    class="bi bi-hospital"></i>Facilities<x-count-badge>{{ $facilityCount }}
                                </x-count-badge></a>
                            <a href="{{ route('studies') }}" class="list-group-item"><i
                                    class="bi bi-kanban"></i>Studies/Projects<x-count-badge>{{ $studyCount }}
                                </x-count-badge></a>
                            <a href="{{ route('requesters') }}" class="list-group-item"><i
                                    class="bi bi-people"></i>Requesters<x-count-badge>{{ $requesterCount }}
                                </x-count-badge></a>
                            <a href="{{ route('collectors') }}" class="list-group-item"><i
                                    class="bx bx-test-tube"></i>Sample Collectors<x-count-badge>{{ $collectorCount }}
                                </x-count-badge></a>
                            <a href="{{ route('couriers') }}" class="list-group-item"><i
                                    class="bi bi-truck"></i>Couriers<x-count-badge>{{ $courierCount }}</x-count-badge>
                            </a>
                            <a href="{{ route('platforms') }}" class="list-group-item"><i
                                    class="bi bi-gear-wide-connected"></i>Platforms<x-count-badge>{{ $platformCount }}
                                </x-count-badge></a>
                            <a href="{{ route('kits') }}" class="list-group-item"><i class="bx bx-bong"></i>Kits
                                <x-count-badge>{{ $kitCount }}</x-count-badge>
                            </a>
                            <a href="{{ route('categories') }}" class="list-group-item"><i
                                    class="bi bi-virus"></i>Test
                                Categories<x-count-badge>{{ $testCategoryCount }}</x-count-badge></a>
                            <a href="{{ route('tests') }}" class="list-group-item"><i class="bx bxs-flask"></i>Tests
                                <x-count-badge>{{ $testCount }}</x-count-badge>
                            </a>
                            <a href="{{ route('sampletypes') }}" class="list-group-item"><i
                                    class="bx bxs-vial"></i>Sample Types<x-count-badge>{{ $sampleTypeCount }}
                                </x-count-badge></a>
                            <a href="{{ route('qualityReports') }}" class="list-group-item"><i
                                    class="bx bxs-vial"></i>Quality Reports<x-count-badge>{{ $sampleTypeCount }}
                                </x-count-badge></a>
                        </div>
                    </div>
                @endif

                <div class="tab-pane fade" id="pills-user-profile">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-0">User Profile</h5>
                            </div>
                        </div>
                        <a href="{{ route('user.account') }}" class="list-group-item"><i
                                class="bi bi-person"></i>Account Details</a>
                        <a href="{{ route('myactivity') }}" class="list-group-item"><i class="bi bi-person"></i>My
                            Activity</a>
                    </div>
                </div>
            </div>
        </div>
    </aside>
    <!--end start sidebar -->
</div>
