@extends('admin.layouts.master')
@section('title')
    Team x Member Details - Derick Veliz admin
@endsection
@push('styles')
    <style type="text/css">
        .loading {
            z-index: 9999;
            position: absolute;
            top: 0;
            left: -5px;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .loading-content {
            position: absolute;
            border: 16px solid #f3f3f3;
            border-top: 16px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            top: 40%;
            left: 50%;
            animation: spin 2s linear infinite;
            /* filter: blur(1px);
            z-index: 9999; */
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endpush

@section('content')
@php
    use App\Models\User;
@endphp
    <section id="loading">
        <div id="loading-content"></div>
    </section>
    <div class="page-wrapper">

        <div class="content container-fluid">

            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Team x Members Information</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Team x Members</a></li>
                            <li class="breadcrumb-item active">List</li>
                        </ul>
                    </div>
                    <div class="col-auto float-end ms-auto">
                        <a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_employee"><i
                            class="fa fa-plus"></i> Add a Team</a>
                    </div>
                </div>
            </div>


            <div class="row staff-grid-row">
                @foreach ($users as $user)
                        @if ($user->data()['isAdmin'] == false)
                            <div class="col-md-4 col-sm-6 col-12 col-lg-4 col-xl-3">
                                <div class="profile-widget">
                                    <div class="profile-img">
                                        @php
                                            $profile = User::profile_picture($user->data()['uid']);
                                        @endphp
                                        <a href="javascript:void(0);" class="avatar">
                                            @if ( isset($profile->profile_picture))
                                            <img src="{{ Storage::url($profile->profile_picture) }}" alt="profiole" id="img-{{ $user->data()['uid'] }}" data-url="{{ Storage::url($profile->profile_picture) }}"></a>
                                            @else
                                            <img src="{{ asset('admin_assets/img/profiles/avatar-02.jpg') }}" alt="" id="img-{{ $user->data()['uid'] }}" data-url="{{ asset('frontend_assets/img/profiles/avatar-02.jpg') }}"></a>
                                            @endif
                                            
                                    </div>
                                    <div class="dropdown profile-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                                            aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item edit-users" href="#" data-bs-toggle="modal"
                                                data-bs-target="#edit_employee" data-id="{{ $user->data()['uid'] }}"
                                                data-route="{{ route('user.edit', $user->data()['uid']) }}"><i
                                                    class="fa fa-pencil m-r-5"></i> Edit</a>
                                            <a class="dropdown-item user_delete" data-id="{{ $user->data()['uid'] }}"
                                                data-route="{{ route('user.delete', $user->data()['uid']) }}" href="#"
                                                data-bs-toggle="modal" data-bs-target="#delete_employee"><i
                                                    class="fa fa-trash-o m-r-5"></i> Delete</a>
                                        </div>
                                    </div>
                                    <h4 class="user-name m-t-10 mb-0 text-ellipsis"><a
                                            href="jsavscript:void(0);">{{ $user->data()['name'] }}</a></h4>
                                    <div class="small text-muted">{{ $user->data()['email'] }}</div>
                                </div>
                            </div>
                            @endif
                @endforeach
            </div>
            <div id="add_employee" class="modal custom-modal fade" role="dialog">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Team x Members Information</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('user.create') }}" method="POST" id="createForm"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="profile-img-wrap edit-img">
                                            <img class="inline-block" alt="user"
                                                src="{{ asset('admin_assets/img/profiles/avatar-02.jpg') }}">
                                            <div class="fileupload btn">
                                                <span class="btn-text">upload</span>
                                                <input class="upload" type="file" name="profile_picture"
                                                    id="profile_picture">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Name<span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="name"
                                                        id="name">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Email<span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="email"
                                                        id="email">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Password<span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" name="password" id="password">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Confirm Password<span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" name="confirm_password"
                                                id="confirm_password">
                                        </div>
                                    </div>
                                </div>
                                <div class="submit-section">
                                    <button class="btn btn-primary submit-btn">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div id="edit_employee" class="modal custom-modal fade" role="dialog">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Team x Members Information Update</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('user.update') }}" method="POST" id="editForm"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" id="hidden_id" name="id" value="">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="profile-img-wrap edit-img" >
                                            <div class="show-image"></div>
                                            <div class="fileupload btn">
                                                <span class="btn-text">edit</span>
                                                <input class="upload" type="file" name="profile_picture"
                                                    id="profile_picture">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Name<span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="edit_name"
                                                        id="edit_name">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Email<span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="edit_email"
                                                        id="edit_email">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="submit-section">
                                    <button class="btn btn-primary submit-btn">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


            <div class="modal custom-modal fade" id="delete_employee" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="form-header">
                                <h3>Delete Employee</h3>
                                <p>Are you sure want to delete?</p>
                            </div>
                            <div class="modal-btn delete-action">
                                <div class="row">
                                    <div class="col-6" id="user_destroy">

                                    </div>
                                    <div class="col-6">
                                        <a href="javascript:void(0);" data-bs-dismiss="modal"
                                            class="btn btn-primary cancel-btn">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            jQuery.validator.addMethod("emailExt", function(value, element, param) {
                return value.match(/^[a-zA-Z0-9_\.%\+\-]+@[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,}$/);
            }, 'Your E-mail is wrong');

            jQuery.validator.addMethod("phoneUS", function(phone_number, element) {
                phone_number = phone_number.replace(/\s+/g, "");
                return this.optional(element) || phone_number.length > 9 &&
                    phone_number.match(/^(\+?1-?)?(\([2-9]\d{2}\)|[2-9]\d{2})-?[2-9]\d{2}-?\d{4}$/);
            }, "Please specify a valid phone number");


            $("#createForm").validate({
                rules: {
                    name: "required",
                    email: {
                        required: true,
                        email: true,
                        emailExt: true,
                    },
                    password: {
                        required: true,
                        minlength: 8
                    },
                    confirm_password: {
                        required: true,
                        minlength: 8,
                        equalTo: "#password"
                    },

                },

            });

            $("#editForm").validate({
                rules: {
                    edit_name: "required",
                    edit_email: {
                        required: true,
                        email: true,
                        emailExt: true,
                    },
                },

            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('.user_delete').on('click', function() {
                var id = $(this).data('id');
                var route = $(this).data('route');
                $('#user_destroy').html('<a href="' + route +
                    '" class="btn btn-primary continue-btn">Delete</a>')
            });

            $('.edit-users').on('click', function() {
                var id = $(this).data('id');
                var route = $(this).data('route');
                var img_url = $('#img-'+id).data('url');
                // console.log(route);
                // console.log(img_url);
                $('#loading').addClass('loading');
                $('#loading-content').addClass('loading-content');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: route,
                    type: 'POST',
                    data: {
                        id: id,
                    },
                    dataType: 'JSON',
                    success: async function(data) {
                        try {
                            console.log(data);
                            await $('#hidden_id').val(data.user.detail.uid);
                            await $('#edit_name').val(data.user.detail.displayName);
                            await $('#edit_email').val(data.user.detail.email);
                            await $('.show-image').html('<img src="'+img_url+'" class="inline-block" alt="user">');
                            await $('#loading').removeClass('loading');
                            await $('#loading-content').removeClass('loading-content');
                        } catch (error) {
                            console.log(error);
                        }
                    }
                });
            });
        });
    </script>
@endpush
