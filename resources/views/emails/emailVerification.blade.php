@component('mail::message')
Thanks for getting started with our RutaJob.com.<br/>
We need a little more information to complete your registration, including a confirmation of your email address.

Click below to confirm your email address:

@component('mail::button', ['url' => $url])
    Verify
@endcomponent

Thanks,<br>
{{ config('app.name') }} Team
@endcomponent
