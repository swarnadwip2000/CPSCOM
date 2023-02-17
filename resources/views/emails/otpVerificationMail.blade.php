@component('mail::message')
# Reset Password

<h2>Your four-digit OTP is </h2><h2>{{$otp}}</h2>
<p>Please do not share your One Time Pin With Anyone. You made a request to reset your password. Please discard if this wasn't you.</p>

Thanks,<br>
{{ config('app.name') }}
@endcomponent