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
            <form class="ui form" method="get" action="/status">
                <div class="ui label">mobiCred Reference No</div>
                <div class="ui input"><input type="text" name="cMCReference"/></div>
                <button type="submit" class="ui button">Submit</button>
            </form>
        </div>
        <a href="/" class="ui button">Home</a>
    </body>
</html>