@component('mail::message')

<p>Hi {{ $maildata['name'] }}</p> 
<p>{{ $maildata['content'] }}</p> 


Thanks,<br>
{{ config('app.name') }}
@endcomponent
