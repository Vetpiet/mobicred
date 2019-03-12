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
            <a href="/checkstatus" class="ui button">Status</a>
            <a href="/getrefund" class="ui button">Refund</a>
        </div>
        <div class="container">
            <form class="ui form" method="get" action="/process">
                <div class="ui label">User login (email address):</div>
                <div class="ui input"><input type="text" name="cCustUsername"/></div>
                <div class="ui label">User password:</div>
                <div class="ui input"><input type="password" name="cCustPasswd"/></div>
                <div class="ui label">PF Intruction No:</div>
                <div class="ui input"><input type="number" name="cMerchantRequestID"/></div>
                <div class="ui label">Amount:</div>
                <div class="ui input"><input type="number" name="dAmount"/></div>
                <button type="submit" class="ui button">Submit</button>
            </form>
        </div>
    </body>
</html>
