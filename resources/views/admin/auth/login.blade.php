<!DOCTYPE html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="Smarthr - Bootstrap Admin Template">
    <meta name="keywords"
        content="admin, estimates, bootstrap, business, corporate, creative, management, minimal, modern, accounts, invoice, html5, responsive, CRM, Projects">
    <meta name="author" content="Dreamguys - Bootstrap Admin Template">
    <meta name="robots" content="noindex, nofollow">
    <title>Login - Chat Box Admin Panel</title>

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
</head>

<body class="account-page">

    <div class="main-wrapper">
        <div class="account-content">
            <div class="container">

                <div class="account-logo">
                    <a href="admin-dashboard.html"><img src="{{ asset('admin_assets/img/logo2.png') }}"
                            alt="Dreamguy's Technologies"></a>
                </div>

                <div class="account-box">
                    <div class="account-wrapper">
                        <h3 class="account-title">Login</h3>
                        <p class="account-subtitle">Access to our dashboard</p>

                        <form action="{{ route('admin.login.check') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="email" class="form-control" name="email" id="inputEmailAddress"
                                    placeholder="Email Address">
                                @if ($errors->has('email'))
                                    <div class="error" style="color:red;">{{ $errors->first('email') }}</div>
                                @endif
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col">
                                        <label>Password</label>
                                    </div>
                                    {{-- <div class="col-auto">
                                        <a class="text-muted" href="forgot-password.html">
                                            Forgot password?
                                        </a>
                                    </div> --}}
                                </div>
                                <div class="position-relative" id="show_hide_password">
                                    <input type="password" class="form-control border-end-0" name="password"
                                        id="inputChoosePassword" placeholder="Enter Password">
                                    <a href="javascript:;" class=""><span class="fa fa-eye-slash" id="toggle-password"></span></a>
                                </div>
                                @if ($errors->has('password'))
                                    <div class="error" style="color:red;">{{ $errors->first('password') }}</div>
                                @endif
                            </div>
                            <div class="form-group text-center">
                                <button class="btn btn-primary account-btn" type="submit">Login</button>
                            </div>
                            <div class="account-footer">
                                <p><a href="{{ route('admin.forget.password.show') }}">Forgot Password?</a></p>
                            </div>
                        </form>

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
<script>
    $(document).ready(function() {
        $("#show_hide_password a").on('click', function(event) {
            event.preventDefault();
            if ($('#show_hide_password input').attr("type") == "text") {
                $('#show_hide_password input').attr('type', 'password');
                $('#show_hide_password i').addClass("bx-hide");
                $('#show_hide_password i').removeClass("bx-show");
            } else if ($('#show_hide_password input').attr("type") == "password") {
                $('#show_hide_password input').attr('type', 'text');
                $('#show_hide_password i').removeClass("bx-hide");
                $('#show_hide_password i').addClass("bx-show");
            }
        });
    });
</script>

</html>
