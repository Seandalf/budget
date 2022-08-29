@component('mail::message')
# Dear {{ $name }}
 
Reset your password using the link below.
 
@component('mail::button', ['url' => $url])
Reset Password
@endcomponent
 
Thanks,<br>
{{ config('app.name') }}
@endcomponent