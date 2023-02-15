@extends('admin.layouts.master')
@section('title')
    Group Chat Image Upload - Derick Veliz admin
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
                        <h3 class="page-title">Image Upload</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('group.index') }}">Group</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Image Upload</a></li>
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
                                <h6 class="mb-0 text-uppercase">Group Image Upload</h6>
                                <hr>
                                <div class="card border-0 border-4">
                                    <div class="card-body">
                                        <form action="{{route('group.image.upload')}}" method="post" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="group_id" value="{{ $group_id }}">
                                            <div class="border p-4 rounded">
                                                
                                                {{-- <label for="inputEnterYourName" class="col-form-label"><h5>Section 1:- </h5></label> --}}
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label for="inputPhoneNo2" class=" col-form-label">Image<span style="color:red">*<span></label>
                                                        <input type="file" class="form-control" id="inputPhoneNo2" name="image" >
                                                        @if($errors->has('image'))
                                                        <div class="error" style="color:red;">{{ $errors->first('image') }}</div>
                                                        @endif
                                                    </div>
                                                    @if(isset($group))
                                                     <div class="col-md-6">
                                                        @if($group->profile_picture)
                                                        <a href="{{Storage::url($group->profile_picture)}}" target="_blank">
                                                        <img src="{{ Storage::url($group->profile_picture) }}" alt="" style="height: 200px; width: 200px;  float:left; margin-top:10px; padding-left:20px;"></a>
                                                        @endif
                                                    </div>
                                                    @endif
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
