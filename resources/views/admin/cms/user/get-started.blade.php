@extends('admin.layouts.master')
@section('title')
Derick Veliz | Get Started Cms
@endsection
@push('styles')
@endpush

@section('content')
<div class="page-wrapper">

    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Content Management System</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Get Started Cms</a></li>
                        <li class="breadcrumb-item active">Update</li>
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
                        <h6 class="mb-0 text-uppercase">Get Started Cms</h6>
                        <hr>
                        <div class="card border-0 border-4">
                            <div class="card-body">
                                <form action="{{route('cms.get-started.update')}}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $getStarted['id'] }}">
                                    <input type="hidden" name="is_panel" value="user">
                                    <div class="border p-4 rounded">
                                        
                                        {{-- <label for="inputEnterYourName" class="col-form-label"><h5>Section 1:- </h5></label> --}}
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="inputEnterYourName" class="col-form-label">Title <span style="color: red;">*</span></label>
                                                <input type="text" class="form-control" id="inputEnterYourName" value="{{$getStarted['title']}}" name="title" >
                                                @if($errors->has('title'))
                                                <div class="error" style="color:red;">{{ $errors->first('title') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <label for="inputEnterYourName" class="col-form-label">Description<span style="color: red;">*</span></label>
                                                <textarea type="text" class="form-control" id="inputEnterYourName" name="description" >{{$getStarted['description']}}</textarea>
                                                @if($errors->has('description'))
                                                <div class="error" style="color:red;">{{ $errors->first('description') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <label for="inputPhoneNo2" class=" col-form-label">Image<span style="color:red">*<span></label>
                                                <input type="file" class="form-control" id="inputPhoneNo2" value="{{$getStarted['image']}}" name="image" >
                                                @if($errors->has('image'))
                                                <div class="error" style="color:red;">{{ $errors->first('image') }}</div>
                                                @endif
                                            </div>
                                             <div class="col-md-6">
                                                @if($getStarted->image)
                                                <a href="{{Storage::url($getStarted->image)}}" target="_blank">
                                                <img src="{{ Storage::url($getStarted->image) }}" alt="" style="height: 200px; width: 200px;  float:left; margin-top:10px; padding-left:20px;"></a>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="row" style="margin-top: 10px;">
                                            <div class="col-sm-9">
                                                <button type="submit" class="btn btn-info px-5" style="color: white; background-color: #176d9b;">Update</button>
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

@endpush