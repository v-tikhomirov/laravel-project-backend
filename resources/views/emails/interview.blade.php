@component('mail::message')
    Hello, {{ $user_name }}!<br/>
    {{ $company_name }} just scheduled an interview.

    The date of the interview:
    {{ \Carbon\Carbon::parse($interview_date)->format('Y d M') }} {{ $interview_time }}

    Thanks,<br>
    {{ config('app.name') }} Team
@endcomponent
