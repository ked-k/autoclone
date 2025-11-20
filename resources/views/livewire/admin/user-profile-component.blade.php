<div>
    <div class="row">
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm border-0 overflow-hidden">
                <div class="card-body">
                    <div class="profile-avatar text-center">
                        <img src="{{ Auth::user()->avatar ? asset('storage/' . $user->avatar) : asset('autolab-assets/images/avatars/avatar-1.png') }}"
                            class="rounded-circle shadow" width="120" height="120" alt="">
                    </div>
                    <div class="text-center mt-4">
                        <h4 class="mb-1">{{ $user->name }}</h4>
                        <p class="mb-0 text-secondary">{{ $user->designation->name ?? 'N/A' }}</p>
                        <div class="mt-4"></div>
                        <h6 class="mb-1">{{ $user->laboratory->laboratory_name ?? 'N/A' }}</h6>
                    </div>
                    <hr>
                    <div class="text-start">
                        <h5 class="">About</h5>
                    </div>
                </div>
                <ul class="list-group list-group-flush">
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-top">
                        Full Name
                        <span class="badge bg-info rounded-pill">{{ $user->fullName }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent">
                        Employee No
                        <span class="badge bg-info rounded-pill">{{ $user->emp_no ?? 'N/A' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent">
                        Contact
                        <span class="badge bg-info rounded-pill">{{ $user->contact ?? 'N/A' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent">
                        Email
                        <span class="badge bg-info rounded-pill">{{ $user->email ?? 'N/A' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent">
                        Active
                        @if ($user->is_active == 1)
                            <span class="badge bg-success rounded-pill">Active</span>
                        @else
                            <span class="badge bg-danger rounded-pill">Suspended</span>
                        @endif

                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent">
                        Signature
                        @if ($user->signature)
                            <span>
                                <img src="{{ asset('storage/' . $user->signature) }}" alt="" height="10%"
                                    width="30%">
                            </span>
                        @else
                            <span class="badge bg-warning rounded-pill">N/A</span>
                        @endif
                    </li>

                </ul>
            </div>
        </div>
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="mb-0">My Account</h5>
                    <hr>
                    <div class="card shadow-none border">
                        <div class="card-header">
                            <h6 class="mb-0">UPDATE INFORMATION</h6>
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="updateUser">

                                <div class="row">
                                    <div class="mb-3 col-md-4">
                                        <label for="title" class="form-label">Title</label>
                                        <select class="form-select select2" id="title" wire:model="title">
                                            <option value="" selected>Select</option>
                                            <option value="Mr">Mr</option>
                                            <option value="Mrs">Mrs</option>
                                            <option value="Ms">Ms</option>
                                            <option value="Miss">Miss</option>
                                            <option value="Dr">Dr</option>
                                            <option value="Eng">Eng</option>
                                            <option value="Prof">Prof</option>
                                        </select>
                                        @error('title')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3 col-md-4">
                                        <label for="surname" class="form-label">Surname</label>
                                        <input type="text" id="surname" class="form-control"
                                            wire:model.lazy="surname">
                                        @error('surname')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label for="first_name" class="form-label">First Name</label>
                                        <input type="text" id="first_name" class="form-control"
                                            wire:model.lazy="first_name">
                                        @error('first_name')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label for="other_name" class="form-label">Other Name</label>
                                        <input type="text" id="other_name" class="form-control"
                                            wire:model.lazy="other_name">
                                        @error('other_name')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label for="usercontact" class="form-label">Contact</label>
                                        <input type="text" id="usercontact" class="form-control"
                                            wire:model.lazy="contact">
                                        @error('contact')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label for="userEmail" class="form-label">Email</label>
                                        <input type="email" id="userEmail" class="form-control" wire:model="email"
                                            readonly>
                                        @error('email')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label for="avatar" class="form-label">Photo/Avatar</label>
                                        <input type="file" id="avatar" class="form-control"
                                            wire:model="avatar">
                                        @error('avatar')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <!-- end row-->
                                <div class="modal-footer">
                                    <x-button class="btn-success">{{ __('Update') }}</x-button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card shadow-none border">
                        <div class="card-header">
                            <h6 class="mb-0">CHANGE PASSWORD (<span class="text-info">Last changed at </span><span
                                    class="text-success">{{ $user->password_updated_at }}</span>)</h6>
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="changePassword">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="current_password" class="form-label">Current Password</label>
                                            <input type="text" id="current_password" class="form-control"
                                                wire:model.lazy="current_password">
                                            @error('current_password')
                                                <div class="text-danger text-small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="password" class="form-label">New Password</label>
                                            <input type="password" id="password" class="form-control"
                                                wire:model.lazy="password">
                                            @error('password')
                                                <div class="text-danger text-small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">Confirm New
                                                Password</label>
                                            <input type="password" id="password_confirmation" class="form-control"
                                                wire:model="password_confirmation">
                                            @error('password_confirmation')
                                                <div class="text-danger text-small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                    </div>
                                </div>
                                <!-- end row-->
                                <div class="modal-footer">
                                    <x-button class="btn-success">{{ __('Change') }}</x-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end row-->
    </div>

    @push('scripts')
        <script>
            @if (Session::has('password_change'))
                swal('Warning', "{{ session('password_change') }}", 'warning');
            @endif
        </script>
    @endpush
</div>
