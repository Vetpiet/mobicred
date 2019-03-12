<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

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
        $options = $this->getOptions([
            'verify' => base_path('cacert.pem'),
        ]);

        $client = new \GuzzleHttp\Client();

        $params["rqDataMode"] = "VAR/JSON";
        $params["rqAuthentication"] = "user:" . $options["merchantUsername"] . "|" . $options["merchantPassword"] . "|GSMUS|";
        $params["cMerchantID"] = $options["merchantId"];
        $params["cMerchantKey"] = $options["merchantKey"];

        $res = $client->request('POST', $options["testUrl"], [
            'form_params' => $params,
        ]);

        if ($res->getStatusCode() === 200) {
            return $res->getBody()->getContents();
        } else {
            return false;
        }
    }




    private function processResponse($params)
    {
        if (empty($params)) {
            Log::error('Empty Params Received');
            return false;
        } else {
            if (empty($params["piResponseCode"])) {
                Log::error('mobiCred:' . __METHOD__ . ':' . $params["rqErrorMessage"]);
                return false;
            } else {
                $respCode = $params["piResponseCode"];
                switch (true) {
                    case ($respCode < 100):
                        $tranStatus = ["proceed" => true, "status" => "Pre-Auth", "code" => $respCode];
                        Log::info('mobiCred:' . __METHOD__ . ":Status=Pre-Auth:ResponseCode=" . $respCode);
                        return true;
                        break;
                    case ($respCode > 100 && $respCode < 200):
                        $tranStatus = ["proceed" => true, "status" => "Approved", "code" => $respCode];
                        Log::info('mobiCred:' . __METHOD__ . ":Status=Approved:ResponseCode=" . $respCode);
                        return true;
                        break;
                    case ($respCode > 200 && $respCode < 300):
                        $tranStatus = ["proceed" => false, "status" => "User Account Error", "code" => $respCode];
                        Log::error('mobiCred:' . __METHOD__ . ":Status=User Account Error:ResponseCode=" . $respCode);
                        echo '  <div class="ui negative message">
                                    <i class="close icon"></i>
                                    <p>Error Response: ' . $params["pcReason"] . '</p>
                                </div>';
                        return false;
                        exit(1);
                        break;
                    case ($respCode > 300 && $respCode < 400):
                        $tranStatus = ["proceed" => false, "status" => "Merchant Account Error", "code" => $respCode];
                        Log::error('mobiCred:' . __METHOD__ . ":Status=Merchant Account Error:ResponseCode=" . $respCode);
                        echo '  <div class="ui negative message">
                                    <i class="close icon"></i>
                                    <p>Error Response: ' . $params["pcReason"] . '</p>
                                </div>';
                        return false;
                        exit(1);
                        break;
                    case ($respCode > 400):
                        $tranStatus = ["proceed" => false, "status" => "Transaction State Error", "code" => $respCode];
                        Log::error('mobiCred:' . __METHOD__ . ":Status=Transaction State Error:ResponseCode=" . $respCode);
                        echo '  <div class="ui negative message">
                                    <i class="close icon"></i>
                                    <p>Error Response: ' . $params["pcReason"] . '</p>
                                </div>';
                        return false;
                        exit(1);
                        break;
                }
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

        if ($this->processResponse($result) === true) {
            $mcrDB = new \App\Mobicred;
            $mcrDB->merc_id = $options["merchantId"];
            $mcrDB->pf_instr_id = request()->get("cMerchantRequestID");
            $mcrDB->pf_ord_no = $pfOrdNo;
            $mcrDB->transaction_type = "Purchase";
            $mcrDB->amount = request()->get("dAmount");
            $mcrDB->mcr_ref_no = $result["pcMCReference"];
            $mcrDB->mcr_resp_code = $result["piResponseCode"];
            $mcrDB->mcr_status = $result["pcStatus"];
            $mcrDB->mcr_response = $result["pcReason"];

            $mcrDB->save();

            return view('otp', [
                "cMCReference" => $result["pcMCReference"],
                "pf_returned_id" => $mcrDB->id
            ]);
        } else {
            exit(1);
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

        if ($this->processResponse($result) === true) {
            $mcrDB = \App\Mobicred::find(request()->get("pf_returned_id"));
            $mcrDB->mcr_status = $result["pcStatus"];
            $mcrDB->mcr_response = $result["pcReason"];

            $mcrDB->save();

            return view('result', $mcrDB);
        } else {
            exit(1);
        }
    }




    public function resendOTP()
    {
        Log::info(__METHOD__ . " Start");

        $sendFields = [
            "cMCReference" => request()->get("mercReqId"),
            "cMerchantRequestID" => "mcr" . uniqid(),
            "rqservice" =>"ilDataService:purOTP",
        ];

        $result = json_decode($this->guzzleIt($sendFields), true);

        $result = $result["rqResponse"];

        echo '<div class="ui visible message"><pre>' . $result["pcReason"] . '</pre></div>';

        Log::info(__METHOD__ . " OTP Resend Requested. " . $result["pcReason"]);

        return view('otp', [
                "cMCReference" => request()->get("mercReqId"),
                "pf_returned_id" => request()->get("id")
            ]);
    }





    public function status()
    {
        Log::info(__METHOD__ . " Start");

        $sendFields = [
            "cMCReference" => request()->get("cMCReference"),
            "rqservice" =>"ilDataService:purQuery",
        ];

        $result = json_decode($this->guzzleIt($sendFields), true);

        $result = $result["rqResponse"];

        echo '<div class="ui visible message"><pre>' . print_r($result, true) . '</pre></div>
        <a href="/" class="ui button">Home</a>';

        Log::info(__METHOD__ . " Status Requested. " . $result["pcReason"]);
    }




    public function refund()
    {
        Log::info(__METHOD__ . " Start");

        $options = $this->getOptions();

        $thisUniqId = "mcr" . uniqid();

        $sendFields = [
            "cMCReference" => request()->get("cMCReference"),
            "dAmount" => request()->get("amount"),
            "cMerchantReason" => request()->get("reason"),
            "cMerchantRequestID" => $thisUniqId,
            "rqservice" =>"ilDataService:purRefund",
        ];

        $result = json_decode($this->guzzleIt($sendFields), true);

        $result = $result["rqResponse"];

        if ($this->processResponse($result) === true) {
            $mcrDB = new \App\Mobicred;
            $mcrDB->merc_id = $options["merchantId"];
            $mcrDB->pf_instr_id = request()->get("pfinst");
            $mcrDB->pf_ord_no = $thisUniqId;
            $mcrDB->transaction_type = "Refund";
            $mcrDB->amount = request()->get("amount");
            $mcrDB->mcr_ref_no = $result["pcMCReference"];
            $mcrDB->original_mcr_ref_no = request()->get("cMCReference");
            $mcrDB->mcr_resp_code = $result["piResponseCode"];
            $mcrDB->pf_refund_reason = request()->get("reason");
            $mcrDB->mcr_status = $result["pcStatus"];
            $mcrDB->mcr_response = $result["pcReason"];

            $mcrDB->save();

            return view('result', $mcrDB);
        } else {
            exit(1);
        }
    }
}
