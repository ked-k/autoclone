 <!-- ADD NEW USER Modal -->

 <div class="modal fade" id="addUser" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true">
     <div class="modal-dialog modal-lg">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="staticBackdropLabel">Add New User</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
             </div> <!-- end modal header -->
             <div class="modal-body">
                 <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data">
                     @csrf

                     <div class="row">
                         <div class="row col-md-12">
                             <div class="mb-3 col-md-4">
                                 <label for="title" class="form-label">Title</label>
                                 <select class="form-select" id="title" name="title" required>
                                     <option value="" selected>Select</option>
                                     <option value="Mr">Mr</option>
                                     <option value="Mrs">Mrs</option>
                                     <option value="Ms">Ms</option>
                                     <option value="Miss">Miss</option>
                                     <option value="Dr">Dr</option>
                                     <option value="Eng">Eng</option>
                                     <option value="Prof">Prof</option>
                                 </select>
                             </div>
                             <div class="mb-3 col-md-4">
                                 <label for="emp_no" class="form-label">Employee No</label>
                                 <input type="text" id="emp_no" class="form-control" name="emp_no">
                             </div>
                             <div class="mb-3 col-md-4">
                                 <label for="surname" class="form-label">Surname</label>
                                 <input type="text" id="surname" class="form-control" name="surname">
                             </div>
                             <div class="mb-3 col-md-4">
                                 <label for="first_name" class="form-label">First Name</label>
                                 <input type="text" id="first_name" class="form-control" name="first_name">
                             </div>
                             <div class="mb-3 col-md-4">
                                 <label for="other_name" class="form-label">Other Name</label>
                                 <input type="text" id="other_name" class="form-control" name="other_name">
                             </div>
                             <div class="mb-3 col-md-4">
                                 <label for="usercontact" class="form-label">Contact</label>
                                 <input type="text" id="usercontact" class="form-control" name="contact">
                             </div>
                             <div class="mb-3 col-md-4">
                                 <label for="userEmail" class="form-label">Email</label>
                                 <input type="email" id="userEmail" class="form-control" name="email">
                             </div>
                             <div class="mb-3 col-md-5">
                                 <label for="laboratory_id" class="form-label">Laboratory</label>
                                 <select class="form-select" id="laboratory_id" name="laboratory_id">
                                     <option selected value="">Select</option>
                                     @forelse ($laboratories as $laboratory)
                                         <option value='{{ $laboratory->id }}'>{{ $laboratory->laboratory_name }}
                                         </option>
                                     @empty
                                     @endforelse
                                 </select>
                             </div>
                             <div class="mb-3 col-md-5">
                                 <label for="designation_id" class="form-label">Designation</label>
                                 <select class="form-select" id="designation_id" name="designation_id">
                                     @if (!$designations->isEmpty())
                                         <option selected value="">Select</option>
                                         @foreach ($designations as $designation)
                                             <option value='{{ $designation->id }}'>{{ $designation->name }}
                                             </option>
                                         @endforeach
                                     @else
                                         <option selected value="">None</option>
                                     @endif
                                 </select>
                             </div>
                             <div class="mb-3 col-md-6">
                                 <label for="avatar" class="form-label">Photo</label>
                                 <input type="file" id="avatar" class="form-control" name="avatar">
                             </div>

                             <div class="mb-3 col-md-6">
                                 <label for="signature" class="form-label">Signature</label>
                                 <input type="file" id="signature" class="form-control" name="signature">
                             </div>
                             <div class="mb-3 col-md-2">
                                 <label for="isActive" class="form-label">Status</label>
                                 <select class="form-select" id="isActive" name="is_active">
                                     <option selected value="">Select</option>
                                     <option value='1'>Active</option>
                                     <option value='0'>Inactive</option>
                                 </select>
                             </div>
                             <div class="mb-3 col-md-6">
                                 <label for="password" class="form-label">Password</label>
                                 <input type="text" id="password" class="form-control" name="password" required
                                     readonly placeholder="Focus to Auto-Generate" onfocus="generatePass();"
                                     name="password">
                             </div>
                         </div> <!-- end col -->
                     </div>
                     <!-- end row-->
                     <div class="d-grid mb-0 text-center">
                         <button class="btn btn-success" type="submit" id="submitBtn"> Add User</button>
                     </div>
                 </form>
             </div>

         </div> <!-- end modal content-->
     </div> <!-- end modal dialog-->
 </div> <!-- end modal-->
 <!-- UPDATE USER Modal -->
 @foreach ($users as $key => $user)
     <div class="modal fade" id="editUser{{ $user->id }}" data-bs-backdrop="static" data-bs-keyboard="false"
         tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
         <div class="modal-dialog modal-lg">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="staticBackdropLabel">UPDATE USER</h5>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                 </div> <!-- end modal header -->
                 <div class="modal-body">
                     <form method="POST" action="{{ route('users.update', [$user->id]) }}"
                         enctype="multipart/form-data">
                         @csrf
                         @method('PUT')
                         <div class="row">
                             <div class="row col-md-12">
                                 <div class="mb-3 col-md-4">
                                     <label for="emp_id2" class="form-label">Emp-No</label>
                                     <input type="text" style="text-transform: uppercase" id="emp_id2"
                                         class="form-control" name="emp_id" required readonly
                                         value="{{ $user->employee->emp_id }}">
                                 </div>
                                 <div class="mb-3 col-md-4">
                                     <label for="is_active2" class="form-label">Status</label>
                                     <select class="form-select" id="is_active2" name="is_active" required>
                                         @if ($user->employee->status == 'Active')
                                             <option value="1" style="color: rgb(130, 199, 130)" selected>
                                                 {{ $user->employee->status }}</option>
                                         @else
                                             <option value="0" style="color: red" selected>
                                                 {{ $user->employee->status }}</option>
                                         @endif
                                     </select>
                                 </div>
                                 <input type="text" id="employee_id" hidden class="form-control"
                                     name="employee_id" value="{{ $user->employee->id }}" required>
                                 <div class="mb-3 col-md-4">
                                     <label for="title2" class="form-label">Title</label>
                                     <select class="form-select" id="title2" name="title" required>
                                         <option value="{{ $user->employee->prefix }}">{{ $user->employee->prefix }}
                                         </option>
                                     </select>
                                 </div>
                                 <div class="mb-3 col-md-6">
                                     <label for="name2" class="form-label">Name</label>
                                     <input type="text" id="name2" class="form-control" readonly
                                         name="name" required
                                         value="{{ $user->employee->first_name . ' ' . $user->employee->surname }}">
                                 </div>
                                 <div class="mb-3 col-md-6">
                                     <label for="email2" class="form-label">Email</label>
                                     <input type="email" id="email2" class="form-control" name="email"
                                         readonly required value="{{ $user->employee->email }}">
                                 </div>
                             </div> <!-- end col -->

                             <div class="mb-3 col-md-6">
                                 <label for="contact2" class="form-label">Contact</label>
                                 <input type="text" id="contact2" class="form-control" name="contact" readonly
                                     required value="{{ $user->employee->contact }}">
                             </div>
                             <div class="mb-3 col-md-6">
                                 <label for="image2" class="form-label">Image</label>
                                 <input type="file" id="image2" class="form-control" name="avatar">
                             </div>


                         </div>
                         <!-- end row-->
                         <div class="modal-footer">
                             <x-button>{{ __('Update') }}</x-button>
                             <x-button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                                 {{ __('Close') }}</x-button>
                         </div>
                     </form>
                 </div>

             </div> <!-- end modal content-->
         </div> <!-- end modal dialog-->
     </div> <!-- end modal-->
 @endforeach
 <!-- VIEW USER ACCOUNT DETAILS -->
 @foreach ($users as $key => $user)
     <div class="modal fade" id="viewUser{{ $user->id }}" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
         <div class="modal-dialog modal-lg">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="staticBackdropLabel">USER DETAILS</h5>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                 </div> <!-- end modal header -->
                 <div class="modal-body">
                     <div class="row">
                         <div class="col-sm-12">
                             <!-- Profile -->
                             <div class="card bg-primary">
                                 <div class="card-body profile-user-box">
                                     <div class="row">
                                         <div class="col-sm-8">
                                             <div class="row align-items-center">
                                                 <div class="col-auto">
                                                     <div class="avatar-lg">
                                                         <img src="{{ asset('storage/' . $user->avatar) }}"
                                                             alt="" class="rounded-circle img-thumbnail">
                                                     </div>
                                                 </div>
                                                 <div class="col">
                                                     <div>
                                                         <h4 class="mt-1 mb-1 text-white">
                                                             {{ $user->title . ' ' . $user->name }}</h4>
                                                         <p class="font-13 text-white-50">{{ $user->email }}</p>

                                                         <ul class="mb-0 list-inline text-light">
                                                             <li class="list-inline-item me-3">
                                                                 <h5 class="mb-1">{{ $user->emp_id }}</h5>
                                                                 <p class="mb-0 font-13 text-white-50">Emp-No</p>
                                                             </li>
                                                             <li class="list-inline-item me-3">
                                                                 <h5 class="mb-1">{{ $user->contact }}</h5>
                                                                 <p class="mb-0 font-13 text-white-50">Contact</p>
                                                             </li>

                                                             <li class="list-inline-item">
                                                                 @if ($user->is_active == 1)
                                                                     <h5 class="mb-1"
                                                                         style="color: rgb(160, 221, 160)">Active</h5>
                                                                 @else
                                                                     <h5 class="mb-1" style="color: red">Suspended
                                                                     </h5>
                                                                 @endif
                                                                 <p class="mb-0 font-13 text-white-50">Status</p>
                                                             </li>
                                                         </ul>
                                                     </div>
                                                 </div>
                                             </div>
                                         </div> <!-- end col-->

                                         <div class="col-sm-4">
                                             <div class="text-center mt-sm-0 mt-3 text-sm-end">
                                                 <button type="button" class="btn btn-light" data-bs-toggle="modal"
                                                     data-bs-target="#editUser{{ $user->id }}"
                                                     data-bs-dismiss="modal">
                                                     <i class="mdi mdi-account-edit me-1"></i> Edit Profile
                                                 </button>
                                             </div>
                                         </div> <!-- end col-->
                                     </div> <!-- end row -->

                                 </div> <!-- end card-body/ profile-user-box-->
                             </div>
                             <!--end profile/ card -->
                         </div> <!-- end col-->
                     </div>
                 </div>
             </div> <!-- end modal content-->
         </div> <!-- end modal dialog-->
     </div> <!-- end modal-->
 @endforeach
 <script type="text/javascript">
     function generatePass() {
         var chars = "0123456789abcdefghijklmnopqrstuvwxyz!@#$%^&*()ABCDEFGHIJKLMNOPQRSTUVWXYZ";
         var passwordLength = 12;
         var password = "";
         var passwordInput = document.getElementById("password");
         for (var i = 0; i <= passwordLength; i++) {
             var randomNumber = Math.floor(Math.random() * chars.length);
             password += chars.substring(randomNumber, randomNumber + 1);
         };
         passwordInput.value = password;
         passwordInput.select();
         document.execCommand("copy");
     }
 </script>
