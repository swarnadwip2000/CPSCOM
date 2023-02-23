@component('mail::message')

<p>Hi {{ $maildata['name'] }}</p> 
<p>You have been upgraded to Admin.</p> 


Thanks,<br>
{{ config('app.name') }}
@endcomponent
