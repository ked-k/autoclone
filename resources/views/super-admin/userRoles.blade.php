<x-app-layout>
    <div class="row">
        <div class="col-12">
            @include('layouts.messages')
            <div class="card">
                <div class="card-header pt-0">
                    <div class="row mb-2">
                        <div class="col-sm-12 mt-3">
                            <div class="d-sm-flex align-items-center">
                                <h5 class="mb-2 mb-sm-0">
                                    User Roles
                                </h5>
                                <div class="ms-auto">
                                    <a type="button" href="#" class="btn btn-info mb-2 me-1"
                                        data-bs-toggle="modal" data-bs-target="#addRole">Create Role</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datableButtons" class="table table-striped mb-0 w-100 nowrap">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th class="th">Display Name</th>
                                    <th class="th">Name</th>
                                    <th class="th"># Permissions</th>
                                    <th>Action</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($roles as $key => $role)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $role->display_name }}</td>
                                        <td> {{ $role->name }}</td>
                                        <td>{{ $role->permissions_count }}</td>
                                        <td class="table-action d-flex">
                                            @if (\Laratrust\Helper::roleIsEditable($role))
                                                <a href="{{ route('user-roles.edit', $role->id) }}"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="" data-bs-original-title="Edit"
                                                    class="action-ico btn btn-outline-info mx-1">
                                                    <i class="bi bi-pencil-square"></i></a>
                                            @else
                                                <a href="{{ route('user-roles.show', $role->id) }}"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="" data-bs-original-title="View"
                                                    class="action-ico btn btn-outline-success">
                                                    <i class="bi bi-eye-fill"></i></a>
                                            @endif

                                            <form action="{{ route('user-roles.destroy', $role->id) }}" method="POST">
                                                @method('DELETE')
                                                @csrf
                                                @if (\Laratrust\Helper::roleIsDeletable($role))
                                                    <a href="#" class="action-ico btn btn-outline-danger mx-1" 
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="" data-bs-original-title="Delete"
                                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                                        <i class="bi bi-trash"></i></a>
                                                @else
                                                    {{-- <i class="uil-padlock"></i> --}}
                                                @endif
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div> <!-- end preview-->

                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
    <!-- end row-->
    @include('super-admin.createRoleModal')

</x-app-layout>
