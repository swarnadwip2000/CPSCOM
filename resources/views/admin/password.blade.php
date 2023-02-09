@extends('admin.layouts.master')
@section('title')
CPSCOM | Profile
@endsection
@push('styles')
@endpush

@section('content')
<div class="page-wrapper">
    <!--page-content-wrapper-->
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Change Password</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.profile') }}">Profile</a></li>
                        <li class="breadcrumb-item active">Password</li>
                    </ul>
                </div>
                
            </div>
        </div>
            
            <!--end breadcrumb-->
            <div class="card">
                <div class="card-body">
                    <div class="form-body">
                        <div class="row">
                            <div class="col-12 col-lg-5 border-right">
                                <form class="row g-3" action="{{route('admin.password.update')}}" method="post" enctype="multipart/form-data">
                                    @csrf
                                   
                                    <div class="col-12">
                                        <label class="form-label">Old Password</label>
                                        <input type="password"  name="old_password" class="form-control">
                                        @if($errors->has('old_password'))
                                            <div class="error" style="color:red;">{{ $errors->first('old_password') }}</div>
                                        @endif
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">New Password</label>
                                        <input type="password"  name="new_password" class="form-control">
                                        @if($errors->has('new_password'))
                                            <div class="error" style="color:red;">{{ $errors->first('new_password') }}</div>
                                        @endif
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Confirm Password</label>
                                        <input type="password"  name="confirm_password" class="form-control">
                                        @if($errors->has('confirm_password'))
                                            <div class="error" style="color:red;">{{ $errors->first('confirm_password') }}</div>
                                        @endif
                                    </div>
                                    
                                    <div class="col-6">
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </div>

                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end page-content-wrapper-->
</div>
@endsection

@push('scripts')
@endpush