@extends('admin.layouts.master')
@section('title')
    All Group Details - Derick Veliz admin
@endsection
@push('styles')
    <style>
        .dataTables_filter {
            margin-bottom: 10px !important;
        }
    </style>
@endpush

@section('content')
    @php
        use App\Models\User;
        use App\Helpers\Helper;
    @endphp
    <section id="loading">
        <div id="loading-content"></div>
    </section>
    <div class="page-wrapper">

        <div class="content container-fluid">

            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Groups Information</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Groups</a></li>
                            <li class="breadcrumb-item active">List</li>
                        </ul>
                    </div>
                    <div class="col-auto float-end ms-auto">
                        <a href="{{ route('group.create') }}" class="btn add-btn"><i class="fa fa-plus"></i> Add Group</a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="mb-0">Groups Details</h4>
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
                                    {{-- <th>Name of Group Members</th> --}}
                                    <th>No. of Group Member</th>
                                    <th>Member list</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($groups as $key => $group)
                                    {{-- @dd($groups)    --}}
                                    <tr>
                                        <td>
                                            @if (isset($group->data()['name']))
                                                {{ $group->data()['name'] }}
                                            @endif
                                        </td>
                                        <td>
                                            @if ($group->data()['members'] != null)
                                            @foreach (Helper::groupAdminName($group->data()['id']) as $item)
                                            <span class="badge bg-inverse-info">
                                                {{ $item['name'] }} </span>
                                            @endforeach
                                            @endif
                                        </td>
                                        <td>{{ Helper::totalMembers($group->data()['id']) }}</td>
                                        <td><a href="{{ route('group.members', $group->data()['id']) }}"><button
                                                    class="btn btn-warning"><i class="fas fa-eye"></i> View </button></a>
                                        </td>

                                        <td>
                                            {{-- <a title="Upload Group Image" 
                                                href="{{ route('group.image.update', $group->data()['id']) }}"><button class="btn btn-danger" style="border-radius: 20px; background: linear-gradient(to right, #10acff 0%, #1f1f1f 100%);
                                                border: none;"><i class="fa fa-upload"></i> Upload Image</button></a> &nbsp;&nbsp; --}}

                                            <a title="Edit Group" href="{{ route('group.edit', $group->id()) }}"><i
                                                    class="fas fa-edit"></i></a>
                                            {{-- <a title="Edit Group" 
                                                href="{{ route('group.delete', $group->id()) }}"><i class="fas fa-trash"></i></a> --}}
                                        </td>
                                    </tr>
                                @endforeach


                            </tbody>

                        </table>
                        {{-- @if ($totalPages > 0)
                            @php
                                $page = request()->has('page') ? request()->get('page') : 1;
                            @endphp
                            <div class="row" style="margin-top: 10px;">
                                <div class="col-md-6 float-left">
                                    <span class="current-page text-info mx-2">Page {{ $page }} of {{ $totalPages }}</span>
                                </div>
    
                                <div class="col-md-6" style="float: right">
                                    <div class="" role="status">
                                        @if ($page > 1)
                                            <a href="{{ route('group.index', ['page' => $page - 1]) }}"
                                                class="btn btn-primary btn-sm">Previous</a>
                                        @endif
                                        @for ($i = 1; $i <= $totalPages; $i++)
                                            @if ($i == $page)
                                                <a href="{{ route('group.index', ['page' => $i]) }}"
                                                    class="btn btn-primary btn-sm active"
                                                    style="width: 50px;">{{ $i }}</a>
                                            @else
                                                <a href="{{ route('group.index', ['page' => $i]) }}"
                                                    class="btn btn-primary btn-sm" style="width: 50px;">{{ $i }}</a>
                                            @endif
                                        @endfor
                                        @if ($page < $totalPages)
                                            <a href="{{ route('group.index', ['page' => $page + 1]) }}"
                                                class="btn btn-primary btn-sm">Next</a>
                                        @endif
    
                                    </div>
                                </div>
                            </div>
                        @endif --}}
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
                        "targets": [3, 4]
                    },
                    {
                        "orderable": true,
                        "targets": [0, 1, 2]
                    }
                ],
                // off pagination
                // "bPaginate": false,
                // off info
                // "bInfo": false,
            });

        });
    </script>
    {{-- <script>
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
    </script> --}}
@endpush
