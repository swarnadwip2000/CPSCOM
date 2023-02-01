@component('mail::message')

<h2>Welcome to the site {{$maildata['first_name']}} {{$maildata['last_name']}},</h2>
<p> Your registered email-id is <b>{{$maildata['email']}}</b> and password is <b>{{$maildata['password']}}</b>. </p>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
