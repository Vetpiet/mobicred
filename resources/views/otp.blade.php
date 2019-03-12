<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>mobiCred</title>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.css" />
        <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.js"></script>
    </head>
    <body>
        <div class="container">
            <form class="ui form" method="get" action="/auth">
                <div class="ui label">One Time PIN (OTP):</div>
                <input type="hidden" name="cMCReference" value="{{ $cMCReference }}"/>
                <input type="hidden" name="pf_returned_id" value="{{ $pf_returned_id }}"
                <div class="ui input"><input type="text" name="iOTP"/></div>
                <a href="/resend?mercReqId={{ $cMCReference }}&id={{ $pf_returned_id }}" class="ui button">Resend</a>
                <button type="submit" class="ui button">Submit</button>
            </form>
        </div>
    </body>
</html>