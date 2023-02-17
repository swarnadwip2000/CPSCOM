@extends('admin.layouts.master')
@section('title')
    Group Chat - Derick Veliz admin
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
                        <h3 class="page-title">Group Chat</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('group.index') }}">Team x Groups</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Team x Groups Chat</a></li>
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
                                <h4 class="mb-0">Team x Groups Chart</h4>
                            </div>

                        </div>
                    </div>

                    <hr />
                    <div class="table-responsive">
                        <table id="myTable" class="dd table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Chat By</th>
                                    <th>Delivered Date</th>
                                    <th>Delivered Time</th>
                                    <th>Message</th>
                                    {{-- <th>Action</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($chats->rows() as $chat)
                                @if ($chat->data()['type'] == 'text')
                                <tr>
                                    <td>@if (isset($chat->data()['sendBy']))
                                        {{ $chat->data()['sendBy'] }}
                                    @endif </td>
                                    <td> {{ date('d M Y',strtotime($chat->data()['time'])) }}</td>
                                    <td> {{ date('h : m A',strtotime($chat->data()['time'])) }}</td>
                                    <td>
                                        @if (isset( $chat->data()['message']))
                                        {{ $chat->data()['message'] }}
                                    @endif</td>
                                    
                                    {{-- <td align="center">
                                        <a href="javascript:void(0);" id="delete" data-route="{{ route('group.chat.delete',['chatId'=> $chat->id(), 'groupId'=> $group_id]) }}"><i
                                                class="fas fa-trash"></i></a>
                                    </td> --}}
                                </tr>
                                @endif
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
                        "targets": []
                    },
                    {
                        "orderable": true,
                        "targets": [0, 1, 2,3]
                    }
                ]
            });

        });
    </script>
    <script>
        $(document).on('click', '#delete', function(e) {
           swal({
               title: "Are you sure?",
               text: "To delete this chat.",
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
