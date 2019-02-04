@component('mail::message')
# Introduction

We just need you to confirm your email address to prove you are actually a human being. Thanks!

@component('mail::button', ['url' => url('/register/confirm?token='. $user->confirmation_token)])
Confirm Email
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
