 <!-- ADD NEW Role Modal -->

 <div class="modal fade" id="addRole" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true">
     <div class="modal-dialog modal-lg">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="staticBackdropLabel">Create New Role</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
             </div> <!-- end modal header -->
             <div class="modal-body">
                 <form method="POST" action="{{ route('user-roles.store') }}">
                     @csrf

                     <div class="row">
                         <div class="mb-3 col-md-6">
                             <label for="name" class="form-label">Name/Code</label>
                             <input type="text" id="name" class="form-control" name="name"
                                 placeholder="this-will-be-the-code-name" required>
                         </div>
                         <div class="mb-3 col-md-6">
                             <label for="display_name" class="form-label">Display Name</label>
                             <input type="text" id="display_name" class="form-control" name="display_name"
                                 placeholder="Edit user profile" required>
                         </div>
                         <div class="mb-3 col-md-12">
                             <label for="description" class="form-label">Description</label>
                             <textarea type="email" id="description" class="form-control" name="description"
                                 placeholder="Some description for the role"></textarea>
                         </div>
                     </div>

                     <div class="row">
                         <h6 class="text-success">Permissions</h6>
                         @foreach ($permissions as $permission)
                             <div class="mb-3 col-md-2">
                                 <div class="form-check form-check-inline">
                                     <input class="form-check-input" type="checkbox"
                                         id="permission{{ $permission->id }}" name="permissions[]"
                                         value="{{ $permission->id }}">
                                     <label class="form-check-label"
                                         for="permission{{ $permission->id }}">{{ $permission->display_name ?? $permission->name }}</label>
                                 </div>
                             </div>
                         @endforeach
                     </div>
                     <!-- end row-->
                     <div class="modal-footer">
                         <x-button>{{ __('Save') }}</x-button>
                         <x-button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('Close') }}
                         </x-button>
                     </div>
                 </form>
             </div>
         </div> <!-- end modal content-->
     </div> <!-- end modal dialog-->
 </div> <!-- end modal-->
