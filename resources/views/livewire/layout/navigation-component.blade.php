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


                {{-- <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Patient Management">
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pills-patients" type="button"><i
                        class="bi bi-person-workspace"></i></button>
            </li>
           
            <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Sample Referral">
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pills-referrals" type="button"><i
                        class="bi bi-airplane-fill"></i></button>
            </li>
            <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Sample Storage">
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pills-storage" type="button"><i
                        class="bi bi-archive-fill"></i></button>
            </li>
            
            <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Logistic Management">
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pills-logistics" type="button"><i
                        class="bi bi-bar-chart-line-fill"></i></button>
            </li>

            <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Trainings">
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pills-trainings" type="button"><i
                        class="bi bi-easel2-fill"></i></button>
            </li>
            <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Documents/Resources">
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pills-resources" type="button"><i
                        class="bi bi-file-earmark-medical-fill"></i></button>
            </li>
            <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Engagements">
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pills-engagements" type="button"><i
                        class="bi bi-file-earmark-easel-fill"></i></button>
            </li>
            <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Help Desk">
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pills-helpdesk" type="button"><i
                        class="bi bi-question-square-fill"></i></button>
            </li> --}}

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
                {{-- <div class="tab-pane fade" id="pills-patients">
                <div class="list-group list-group-flush">
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-0">PATIENTS</h5>
                        </div>
                    </div>
                    <a href="javascript: void(0);" class="list-group-item"><i
                            class="bi bi-house-door-fill"></i>Create
                        New</a>
                    <a href="javascript: void(0);" class="list-group-item"><i class="bi bi-cast"></i>Today</a>
                    <a href="javascript: void(0);" class="list-group-item"><i class="bi bi-wallet"></i>This Week</a>
                    <a href="javascript: void(0);" class="list-group-item"><i class="bi bi-bar-chart-line"></i>This
                        Month</a>
                    <a href="javascript: void(0);" class="list-group-item"><i class="bi bi-archive"></i>This Year</a>
                    <a href="javascript: void(0);" class="list-group-item"><i class="bi bi-cast"></i>All Patients</a>
                </div>
            </div> --}}

                <div class="tab-pane fade {{ request()->segment(1) == 'samplemgt' || $navItem == 'samplemgt' ? 'active show' : '' }}"
                    id="pills-samples">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-0">Sample Mgt</h5>
                            </div>
                        </div>
                        @if (Auth::user()->hasPermission(['create-reception-info|review-reception-info']))
                            <a href="{{ route('samplereception') }}" class="list-group-item"><i
                                    class="bi bi-box2"></i>Reception<x-count-badge>{{ $batchesCount }}</x-count-badge>
                            </a>
                        @endif

                        @if (Auth::user()->hasPermission(['accession-samples']))
                            <a href="javascript: void(0);"
                                class="list-group-item {{ Request::routeIs('specimen-request') ? 'active' : '' }}"><i
                                    class="bi bi-receipt"></i>Accessioning</a>
                        @endif
                        
                        @if (Auth::user()->hasPermission(['assign-test-requests']))
                            <a href="{{ route('test-request-assignment') }}" class="list-group-item"><i
                                    class="bi bi-file-medical"></i>Assign
                                Requests<x-count-badge>{{ $testRequestsCount }}</x-count-badge></a>
                        @endif

                        @if (Auth::user()->hasPermission(['enter-results']))
                            <a href="{{ route('test-request') }}" class="list-group-item"><i
                                    class="bi bi-file-medical"></i>Test
                                Requests<x-count-badge>{{ $testAssignedCount }}</x-count-badge></a>

                            <a href="javascript: void(0);"
                                class="list-group-item {{ Request::routeIs('attach-test-results') ? 'active' : '' }}"><i
                                    class="bi bi-file-earmark-medical"></i>Attach Results</a>
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

                        @if (Auth::user()->hasPermission(['view-result-reports']))
                            <a href="{{ route('test-reports') }}" class="list-group-item"><i
                                    class="bi bi-file-earmark-text"></i>Result
                                Reports<x-count-badge>{{ $testReportsCount }}</x-count-badge></a>
                        @endif

                        @if (Auth::user()->hasPermission(['view-participant-info']))
                            <a href="{{ route('samples-list') }}" class="list-group-item"><i
                                    class="bx bxs-vial"></i>Samples<x-count-badge>{{ $samplesCount }}
                                </x-count-badge></a>
                            <a href="{{ route('tests-performed-list') }}" class="list-group-item"><i
                                    class="bx bxs-flask"></i>Tests Performed <x-count-badge>{{ $testsPerformedCount }}
                                </x-count-badge></a>
                            <a href="{{ route('participants') }}" class="list-group-item"><i
                                    class="bi bi-people"></i>Participants <x-count-badge>{{ $participantCount }}
                                </x-count-badge></a>
                        @endif
                    </div>
                </div>
                {{-- <div class="tab-pane fade" id="pills-referrals">
                <div class="list-group list-group-flush">
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-0">REFERRAL</h5>
                        </div>
                    </div>
                    <a href="javascript: void(0);" class="list-group-item"><i
                            class="bi bi-house-door-fill"></i>Outgoing</a>
                    <a href="javascript: void(0);" class="list-group-item"><i
                            class="bi bi-house-door-fill"></i>Incoming</a>
                    <a href="javascript: void(0);" class="list-group-item"><i class="bi bi-cast"></i>Sequence
                        Data</a>
                    <a href="javascript: void(0);" class="list-group-item"><i class="bi bi-wallet"></i>Track
                        Sample</a>
                    <a href="javascript: void(0);" class="list-group-item"><i
                            class="bi bi-bar-chart-line"></i>Equipment
                        Profiling</a>
                    <a href="javascript: void(0);" class="list-group-item"><i class="bi bi-archive"></i>Reports</a>

                </div>
            </div>
            <div class="tab-pane fade" id="pills-storage">
                <div class="list-group list-group-flush">
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-0">STORAGE</h5>
                        </div>
                    </div>
                    <a href="javascript: void(0);" class="list-group-item"><i
                            class="bi bi-house-door-fill"></i>Search
                        Sample</a>
                    <a href="javascript: void(0);" class="list-group-item"><i class="bi bi-house-door-fill"></i>Store
                        New</a>
                    <a href="javascript: void(0);" class="list-group-item"><i class="bi bi-cast"></i>Today</a>
                    <a href="javascript: void(0);" class="list-group-item"><i class="bi bi-wallet"></i>This Week</a>
                    <a href="javascript: void(0);" class="list-group-item"><i class="bi bi-bar-chart-line"></i>This
                        Month</a>
                    <a href="javascript: void(0);" class="list-group-item"><i class="bi bi-archive"></i>This Year</a>
                    <a href="javascript: void(0);" class="list-group-item"><i class="bi bi-cast"></i>All stored</a>
                </div>
            </div>
           
            <div class="tab-pane fade" id="pills-logistics">
                <div class="list-group list-group-flush">
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-0">LOGISTICS</h5>
                        </div>
                    </div>
                    <a href="javascript: void(0);" class="list-group-item"><i
                            class="bi bi-house-door-fill"></i>Inventory</a>
                    <a href="javascript: void(0);" class="list-group-item"><i
                            class="bi bi-house-door-fill"></i>Suppliers</a>
                    <a href="javascript: void(0);" class="list-group-item"><i class="bi bi-cast"></i>Couriers</a>
                    <a href="javascript: void(0);" class="list-group-item"><i class="bi bi-wallet"></i>Dry Ice
                        Sources</a>
                </div>
            </div> --}}

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
                        </div>
                    </div>
                @endif

                {{-- <div class="tab-pane fade" id="pills-resources">
                <div class="list-group list-group-flush">
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-0">Resources</h5>
                        </div>
                    </div>
                    <a href="javascript: void(0);" class="list-group-item"><i
                            class="bi bi-house-door-fill"></i>General
                        Documents</a>
                    <a href="javascript: void(0);" class="list-group-item"><i
                            class="bi bi-house-door-fill"></i>Shipment
                        Documents</a>
                </div>
            </div> --}}

                {{-- <div class="tab-pane fade" id="pills-trainings">
                <div class="list-group list-group-flush">
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-0">Training</h5>
                        </div>
                    </div>
                    <a href="javascript: void(0);" class="list-group-item"><i
                            class="bi bi-house-door-fill"></i>Trainers</a>
                    <a href="javascript: void(0);" class="list-group-item"><i
                            class="bi bi-house-door-fill"></i>Trainees</a>
                    <a href="javascript: void(0);" class="list-group-item"><i
                            class="bi bi-house-door-fill"></i>Training
                        Materials</a>
                </div>
            </div> --}}

                {{-- <div class="tab-pane fade" id="pills-engagements">
                <div class="list-group list-group-flush">
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-0">Engagements</h5>
                        </div>
                    </div>
                    <a href="javascript: void(0);" class="list-group-item"><i
                            class="bi bi-house-door-fill"></i>Minutes</a>
                    <a href="javascript: void(0);" class="list-group-item"><i
                            class="bi bi-house-door-fill"></i>Other</a>
                    <a href="javascript: void(0);" class="list-group-item"><i
                            class="bi bi-house-door-fill"></i>Reports</a>
                </div>
            </div> --}}

                {{-- <div class="tab-pane fade" id="pills-helpdesk">
                <div class="list-group list-group-flush">
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-0">Help Desk</h5>
                        </div>
                    </div>
                    <a href="javascript: void(0);" class="list-group-item"><i
                            class="bi bi-house-door-fill"></i>Tickets</a>
                    <a href="javascript: void(0);" class="list-group-item"><i
                            class="bi bi-house-door-fill"></i>Resolved
                        Issues</a>
                    <a href="javascript: void(0);" class="list-group-item"><i
                            class="bi bi-house-door-fill"></i>Reports</a>
                </div>
            </div> --}}
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
