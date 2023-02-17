<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Forgot Password - Chat Box Admin Panel</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('admin_assets/img/favicon.png') }}">

    <link rel="stylesheet" href="{{ asset('admin_assets/css/bootstrap.min.css') }}">

    <link rel="stylesheet" href="{{ asset('admin_assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin_assets/plugins/fontawesome/css/all.min.css') }}">

    <link rel="stylesheet" href="{{ asset('admin_assets/css/line-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin_assets/css/material.css') }}">

    <link rel="stylesheet" href="{{ asset('admin_assets/css/font-awesome.min.css') }}">

    <link rel="stylesheet" href="{{ asset('admin_assets/css/line-awesome.min.css') }}">

    <link rel="stylesheet" href="{{ asset('admin_assets/css/font-awesome.min.css') }}">

    <link rel="stylesheet" href="{{ asset('admin_assets/css/style.css') }}">

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        .form-gap {
            padding-top: 70px;
        }
    </style>
</head>

<body>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <div class="form-gap"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="text-center">
                            <h3><i class="fa fa-lock fa-4x"></i></h3>
                            <h2 class="text-center">Forgot Password?</h2>
                            <p>You can reset your password here.</p>
                            <div class="panel-body">

                                <form id="register-form" action="{{ route('admin.forget.password') }}" role="form"
                                    autocomplete="off" class="form" method="post">
                                    @csrf
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i
                                                    class="glyphicon glyphicon-envelope color-blue"></i></span>
                                            <input id="email" name="email" placeholder="email address"
                                                class="form-control" type="text" value="{{ old('email') }}">
                                        </div>
                                        @if ($errors->has('email'))
                                            <div class="error" style="color:red;">{{ $errors->first('email') }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <input name="recover-submit" class="btn btn-lg btn-primary btn-block"
                                            value="Reset Password" type="submit">
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="{{ asset('admin_assets/js/jquery-3.6.0.min.js') }}"></script>

<script src="{{ asset('admin_assets/js/bootstrap.bundle.min.js') }}"></script>

<script src="{{ asset('admin_assets/js/layout.js') }}"></script>
<script src="{{ asset('admin_assets/js/theme-settings.js') }}"></script>
<script src="{{ asset('admin_assets/js/greedynav.js') }}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
    @if(Session::has('message'))
    toastr.options = {
        "closeButton": true,
        "progressBar": true
    }
    toastr.success("{{ session('message') }}");
    @endif

    @if(Session::has('error'))
    toastr.options = {
        "closeButton": true,
        "progressBar": true
    }
    toastr.error("{{ session('error') }}");
    @endif

    @if(Session::has('info'))
    toastr.options = {
        "closeButton": true,
        "progressBar": true
    }
    toastr.info("{{ session('info') }}");
    @endif

    @if(Session::has('warning'))
    toastr.options = {
        "closeButton": true,
        "progressBar": true
    }
    toastr.warning("{{ session('warning') }}");
    @endif
</script>

<script src="{{ asset('admin_assets/js/app.js') }}"></script>

</html>
