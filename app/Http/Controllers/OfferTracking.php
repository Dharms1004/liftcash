<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\UserWallet;
use App\Models\OfferClicked;
use App\Traits\common_methods;
use DB;

class OfferTracking extends Controller
{

    use common_methods;

    public function index(Request $request)
    {
        $rules = [
            'click_id' => 'required'
        ];

        $customMessages = [
            'required' => 'Please fill required :attribute'
        ];

        $this->validate($request, $rules, $customMessages);

        $offerWebhookResponse = $request->input('click_id');

        $webhookData = \explode("_", $offerWebhookResponse);

        $userId = $webhookData[0];
        $offerId  = $webhookData[1];
        $clickId = $webhookData[2];

        $offerAmount = DB::table('offer')->select('OFFER_AMOUNT')->where(['OFFER_ID' => $offerId])->first(); /**get offer details by offerId */

        $userBalance = $this->getUserBalance($userId); /** get user's current balance */

        $currentTotBalance = $userBalance->BALANCE;
        $closingTotBalance = $currentTotBalance + $offerAmount->OFFER_AMOUNT;

        date_default_timezone_set('Asia/Kolkata');
		$currentDate = date('Y-m-d H:i:s');

        $internalRefNo = "111" . $userId;
		$internalRefNo = $internalRefNo . mt_rand(100, 999);
		$internalRefNo = $internalRefNo . $this->getDateTimeInMicroseconds();
		$internalRefNo = $internalRefNo . mt_rand(100, 999);

        try{
            $transData = [
                "USER_ID" => $userId,
                "BALANCE_TYPE_ID" => 1,
                "TRANSACTION_STATUS_ID" => 1, /** for coins credited succesfully */
                "TRANSACTION_TYPE_ID" => 8, /** for coins credited from offer completion */
                "PAYOUT_COIN" => $offerAmount->OFFER_AMOUNT,
                "PAYOUT_EMIAL" => "",
                "PAY_MODE" => "",
                "INTERNAL_REFERENCE_NO" => $internalRefNo,
                "PAYOUT_NUMBER" => "",
                "CURRENT_TOT_BALANCE" => $currentTotBalance,
                "CLOSING_TOT_BALANCE" => $closingTotBalance,
                "TRANSACTION_DATE" => $currentDate
            ];

            $userNewBalance = $userBalance->BALANCE + $offerAmount->OFFER_AMOUNT;
            $userMainBalance = $userBalance->MAIN_BALANCE + $offerAmount->OFFER_AMOUNT;

            $this->creditOrDebitCoinsToUser($transData);
            $this->updateUserBalanceMain($userNewBalance, $userMainBalance ,$userId);

            $offerClicked = OfferClicked::create([
                'USER_ID' => $userId,
                'OFFER_ID' => $offerId,
                'CLICK_ID'=> $clickId
            ]);

            $res['status'] = '200';
            $res['message'] = 'Success';
            $res['type'] = 'Offer Completed';
            return response($res);

        }catch(\Illuminate\Database\QueryException $e){
            $res['status'] = false;
            $res['message'] = $e;
            $res['type'] = 'some_error_occured';
            return response($res);
        }
    }
}
