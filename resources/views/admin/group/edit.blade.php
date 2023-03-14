@extends('admin.layouts.master')
@section('title')
    Derick Veliz | Create Group
@endsection
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
    <div class="page-wrapper">

        <div class="content container-fluid">

            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Edit</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Group</a></li>
                            <li class="breadcrumb-item active">Edit Group</li>
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
                                <h6 class="mb-0 text-uppercase">Edit Group</h6>
                                <hr>
                                <div class="card border-0 border-4">
                                    <div class="card-body">
                                        <form action="{{ route('group.update') }}" method="post"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="border p-4 rounded">
                                                <input type="hidden" name="group_id" value="{{ $groups->rows()[0]->id() }}">
                                                {{-- <label for="inputEnterYourName" class="col-form-label"><h5>Section 1:- </h5></label> --}}
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label for="inputEnterYourName" class="col-form-label">Group Name
                                                            <span style="color: red;">*</span></label>
                                                        <input type="text" name="name" id=""
                                                            class="form-control"
                                                            value="{{ $groups->rows()[0]->data()['name'] }}">
                                                        @if ($errors->has('name'))
                                                            <div class="error" style="color:red;">
                                                                {{ $errors->first('name') }}</div>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="inputEnterYourName" class="col-form-label">Admin <span
                                                                style="color: red;">*</span></label>
                                                                {{-- @dd($groups->rows()[0]->data()['members'] ) --}}
                                                        <select name="admin_id" id="" class="form-control select2">
                                                            @foreach ($admins as $key=>$admin)
                                                                <option value="{{ $admin->data()['uid'] }}" @foreach($groups->rows()[0]->data()['members'] as $member) {{ $member['name'] }} @if ($member['uid'] == $admin->data()['uid'])
                                                                    selected
                                                                    @endif
                                                                @endforeach >
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
                                                            class="form-control multi-select" multiple >
                                                            @foreach ($users as $key => $user)
                                                                    <option value="{{ $user->data()['uid'] }}" @foreach ($groups->rows()[0]->data()['members'] as $member)
                                                                        @if ($member['uid'] == $user->data()['uid'])
                                                                            selected 
                                                                        @endif   
                                                                    @endforeach>
                                                                        {{ $user->data()['name'] }}</option>
                                                            @endforeach

                                                        </select>
                                                        @if ($errors->has('user_id'))
                                                            <div class="error" style="color:red;">
                                                                {{ $errors->first('user_id') }}</div>
                                                        @endif
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="inputPhoneNo2" class="col-form-label">Image<span
                                                                style="color:red">*<span></label>
                                                        <input type="file" name="image" id=""
                                                            class="form-control">
                                                        @if ($errors->has('image'))
                                                            <div class="error" style="color:red;">
                                                                {{ $errors->first('image') }}</div>
                                                        @endif

                                                        @if (isset($groups->rows()[0]->data()['profile_picture']))
                                                            <div class="col-sm-9" style="display: flex;">
                                                                <div class="image-area m-4">
                                                                    <img src="{{ Storage::url($groups->rows()[0]->data()['profile_picture']) }}"
                                                                        alt="Preview">
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>


                                                    <div class="row" style="margin-top: 10px;">
                                                        <div class="col-sm-9">
                                                            <button type="submit" class="btn btn-info px-5"
                                                                style="color: white; background-color: #176d9b;">Update</button>
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

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(".multi-select").select2({
            // maximumSelectionLength: 2
        });
    </script>

@endpush
