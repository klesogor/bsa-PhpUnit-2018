@component('mail::message')
    # Successful trade!

    {{$buyerName}} successfully bought {{$amount}}  of currency from your lot {{$lotId}}.

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent