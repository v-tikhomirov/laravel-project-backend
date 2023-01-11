@component('mail::message')
You've been invited as company manager to RutaJob.com.<br/>
We need a little more information to complete your registration, including a confirmation of your email address.

Click below to sign in to the system:

@component('mail::button', ['url' => $url])
    Sign In
@endcomponent

Thanks,<br>
{{ config('app.name') }} Team
@endcomponent
