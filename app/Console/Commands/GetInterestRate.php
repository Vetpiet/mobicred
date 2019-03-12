<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GetInterestRate extends Command
{
    protected $name = 'interestrate:get';

    protected $description = 'Daily check of current Interest Rate charged by Mobicred';


    // public function __construct()
    // {
    //     parent::__construct();
    // }



    // protected function getOptions()
    // {
    //     $returnedParams = [
    //         "testUrl" => "https://test.mobicred.co.za/cgi-bin/wspd_113.sh/WService=wsb_mcrtst/rest.w",
    //         "merchantId" => 2772,
    //         "merchantKey" => 1411342727,
    //         "merchantUsername" => "Payfasttest",
    //         "merchantPassword" => "Mobicred123",
    //         "minAmount" => 1,
    //         "maxAmount" => 20000,
    //     ];

    //     return $returnedParams;
    // }





    protected function guzzleIt($params)
    {
        $client = new \GuzzleHttp\Client();

        // $params["rqDataMode"] = "VAR/JSON";
        // $params["rqAuthentication"] = "user:" . $options["merchantUsername"] . "|" . $options["merchantPassword"] . "|GSMUS|";
        // $params["cMerchantID"] = $options["merchantId"];
        // $params["cMerchantKey"] = $options["merchantKey"];

        $params["rqDataMode"] = "VAR/JSON";
        $params["rqAuthentication"] = "user:Payfasttest|Mobicred123|GSMUS|";
        $params["cMerchantID"] = 2772;
        $params["cMerchantKey"] = 1411342727;

        $res = $client->request('POST', "https://test.mobicred.co.za/cgi-bin/wspd_113.sh/WService=wsb_mcrtst/rest.w", [
            'form_params' => $params,
        ]);

        if ($res->getStatusCode() === 200) {
            return $res->getBody()->getContents();
        } else {
            return false;
        }
    }




    public function handle()
    {
        $postFields = [
            "rqDataMode" => "VAR/JSON",
            "cMerchantID" => 2772,
            "cMerchantKey" => 1411342727,
            "rqservice" => "ilDataService:getCurrentIntRate"
        ];

        $response = $this->guzzleIt($postFields);

        $response = json_decode($response, true);

        $response = $response["rqResponse"];

        $now = date('Y-m-d H:i:s');

        if (Cache::has('mcr_interestrate') === false) {
            if ($response["pcStatus"] === "Success") {
                Cache::put('mcr_interestrate', $response["pcIntRate"], 1440);
                Cache::put('mcr_interestrate_last_update', $now, 1440);
                Log::info("GetInterestRate scheduled task completed. Interest Rate set at " . $response["pcIntRate"]);
            } else {
                Log::error("GetInterestRate scheduled task failed - " . $response["rqWarningMessage"]);
            }
        } else {
            Log::info("GetInterestRate scheduled task not required. Interest Rate is still " . $response["pcIntRate"]);
        }
    }
}
