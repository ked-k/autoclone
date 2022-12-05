<div>
    <!--start top header-->
    <header class="top-header">
        <nav class="navbar navbar-expand">
            <div class="mobile-toggle-icon d-xl-none">
                <i class="bi bi-list"></i>
            </div>

            <div class="search-toggle-ico d-xl-none ms-auto">
                {{-- <i class="bi bi-search"></i> --}}
            </div>
            <form class="searchbar d-none d-xl-flex ms-auto">
                <div class="input-group">
                    <button class="btn btn-primary radius-30 dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false"><i class="bi bi-search"></i></button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item {{ $model === 'SampleReception' ? 'active' : '' }}"
                                href="javascript: void(0);" wire:click="$set('model','SampleReception')">Batch</a>
                        </li>
                        <li><a class="dropdown-item {{ $model === 'Sample' ? 'active' : '' }}"
                                href="javascript: void(0);" wire:click="$set('model','Sample')">Sample</a>
                        </li>
                        <li><a class="dropdown-item {{ $model === 'Participant' ? 'active' : '' }}"
                                href="javascript: void(0);" wire:click="$set('model','Participant')">Participant</a>
                        </li>
                        <li><a class="dropdown-item {{ $model === 'TestResult' ? 'active' : '' }}"
                                href="javascript: void(0);" wire:click="$set('model','TestResult')">Test Result</a>
                        </li>
                        <hr class="dropdown-divider">
                        <li><a class="dropdown-item" href="javascript: void(0);" wire:click="resetData()">Clear</a>
                    </ul>
                    <input class="form-control" aria-label="Text input search with with selectable target"
                        type="text" placeholder="{{ $placeHolder }}"
                        @if (!$searchInputActive) readonly @endif wire:model.lazy="search">
                </div>
                <div class="position-absolute top-50 translate-middle-y d-block d-xl-none search-close-icon"><i
                        class="bi bi-x-lg"></i></div>
            </form>

            <div class="top-navbar-right ms-3">
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item dropdown dropdown-large">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="#"
                            data-bs-toggle="dropdown">
                            <div class="user-setting d-flex align-items-center gap-1">
                                <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : asset('autolab-assets/images/avatars/avatar-1.png') }}"
                                    class="user-img" alt="">
                                <div class="user-name d-none d-sm-block">{{ Auth::user()->name }}</div>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('user.account') }}">
                                    <div class="d-flex align-items-center">
                                        <div class="setting-icon"><i class="bi bi-person-fill"></i></div>
                                        <div class="setting-text ms-3"><span>Profile</span></div>
                                    </div>
                                </a>
                            </li>

                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf

                                    <a href="#" onclick="event.preventDefault(); this.closest('form').submit();"
                                        class="dropdown-item">
                                        <div class="d-flex align-items-center">
                                            <div class="setting-icon"><i class="bi bi-lock-fill"></i></div>
                                            <div class="setting-text ms-3"><span>Logout</span></div>
                                        </div>
                                    </a>
                                </form>
                            </li>
                            <hr>
                            <li>
                                <h6 class="mb-0 text-center text-info">Color Mode</h6>
                                <hr>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" wire:model='theme' id="Light"
                                        value="light-theme">
                                    <label class="form-check-label" for="Light">Light</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" wire:model='theme' id="Dark"
                                        value="dark-theme">
                                    <label class="form-check-label" for="Dark">Dark</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" wire:model='theme' id="SemiDark"
                                        value="semi-dark">
                                    <label class="form-check-label" for="SemiDark">Semi Dark</label>
                                </div>
                                {{-- <hr> --}}
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" wire:model='theme' id="Minimal"
                                        value="minimal-theme" checked>
                                    <label class="form-check-label" for="Minimal">Minimal Theme</label>
                                </div>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <!--end top header-->
    @push('scripts')
        <script>
            window.addEventListener('found', event => {
                window.open(`${event.detail.url}`, '_blank').focus();
            });
        </script>
    @endpush
</div>
