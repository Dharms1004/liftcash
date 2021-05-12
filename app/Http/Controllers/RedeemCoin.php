<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Traits\common_methods;
use DB;

class RedeemCoin extends Controller
{
    use common_methods;

    public function withdrawAmount(Request $request)
    {
        $rules = [
            'userId' => 'required|max:10',
            'versionName' => 'required|max:100',
            'versionCode' => 'required|max:100',
            'api_token' => 'required|max:100',
            'paymentMode' => 'required',
            'payoutEmail' => 'required',
            'payoutNumber' => 'required',
            'coinsToBeRedeem' => 'required|max:100'
        ];
        $customMessages = [
            'required' => 'Please fill required :attribute'
        ];
        $this->validate($request, $rules, $customMessages);
        $userId = $request->input('userId');
        $token = $request->input('api_token');
        $coinsToBeRedeem = $request->input('coinsToBeRedeem');
        $paymentMode = $request->input('paymentMode');
        $payoutEmail = $request->input('payoutEmail');
        $payoutNumber = $request->input('payoutNumber');
        $check_token = User::where('API_TOKEN', $token)->select('USER_ID')->first();

        $userBalance = $this->getUserBalance($check_token->USER_ID); /** get user's current balance */

        $currentTotBalance = $userBalance->BALANCE;
        $closingTotBalance = $currentTotBalance - $coinsToBeCredit;
        

        date_default_timezone_set('Asia/Kolkata');
		$currentDate = date('Y-m-d H:i:s');

        $internalRefNo = "111" . $check_token->USER_ID;
		$internalRefNo = $internalRefNo . mt_rand(100, 999);
		$internalRefNo = $internalRefNo . $this->getDateTimeInMicroseconds();
		$internalRefNo = $internalRefNo . mt_rand(100, 999);

        if ($userBalance->BALANCE >= $coinsToBeRedeem) {
            try{
                $transData = [
                    "USER_ID" => $check_token->USER_ID,
                    "BALANCE_TYPE_ID" => 1,
                    "TRANSACTION_STATUS_ID" => 6, /** withdraw pending */
                    "TRANSACTION_TYPE_ID" => 6, /** withdraw request */
                    "PAYOUT_COIN" => $coinsToBeRedeem,
                    "PAYOUT_EMIAL" => $payoutEmail,
                    "PAY_MODE" => $paymentMode,
                    "INTERNAL_REFERENCE_NO" => $internalRefNo,
                    "PAYOUT_NUMBER" => $payoutNumber,
                    "CURRENT_TOT_BALANCE" => $currentTotBalance,
                    "CLOSING_TOT_BALANCE" => $closingTotBalance,
                    "TRANSACTION_DATE" => $currentDate
                ];

                $userNewBalance = $userBalance->BALANCE - $coinsToBeRedeem;

                $this->creditOrDebitCoinsToUser($transData);
                $this->updateUserBalance($userNewBalance, $check_token->USER_ID);

                $res['status'] = '200';
                $res['message'] = 'Success';
                $res['type'] = 'withdraw_success';
                return response($res);

            }catch(\Illuminate\Database\QueryException $e){
                $res['status'] = false;
                $res['message'] = $e;
                $res['type'] = 'some_error_occured';
                return response($res);
            }
        } else {
            $res['status'] = false;
            $res['message'] = 'Failed';
            $res['type'] = 'user_transaction_failed';
            return response($res);
        }
    }
}
