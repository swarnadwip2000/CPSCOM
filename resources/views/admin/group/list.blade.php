@extends('admin.layouts.master')
@section('title')
    All Group Details - Derick Veliz admin
@endsection
@push('styles')
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
                        <h3 class="page-title">Team x Groups Information</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Team x Groups</a></li>
                            <li class="breadcrumb-item active">List</li>
                        </ul>
                    </div>
                    <div class="col-auto float-end ms-auto">
                        {{-- <a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_group"><i
                                class="fa fa-plus"></i> Add Group</a> --}}
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="mb-0">Team x Groups Details</h4>
                            </div>

                        </div>
                    </div>

                    <hr />
                    <div class="table-responsive">
                        <table id="myTable" class="dd table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Group Name</th>
                                    <th>Group Admin Name</th>
                                    <th>Name of Group Members</th>
                                    <th>No. of Group Member</th>
                                    <th>Chat</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($groups as $key => $group)
                                {{-- @dd($group) --}}
                                    <tr>
                                        <td>{{ $group->data()['name'] }}</td>
                                        <td>@foreach ($group->data()['members'] as $isAdmin)
                                            @if($isAdmin['isAdmin'] == true)
                                            {{ $isAdmin['name'] }}
                                            @endif
                                        @endforeach</td>
                                        @php
                                            $count = count($group->data()['members']);
                                        @endphp
                                        <td>@foreach ($group->data()['members'] as $key => $isAdmin)
                                            {{ $isAdmin['name'] }} @if($key < $count - 1), @endif
                                        @endforeach</td>
                                        <td>{{ count($group->data()['members']) }}</td>
                                        <td><a href="{{ route('group.chat', $group->data()['id']) }}"><i
                                                    class="fas fa-eye"></i></a></td>
                                        <td >
                                            <a title="Update Group Image" 
                                                href="{{ route('group.image.update', $group->data()['id']) }}"><button class="btn btn-primary" style="border-radius: 20px"><i class="fa fa-plus"></i> Add Image</button></a> &nbsp;&nbsp;

                                            {{-- <a title="Delete Group" data-route="{{ route('group.delete', $group->id()) }}"
                                                href="javascipt:void(0);" id="delete"><i class="fas fa-trash"></i></a> --}}
                                        </td>
                                    </tr>
                                @endforeach


                            </tbody>

                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            //Default data table
            $('#myTable').DataTable({
                "aaSorting": [],
                "columnDefs": [{
                        "orderable": false,
                        "targets": [4, 5]
                    },
                    {
                        "orderable": true,
                        "targets": [0, 1, 2, 3]
                    }
                ]
            });

        });
    </script>
    <script>
        $(document).on('click', '#delete', function(e) {
            swal({
                    title: "Are you sure?",
                    text: "To delete this group.",
                    type: "warning",
                    confirmButtonText: "Yes",
                    showCancelButton: true
                })
                .then((result) => {
                    if (result.value) {
                        window.location = $(this).data('route');
                    } else if (result.dismiss === 'cancel') {
                        swal(
                            'Cancelled',
                            'Your stay here :)',
                            'error'
                        )
                    }
                })
        });
    </script>
@endpush
