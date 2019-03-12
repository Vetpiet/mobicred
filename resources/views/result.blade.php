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
        	<table class="ui celled table">
        		<thead>
    				<tr>
    					<th>Key</th>
    					<th>Value</th>
  					</tr>
  				</thead>
  				<tbody>
  					<tr><td>DB ID</td><td>{{  $id }}</td></tr>
  					<tr><td>Merchant ID</td><td>{{  $merc_id }}</td></tr>
  					<tr><td>Payfast Instruction No</td><td>{{  $pf_instr_id }}</td></tr>
  					<tr><td>Payfast Order No</td><td>{{  $pf_ord_no }}</td></tr>
  					<tr><td>Transaction Amount</td><td>{{  $amount }}</td></tr>
  					<tr><td>mobiCred Reference No</td><td>{{  $mcr_ref_no }}</td></tr>
  					<tr><td>mobiCred Response Code</td><td>{{  $mcr_resp_code }}</td></tr>
  					<tr><td>mobiCred Status</td><td>{{  $mcr_status }}</td></tr>
  					<tr><td>mobiCred Response</td><td>{{  $mcr_response }}</td></tr>
  				</tbody>
        	</table>
            <a href="/" class="ui button">Home</a>
        </div>
    </body>
</html>