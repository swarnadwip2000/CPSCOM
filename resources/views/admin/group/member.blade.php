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
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Member</a></li>
                            <li class="breadcrumb-item active">List</li>
                        </ul>
                    </div>
                    <div class="col-auto float-end ms-auto">
                        <a href="{{ route('group.create') }}" class="btn add-btn" ><i
                                class="fa fa-plus"></i> Add Member</a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="mb-0">Members Details</h4>
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
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($members[0]['members'] as $member)
                                  
                                    <tr>
                                        <td>{{ $member['name'] }}</td>
                                        <td>{{ $member['email']  }}</td>
                                        <td >
                                               <a href="" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
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
                        "targets": [3, 4]
                    },
                    {
                        "orderable": true,
                        "targets": [0, 1, 2]
                    }
                ]
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
