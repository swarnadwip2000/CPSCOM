@extends('admin.layouts.master')
@section('title')
    Derick Veliz | Create Group
@endsection
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
<section id="loading">
    <div id="loading-content"></div>
</section>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       
    <div class="page-wrapper">

        <div class="content container-fluid">

            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Create</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Group</a></li>
                            <li class="breadcrumb-item active">Create a New Group</li>
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
                            <div class="col-xl-12 mx-auto">
                                <h6 class="mb-0 text-uppercase">Create Group</h6>
                                <hr>
                                <div class="card border-0 border-4">
                                    <form action="{{ route('group.store') }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <div class="border p-4 rounded">

                                            {{-- <label for="inputEnterYourName" class="col-form-label"><h5>Section 1:- </h5></label> --}}
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="inputEnterYourName" class="col-form-label">Group Name
                                                        <span style="color: red;">*</span></label>
                                                    <input type="text" name="name" id="" class="form-control"
                                                        value="{{ old('name') }}">
                                                    @if ($errors->has('name'))
                                                        <div class="error" style="color:red;">
                                                            {{ $errors->first('name') }}</div>
                                                    @endif
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="inputEnterYourName" class="col-form-label">Admin <span
                                                            style="color: red;">*</span></label>
                                                    <select name="admin_id" id="" class="form-control select-admin"
                                                        data-route="{{ route('groups.get-users') }}">
                                                        <option value="">Select a Admin</option>
                                                        @foreach ($admins as $admin)
                                                            <option value="{{ $admin->data()['uid'] }}">
                                                                {{ $admin->data()['name'] }}</option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('admin_id'))
                                                        <div class="error" style="color:red;">
                                                            {{ $errors->first('admin_id') }}</div>
                                                    @endif
                                                </div>

                                                <div class="col-md-6">
                                                    <label for="inputPhoneNo2" class="col-form-label">Members<span
                                                            style="color:red">*<span></label>
                                                    <select name="user_id[]" id=""
                                                        class="form-control multi-select get-user" multiple>

                                                    </select>
                                                    @if ($errors->has('user_id'))
                                                        <div class="error" style="color:red;">
                                                            {{ $errors->first('user_id') }}</div>
                                                    @endif
                                                </div>

                                                <div class="col-md-6">
                                                    <label for="inputPhoneNo2" class="col-form-label">Image</label>
                                                    <input type="file" name="image" id=""
                                                        class="form-control">
                                                    @if ($errors->has('image'))
                                                        <div class="error" style="color:red;">
                                                            {{ $errors->first('image') }}</div>
                                                    @endif
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="inputEnterYourName" class="col-form-label">Group Description
                                                        </label>
                                                    <textarea name="description" id="description" class="form-control" cols="30" rows="10">{{ old('description') }}</textarea>
                                                    @if ($errors->has('description'))
                                                        <div class="error" style="color:red;">
                                                            {{ $errors->first('description') }}</div>
                                                    @endif
                                                </div>
                                                <div class="row" style="margin-top: 10px;">
                                                    <div class="col-sm-9">
                                                        <button type="submit" class="btn btn-info px-5"
                                                            style="color: white; background-color: #10aafd; border-radius: 30px;">Create</button>
                                                    </div>
                                                </div>
                                            </div>
                                    </form>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(".multi-select").select2({
            // maximumSelectionLength: 2
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#loading-bar-spinner').hide();
            $('.select-admin').on('change', function() {
                var admin_id = $(this).val();
                var route = $(this).data('route');
                $('#loading').addClass('loading');
                $('#loading-content').addClass('loading-content');
                $.ajax({
                    url: route,
                    type: 'GET',
                    data: {
                        admin_id: admin_id
                    },
                    success: function(output) {
                        $('.get-user').html(output);
                        $('#loading').removeClass('loading');
                        $('#loading-content').removeClass('loading-content');
                    }
                });
            });
        });
    </script>
@endpush
