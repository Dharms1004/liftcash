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
        $freefireId = $request->input('freefireId') ?? null;
        $check_token = User::where('API_TOKEN', $token)->select('USER_ID')->first();

        $userBalance = $this->getUserBalance($check_token->USER_ID); /** get user's current balance */

        /**calculate user deductable balance from all balances */

        $amountToBeDedictInMain = $coinsToBeRedeem/2;
        $amountToBeDedictInPromo = $coinsToBeRedeem/2;

        if($userBalance->BALANCE >= $coinsToBeRedeem && $userBalance->MAIN_BALANCE >= $amountToBeDedictInMain && $userBalance->PROMO_BALANCE >= $amountToBeDedictInPromo){

            $currentTotBalance = $userBalance->BALANCE;
            $currentMainBalance = $userBalance->MAIN_BALANCE - $amountToBeDedictInMain;
            $currentPromoBalance = $userBalance->PROMO_BALANCE - $amountToBeDedictInPromo;
            $closingTotBalance = $currentTotBalance - $coinsToBeRedeem;

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
                        "FREEFIRE_ID" => $freefireId,
                        "CURRENT_TOT_BALANCE" => $currentTotBalance,
                        "CLOSING_TOT_BALANCE" => $closingTotBalance,
                        "TRANSACTION_DATE" => $currentDate
                    ];

                    $this->creditOrDebitCoinsToUser($transData);

                    $this->updateUserFinalBalance($currentMainBalance, $currentPromoBalance, $closingTotBalance, $check_token->USER_ID);

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

        }else {
            $res['status'] = false;
            $res['message'] = 'Failed';
            $res['type'] = 'insufficient_balance_in_user_account';
            return response($res);
        }
    }

    public function transferDiamond(Request $request){

        $rules = [
            'userId' => 'required|max:10',
            'versionName' => 'required',
            'versionCode' => 'required',
            'api_token' => 'required',
            'diamondsToBeConvert' => 'required'
        ];
        $customMessages = [
            'required' => 'Please fill required :attribute'
        ];
        $this->validate($request, $rules, $customMessages);
        $userId = $request->input('userId');
        $token = $request->input('api_token');
        $diamondsToBeConvert = $request->input('diamondsToBeConvert');

        if ($diamondsToBeConvert < 20) {
            $res['status'] = false;
            $res['message'] = 'Minimum transfer amount is 20.';
            $res['type'] = 'filed';
            return response($res);
        }

        $check_token = User::where('API_TOKEN', $token)->select('USER_ID')->first();
        if(!$check_token){
            $res['status'] = false;
            $res['message'] = 'Invalid request';
            $res['type'] = 'failed';
            return response($res);
        }
        date_default_timezone_set('Asia/Kolkata');
        $currentDate = date('Y-m-d H:i:s');

        $internalRefNo = "111" . $check_token->USER_ID;
        $internalRefNo = $internalRefNo . mt_rand(100, 999);
        $internalRefNo = $internalRefNo . $this->getDateTimeInMicroseconds();
        $internalRefNo = $internalRefNo . mt_rand(100, 999);

        $userBalance = $this->getUserBalance($check_token->USER_ID); /** get user's current balance */
        $userDiamond = $this->getUserDiamondBalance($check_token->USER_ID); /** get user's Diamond balance */

        if ($userDiamond->BALANCE >= $diamondsToBeConvert) {
            
            try {
                $currentTotBalanceDiamond = $userDiamond->BALANCE;
                $currentMainBalanceDiamond = $userDiamond->MAIN_BALANCE - $diamondsToBeConvert;
                $currentPromoBalanceDiamond = $userDiamond->PROMO_BALANCE;
                $closingTotBalanceDiamond = $currentTotBalanceDiamond - $diamondsToBeConvert;
    
                $diamondTransData = [
                    "USER_ID" =>  $check_token->USER_ID,
                    "BALANCE_TYPE_ID" => 1,
                    "TRANSACTION_STATUS_ID" => 10, /** diamond success */
                    "TRANSACTION_TYPE_ID" => 11, /** diamond debit request */
                    "PAYOUT_COIN" => $diamondsToBeConvert,
                    "PAYOUT_EMIAL" => "",
                    "PAY_MODE" => "",
                    "INTERNAL_REFERENCE_NO" => $internalRefNo,
                    "PAYOUT_NUMBER" => "",
                    "FREEFIRE_ID" => "",
                    "CURRENT_TOT_BALANCE" => $currentTotBalanceDiamond,
                    "CLOSING_TOT_BALANCE" => $closingTotBalanceDiamond,
                    "TRANSACTION_DATE" => $currentDate
                ];
        
                $this->creditOrDebitDiamondToUser($diamondTransData);
        
                $this->updateUserDiamondMain($currentMainBalanceDiamond, $currentPromoBalanceDiamond, $closingTotBalanceDiamond, $check_token->USER_ID);
        
                $coinsToBecredit = $diamondsToBeConvert * 200;
        
                $currentTotBalance = $userBalance->BALANCE;
                $currentMainBalance = $userBalance->MAIN_BALANCE + $coinsToBecredit;
                $currentPromoBalance = $userBalance->PROMO_BALANCE;
                $closingTotBalance = $currentMainBalance + $currentPromoBalance;
        
                $coinTransData = [
                    "USER_ID" => $check_token->USER_ID,
                    "BALANCE_TYPE_ID" => 1,
                    "TRANSACTION_STATUS_ID" => 1, /** Coins credited success*/
                    "TRANSACTION_TYPE_ID" => 12, /** coins credited from diamond */
                    "PAYOUT_COIN" => $coinsToBecredit,
                    "PAYOUT_EMIAL" => "",
                    "PAY_MODE" => "",
                    "INTERNAL_REFERENCE_NO" => $internalRefNo,
                    "PAYOUT_NUMBER" => "",
                    "FREEFIRE_ID" => "",
                    "CURRENT_TOT_BALANCE" => $currentTotBalance,
                    "CLOSING_TOT_BALANCE" => $closingTotBalance,
                    "TRANSACTION_DATE" => $currentDate
                ];
        
                $this->creditOrDebitCoinsToUser($coinTransData);
        
                $this->updateUserFinalBalance($currentMainBalance, $currentPromoBalance, $closingTotBalance, $check_token->USER_ID);

                $res['status'] = '200';
                $res['message'] = 'Diamond Transferred to Coins.';
                $res['type'] = 'success';
                return response($res);

            } catch (\Throwable $th) {
                $res['status'] = false;
                $res['message'] = $th;
                $res['type'] = 'failed';
                return response($res);
            }
        }else {
            $res['status'] = false;
            $res['message'] = 'Failed';
            $res['type'] = 'insufficient_balance_in_user_account';
            return response($res);
        }
    }
}
