@extends('admin.layouts.master')
@section('title')
    All Group Details - Derick Veliz admin
@endsection
@push('styles')
<style>
    .dataTables_filter{
        margin-bottom: 10px !important;
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
                        <h3 class="page-title">Members Information</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('group.index') }}">Groups</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Member</a></li>
                            <li class="breadcrumb-item active">List</li>
                        </ul>
                    </div>
                    <div class="col-auto float-end ms-auto">
                        <a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_admin" ><i
                                class="fa fa-plus"></i> Add a Member</a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="mb-0">Members Details of <b>"{{ $members[0]->data()['name'] }}"</b></h4>
                            </div>

                        </div>
                    </div>

                    <hr />
                    <div class="table-responsive">
                        <table id="myTable" class="dd table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Member Name</th>
                                    <th>Member Email</th>
                                    <th>Permission</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($members[0]['members'] as $key =>$member)
                              
                                    <tr>
                                        <td>{{ $member['name'] }}</td>
                                        <td>{{ $member['email']  }}</td>
                                        <td>
                                            @if($member['isAdmin'] == false)
                                                <span class="badge bg-success">Member</span>
                                            @else
                                                <span class="badge bg-danger">Admin</span>
                                            @endif
                                        </td>
                                        @if($member['isAdmin'] == false)
                                        <td >
                                               <a href="{{ route('group.members.delete',['user_id'=>$member['uid'], 'group_id'=>$members[0]->data()['id'] ]) }}" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
                                        </td>
                                        @else 
                                        <td></td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div id="add_admin" class="modal custom-modal fade" role="dialog">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add A New Member</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('group.members.store') }}" method="POST" id="createForm"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="group_id" value="{{ $members[0]->data()['id'] }}" id="">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Select a Member<span class="text-danger">*</span></label>
                                            <select name="add_member" id="add_member" class="form-control">
                                                <option value="">Select a Member</option>
                                                @foreach ($users as $user)
                                                <option value="{{ $user->data()['uid'] }}">
                                                    {{ $user->data()['name'] }}</option>
                                            @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="submit-section">
                                    <button class="btn btn-primary submit-btn">Add</button>
                                </div>
                            </form>
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
    $("#createForm").validate({
        rules: {
            add_member: {
                required: true,
            },
        },
        messages: {
            add_member: {
                required: "Please select a member",
            },
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

    });
</script>

<script>
    $(document).ready(function() {
        //Default data table
        $('#myTable').DataTable({
            "aaSorting": [],
            "columnDefs": [{
                    "orderable": false,
                    "targets": [3]
                },
                {
                    "orderable": true,
                    "targets": [0, 1, 2]
                }
            ]
        });

    });
</script>
@endpush
