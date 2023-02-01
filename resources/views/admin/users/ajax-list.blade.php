<div class="row staff-grid-row">
    @if (count($employees) > 0)
        @foreach ($employees as $employee)
            <div class="col-md-4 col-sm-6 col-12 col-lg-4 col-xl-3">
                <div class="profile-widget">
                    <div class="profile-img">
                        <a href="{{ route('employees.profile', $employee->id) }}" class="avatar"><img
                            @if (isset($employee['profile_picture'])) src="{{ Storage::url($employee['profile_picture']) }}" @else src="{{ asset('admin_assets/img/profiles/avatar-02.jpg') }}" @endif
                                alt=""></a>
                    </div>
                    <div class="dropdown profile-action">
                        <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                            aria-expanded="false"><i class="material-icons">more_vert</i></a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item edit-employees" href="#" data-bs-toggle="modal"
                                data-bs-target="#edit_employee" data-id="{{ $employee['id'] }}"
                                data-route="{{ route('admin.employees.edit', $employee->id) }}"><i
                                    class="fa fa-pencil m-r-5"></i> Edit</a>
                            <a class="dropdown-item emp_delete" href="javascript:void(0);" data-bs-toggle="modal"
                                data-bs-target="#delete_employee" data-id="{{ $employee['id'] }}"
                                data-route="{{ route('employees.delete', $employee->id) }}"><i
                                    class="fa fa-trash-o m-r-5"></i> Delete</a>
                        </div>
                    </div>
                    <h4 class="user-name m-t-10 mb-0 text-ellipsis"><a
                            href="{{ route('employees.profile', $employee->id) }}">{{ $employee['full_name'] }}</a>
                    </h4>
                    <div class="small text-muted">{{ $employee['designation']['name'] }}</div>
                </div>
            </div>
        @endforeach
    @else
        <p>No data found...</p>
    @endif
</div>
