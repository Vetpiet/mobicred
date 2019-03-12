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
            <form class="ui form" method="get" action="/refund">
                <div class="ui label">mobiCred Reference No</div>
                <div class="ui input"><input type="text" name="cMCReference"/></div>
                <div class="ui label">PayFast Instruction No</div>
                <div class="ui input"><input type="number" name="pfinst"/></div>
                <div class="ui label">Amount:</div>
                <div class="ui input"><input type="number" name="amount"/></div>
                <div class="ui label">Reason:</div>
                <div class="ui input"><input type="text" name="reason"/></div>
                <button type="submit" class="ui button">Submit</button>
            </form>
        </div>
        <a href="/" class="ui button">Home</a>
    </body>
</html>