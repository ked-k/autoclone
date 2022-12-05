<x-app-layout>
    <div class="row ">
        <div class="col-12">
            @include('layouts.messages')
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-pills bg-nav-pills nav-justified mb-3">
                        <li class="nav-item">
                            <a href="#createVisit" data-bs-toggle="tab" aria-expanded="false"
                                class="nav-link rounded-0 active">
                                <i class="mdi mdi-home-variant d-md-none d-block"></i>
                                <span class="d-none d-md-block">
                                    @if (!$profile)
                                        CREATE
                                    @endif
                                    FACILITY PROFILE
                                </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#editPatientInfo" data-bs-toggle="tab" aria-expanded="true"
                                class="nav-link rounded-0">
                                <i class="mdi mdi-account-circle d-md-none d-block"></i>
                                <span class="d-none d-md-block">EDIT FACILITY PROFILE <span>(<span
                                            class="text-danger">*</span>) is Required</span></span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane show active" id="createVisit">
                            <div class="row mb-0">
                                <div class="col-sm-12">
                                    <div class="card border-success border mt-1">
                                        <div class="card-body">
                                            @if ($profile)
                                                <div>
                                                    {{-- <div class="container d-flex align-items-center"> --}}
                                                    <img src="{{ asset('storage/' . $profile->logo) }}"
                                                        class="rounded-circle avatar-lg img-thumbnail img-center">

                                                    <h4 class="mb-0 mt-2">{{ $profile->facility_name }}</h4>
                                                    <p class="text-muted font-14">
                                                        {{ $profile->slogan ? $profile->slogan : '' }}</p>
                                                    <div class="text-start mt-3">
                                                        <h4 class="font-13 text-uppercase">About :</h4>
                                                        <p class="text-muted font-13 mb-3">
                                                            {{ $profile->about ? $profile->about : '......' }}
                                                        </p>
                                                        <p class="text-muted mb-2 font-13"><strong>FACILITY TYPE
                                                                :</strong> <span
                                                                class="ms-2">{{ $profile->facility_type ? $profile->facility_type : 'N/A' }}</span>
                                                        </p>

                                                        <p class="text-muted mb-2 font-13"><strong>PHYSICAL ADDRESS
                                                                :</strong><span
                                                                class="ms-2">{{ $profile->physical_address ? $profile->physical_address : 'N/A' }}</span>
                                                        </p>

                                                        <p class="text-muted mb-2 font-13"><strong>ADDRESS LINE 2
                                                                :</strong> <span
                                                                class="ms-2 ">{{ $profile->address2 ? $profile->address2 : 'N/A' }}</span>
                                                        </p>

                                                        <p class="text-muted mb-1 font-13"><strong>OFFICIAL CONTACT
                                                                :</strong> <span
                                                                class="ms-2">{{ $profile->contact ? $profile->contact : 'N/A' }}</span>
                                                        </p>
                                                        <p class="text-muted mb-1 font-13"><strong>OTHER CONTACT
                                                                :</strong> <span
                                                                class="ms-2">{{ $profile->contact2 ? $profile->contact2 : 'N/A' }}</span>
                                                        </p>
                                                        <p class="text-muted mb-1 font-13"><strong>FAX :</strong> <span
                                                                class="ms-2">{{ $profile->fax ? $profile->fax : 'N/A' }}</span>
                                                        </p>
                                                        <p class="text-muted mb-1 font-13"><strong>OFFICAL EMAIL
                                                                :</strong> <span
                                                                class="ms-2">{{ $profile->email ? $profile->email : 'N/A' }}</span>
                                                        </p>
                                                        <p class="text-muted mb-1 font-13"><strong>OTHER EMAIL
                                                                :</strong> <span
                                                                class="ms-2">{{ $profile->email2 ? $profile->email2 : 'N/A' }}</span>
                                                        </p>
                                                        <p class="text-muted mb-1 font-13"><strong>WEBSITE :</strong>
                                                            <span
                                                                class="ms-2">{{ $profile->website ? $profile->website : 'N/A' }}</span>
                                                        </p>
                                                        <p class="text-muted mb-1 font-13"><strong>TIN :</strong> <span
                                                                class="ms-2">{{ $profile->tin ? $profile->tin : 'N/A' }}</span>
                                                        </p>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="mt-0">
                                                    {{-- <h4 class="header-title mb-3  text-center">Patients</h4> --}}
                                                    <form method="POST"
                                                        action="{{ route('facilityInformation.store') }}"
                                                        enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="row">
                                                            <div class="mb-3 col-md-5">
                                                                <label for="facility_name" class="form-label">Facility
                                                                    Name<span class="text-danger">*</span></label>
                                                                <input type="text" id="facility_name"
                                                                    class="form-control" name="facility_name" required
                                                                    value="{{ old('facility_name', '') }}">
                                                            </div>

                                                            <div class="mb-3 col-md-3">
                                                                <label for="facility" class="form-label">Facility
                                                                    Type<span class="text-danger">*</span></label>
                                                                <select class="form-select select2"
                                                                    data-toggle="select2" id="facility"
                                                                    name="facility_type" required>
                                                                    <option selected value="">Select</option>
                                                                    <option value="GOVERMENT">GOVERMENT</option>
                                                                    <option value="NGO">NGO</option>
                                                                    <option value="PRIVATE">PRIVATE</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3 col-md-4">
                                                                <label for="physical_address"
                                                                    class="form-label">Physical Address<span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" id="physical_address"
                                                                    class="form-control" name="physical_address"
                                                                    required value="{{ old('physical_address', '') }}"
                                                                    placeholder="Plot, Street, Block, City">
                                                            </div>

                                                            <div class="mb-3 col-md-6">
                                                                <label for="slogan" class="form-label">Slogan</label>
                                                                <input type="text" id="slogan"
                                                                    class="form-control" name="slogan"
                                                                    value="{{ old('slogan', '') }}"
                                                                    placeholder="slogan">
                                                            </div>
                                                            <div class="mb-3 col-md-3">
                                                                <label for="address2" class="form-label">Address Line
                                                                    2</label>
                                                                <input type="text" id="address2"
                                                                    class="form-control" name="address2"
                                                                    value="{{ old('address2', '') }}"
                                                                    placeholder="P.O BOX.....">
                                                            </div>

                                                            <div class="mb-3 col-md-3">
                                                                <label for="email" class="form-label">Email<span
                                                                        class="text-danger">*</span></label>
                                                                <input type="email" id="email"
                                                                    class="form-control" name="email" required
                                                                    value="{{ old('email', '') }}">
                                                            </div>
                                                            <div class="mb-3 col-md-3">
                                                                <label for="email2" class="form-label">Email
                                                                    2</label>
                                                                <input type="email" id="email2"
                                                                    class="form-control" name="email2"
                                                                    value="{{ old('email2', '') }}">
                                                            </div>
                                                            <div class="mb-3 col-md-3">
                                                                <label for="contact" class="form-label">Contact
                                                                    1<span class="text-danger">*</span></label>
                                                                <input type="text" id="contact"
                                                                    class="form-control" name="contact" required
                                                                    value="{{ old('contact', '') }}">
                                                            </div>
                                                            <div class="mb-3 col-md-3">
                                                                <label for="contact2" class="form-label">Contact
                                                                    2</label>
                                                                <input type="text" id="contact2"
                                                                    class="form-control" name="contact2"
                                                                    value="{{ old('contact2', '') }}">
                                                            </div>
                                                            <div class="mb-3 col-md-3">
                                                                <label for="fax" class="form-label">Fax</label>
                                                                <input type="text" id="fax"
                                                                    class="form-control" name="fax"
                                                                    value="{{ old('fax', '') }}">
                                                            </div>
                                                            <div class="mb-3 col-md-3">
                                                                <label for="website"
                                                                    class="form-label">Website</label>
                                                                <input type="url" id="website"
                                                                    class="form-control" name="website"
                                                                    value="{{ old('website', '') }}">
                                                            </div>
                                                            <div class="mb-3 col-md-3">
                                                                <label for="tin" class="form-label">TIN</label>
                                                                <input type="text" id="tin"
                                                                    class="form-control" name="tin"
                                                                    value="{{ old('tin', '') }}">
                                                            </div>
                                                            <div class="mb-3 col-md-3">
                                                                <label for="logo" class="form-label">Logo<span
                                                                        class="text-danger">*</span></label>
                                                                <input type="file" id="logo"
                                                                    class="form-control" name="logo"
                                                                    accept=".png,.jpg,.jpeg" required>
                                                            </div>
                                                            <div class="mb-3 col-md-3">
                                                                <label for="logo2" class="form-label">Logo
                                                                    2</label>
                                                                <input type="file" id="logo2"
                                                                    class="form-control" name="logo2"
                                                                    accept=".png,.jpg,.jpeg">
                                                            </div>
                                                            <div class="mb-3 col-md-12">
                                                                <label for="about2"
                                                                    class="form-label">About/Description</label>
                                                                <textarea type="text" id="about2" class="form-control" name="about">{{ old('about', '') }}</textarea>
                                                            </div>

                                                            {{-- <div class="mb-3 mt-1 col-md-12 text-end">
                                                                <button class="btn btn-success" type="submit"
                                                                    id="submitBt">Save</button>
                                                            </div> --}}
                                                        </div>
                                                        <div class="modal-footer">
                                                            <x-button>{{ __('Save') }}</x-button>
                                                            {{-- <x-button type="button" class="btn btn-danger" 
                                                                data-bs-dismiss="modal">{{ __('Close') }}</x-button> --}}
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif

                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="editPatientInfo">
                            @if ($profile)
                                <form action="{{ route('facilityInformation.update', $profile->id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <div class="row">
                                        <div class="mb-3 col-md-5">
                                            <label for="facility_name2" class="form-label">Facility Name<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="facility_name2" class="form-control"
                                                name="facility_name" required value="{{ $profile->facility_name }}">
                                        </div>

                                        <div class="mb-3 col-md-3">
                                            <label for="facility_type2" class="form-label">Facility Type<span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select" data-toggle="select2" id="facility_type2"
                                                name="facility_type" required>

                                                <option selected value="{{ $profile->facility_type }}">
                                                    {{ $profile->facility_type }}</option>
                                                <option value="GOVERMENT">GOVERMENT</option>
                                                <option value="NGO">NGO</option>
                                                <option value="PRIVATE">PRIVATE</option>
                                                <option value="OTHER">OTHER</option>

                                            </select>
                                        </div>
                                        <div class="mb-3 col-md-4">
                                            <label for="physical_address2" class="form-label">Physical Address<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="physical_address2" class="form-control"
                                                name="physical_address" required
                                                value="{{ $profile->physical_address }}"
                                                placeholder="Plot, Street, Block, City">
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="slogan2" class="form-label">Slogan</label>
                                            <input type="text" id="slogan2" class="form-control" name="slogan"
                                                value="{{ $profile->slogan }}" placeholder="slogan">
                                        </div>
                                        <div class="mb-3 col-md-3">
                                            <label for="address22" class="form-label">Address Line 2</label>
                                            <input type="text" id="address22" class="form-control"
                                                name="address2" value="{{ $profile->address2 }}"
                                                placeholder="P.O BOX.....">
                                        </div>

                                        <div class="mb-3 col-md-3">
                                            <label for="email2" class="form-label">Email<span
                                                    class="text-danger">*</span></label>
                                            <input type="email" id="email2" class="form-control" name="email"
                                                required value="{{ $profile->email }}">
                                        </div>
                                        <div class="mb-3 col-md-3">
                                            <label for="email22" class="form-label">Email 2</label>
                                            <input type="email" id="email22" class="form-control" name="email2"
                                                value="{{ $profile->email2 }}">
                                        </div>
                                        <div class="mb-3 col-md-3">
                                            <label for="contact2" class="form-label">Contact 1<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="contact2" class="form-control" name="contact"
                                                required value="{{ $profile->contact }}">
                                        </div>
                                        <div class="mb-3 col-md-3">
                                            <label for="contact22" class="form-label">Contact 2</label>
                                            <input type="text" id="contact22" class="form-control"
                                                name="contact2" value="{{ $profile->contact2 }}">
                                        </div>
                                        <div class="mb-3 col-md-3">
                                            <label for="fax2" class="form-label">Fax</label>
                                            <input type="text" id="fax2" class="form-control" name="fax"
                                                value="{{ $profile->fax }}">
                                        </div>
                                        <div class="mb-3 col-md-3">
                                            <label for="website2" class="form-label">Website</label>
                                            <input type="url" id="website2" class="form-control" name="website"
                                                value="{{ $profile->website }}">
                                        </div>
                                        <div class="mb-3 col-md-3">
                                            <label for="tin2" class="form-label">TIN</label>
                                            <input type="text" id="tin2" class="form-control" name="tin"
                                                value="{{ $profile->tin }}">
                                        </div>
                                        <div class="mb-3 col-md-3">
                                            <label for="logo2" class="form-label">Logo</label>
                                            <input type="file" id="logo2" class="form-control" name="logo"
                                                accept=".png,.jpg,.jpeg">
                                        </div>
                                        <div class="mb-3 col-md-3">
                                            <label for="logo22" class="form-label">Logo 2</label>
                                            <input type="file" id="logo22" class="form-control" name="logo2"
                                                accept=".png,.jpg,.jpeg">
                                        </div>
                                        <div class="mb-3 col-md-12">
                                            <label for="about" class="form-label">About/Description</label>
                                            <textarea type="text" id="about" class="form-control" name="about" rows="5">{{ $profile->about }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <x-button>{{ __('Update') }}</x-button>
                                    </div>
                                </form>
                            @else
                                <div class="alert alert-warning" role="alert">
                                    <i class="dripicons-warning me-2"></i><strong>No Information</strong> Available for
                                    update!
                                </div>
                            @endif

                        </div>

                    </div>
                </div><!-- end card body-->
            </div><!-- end card -->
        </div> <!-- end col -->
    </div>
    <!--end of row-->

    </x-super-admin-layout>
