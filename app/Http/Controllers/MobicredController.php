<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MobicredController extends Controller
{
	private function getOptions()
	{
		$returnedParams = [
			"testUrl" => "https://test.mobicred.co.za/cgi-bin/wspd_113.sh/WService=wsb_mcrtst/rest.w",
			"merchantId" => 2772,
			"merchantKey" => 1411342727,
			"merchantUsername" => "Payfasttest",
			"merchantPassword" => "Mobicred123",
			"minAmount" => 1,
			"maxAmount" => 20000
		];

		return $returnedParams;
	}




    private function guzzleIt($params)
    {
    	$options = $this->getOptions();

    	$client = new \GuzzleHttp\Client();

    	$params["rqDataMode"] = "VAR/JSON";
    	$params["rqAuthentication"] = "user:" . $options["merchantUsername"] . "|" . $options["merchantPassword"] . "|GSMUS|";
    	$params["cMerchantID"] = $options["merchantId"];
    	$params["cMerchantKey"] = $options["merchantKey"];

		$res = $client->request('POST', $options["testUrl"], [
    		'form_params' => $params
		]);

		if ($res->getStatusCode() === 200) {
			return $res->getBody()->getContents();
		} else {
			return false;
		}
    }




    private function processResponses($params)
    {
    	if (empty($params)) {
    		Log::error('Empty Params Received');
    		return false;
    	} else {
    		if($params["piResponseCode"] > 100) {
    			return $params["piResponseCode"] . ' - ' . $params["pcReason"] . ' (' . $params["pcStatus"] . ')';
    		} else {
    			return $params;
    		}
    	}
    }





    public function process()
    {
    	Log::info(__METHOD__ . " Start");

    	$options = $this->getOptions();

    	$pfOrdNo = "mcr" . uniqid();

    	$sendFields = [
    		"cCustUsername" => request()->get("cCustUsername"),
    		"cCustPasswd" => request()->get("cCustPasswd"),
    		"cMerchantRequestID" => request()->get("cMerchantRequestID"),
    		"dAmount" => request()->get("dAmount"),
    		"rqservice" =>"ilDataService:purCreate",
    		"lAutoApprove" => "True",
    		"cOrderNo" => $pfOrdNo

    	];

    	$result = json_decode($this->guzzleIt($sendFields), true);

    	$result = $result["rqResponse"];

    	if (!empty($result["pcMCReference"])) {
    		
    		$mcrDB = new \App\Mobicred;
    		$mcrDB->merc_id = $options["merchantId"];
    		$mcrDB->pf_instr_id = request()->get("cMerchantRequestID");
    		$mcrDB->pf_ord_no = $pfOrdNo;
    		$mcrDB->amount = request()->get("dAmount");
    		$mcrDB->mcr_ref_no = $result["pcMCReference"];
    		$mcrDB->mcr_resp_code = $result["piResponseCode"];
    		$mcrDB->mcr_status = $result["pcStatus"];
    		$mcrDB->mcr_repsonse = $result["pcReason"];

    		$mcrDB->save();

    		return view('otp', [
    			"cMCReference" => $result["pcMCReference"],
    			"pf_returned_id" => $mcrDB->id
    			]);
    	} else {
    		return $result;
    	}	
    }



    public function auth()
    {
    	Log::info(__METHOD__ . " Start");

    	$sendFields = [
    		"iOTP" => request()->get("iOTP"),
    		"cMCReference" => request()->get("cMCReference"),
    		"rqservice" =>"ilDataService:purPreAuth",
    	];

    	$result = json_decode($this->guzzleIt($sendFields), true);

    	$result = $result["rqResponse"];

    	$mcrDB = \App\Mobicred::find(request()->get("pf_returned_id"));
		$mcrDB->mcr_status = $result["pcStatus"];
		$mcrDB->mcr_repsonse = $result["pcReason"];

		$mcrDB->save();

		return view('result', $mcrDB);

    	return $result;
    }
}
