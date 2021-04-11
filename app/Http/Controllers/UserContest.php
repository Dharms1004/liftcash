<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Traits\common_methods;
use DB;

class UserContest extends Controller
{
    use common_methods;
    
    public function spinContest(Request $request)
    {
        $rules = [
            'userId' => 'required|max:10',
            'versionName' => 'required|max:100',
            'versionCode' => 'required|max:100',
            'api_token' => 'required|max:100',
            'coinsToBeCredit' => 'required'

        ];
        $customMessages = [
            'required' => 'Please fill required :attribute'
        ];

        $this->validate($request, $rules, $customMessages);
        $token = $request->input('api_token');
        $coinsToBeCredit = $request->input('coinsToBeCredit');
        $check_token = User::where('API_TOKEN', $token)->select('USER_ID')->first();

        $userBalance = $this->getUserBalance($check_token->USER_ID); /** get user's current balance */
        $getSpinLimit = $this->getSpinLimit($check_token->USER_ID); /** get user's spin count balance */

        $spinLimit = env('SPIN_LIMIT');

        $openingClosingBalace = $this->getOpeningClosingBalace($check_token->USER_ID); /** get opening and closing balance */
        
        $oldClosingBalance = $openingClosingBalace->CLOSING_TOT_BALANCE ?? 0;
        $oldCurrentBalance = $openingClosingBalace->CURRENT_TOT_BALANCE ?? 0;
        
        $newCurrentBalance = $oldCurrentBalance + $coinsToBeCredit;
        $newClosingBalance = $oldCurrentBalance;

        date_default_timezone_set('Asia/Kolkata');
		$currentDate = date('Y-m-d H:i:s');

        $internalRefNo = "111" . $check_token->USER_ID;
		$internalRefNo = $internalRefNo . mt_rand(100, 999);
		$internalRefNo = $internalRefNo . $this->getDateTimeInMicroseconds();
		$internalRefNo = $internalRefNo . mt_rand(100, 999);

        if($getSpinLimit <= $spinLimit){

            try{
                $transData = [
                    "USER_ID" => $check_token->USER_ID,
                    "BALANCE_TYPE_ID" => 1,
                    "TRANSACTION_STATUS_ID" => 1, /** for coins credited succesfully */
                    "TRANSACTION_TYPE_ID" => 1, /** for coins credited from spin wheel */
                    "PAYOUT_COIN" => $coinsToBeCredit,
                    "PAYOUT_EMIAL" => "",
                    "PAY_MODE" => "",
                    "INTERNAL_REFERENCE_NO" => $internalRefNo,
                    "PAYOUT_NUMBER" => "",
                    "CURRENT_TOT_BALANCE" => $newCurrentBalance,
                    "CLOSING_TOT_BALANCE" => $newClosingBalance,
                    "TRANSACTION_DATE" => $currentDate
                ];

                $userNewBalance = $userBalance->BALANCE + $coinsToBeCredit;

                $this->creditOrDebitCoinsToUser($transData);
                $this->updateUserBalance($userNewBalance, $check_token->USER_ID);

                $res['status'] = '200';
                $res['message'] = 'Success';
                $res['type'] = 'Spin_wheel_success';
                $res['totalAttempts'] = $getSpinLimit + 1;
                return response($res);

            }catch(\Illuminate\Database\QueryException $e){
                $res['status'] = false;
                $res['message'] = $e;
                $res['type'] = 'some_error_occured';
                $res['totalAttempts'] = $getSpinLimit;
                return response($res);
            }

        }else{
            $res['status'] = false;
            $res['message'] = 'Failed';
            $res['type'] = 'spin_limit_exhausted';
            $res['totalAttempts'] = $getSpinLimit;
            return response($res);
        }

    }


    public function scratchCard(Request $request)
    {
        $rules = [
            'userId' => 'required|max:10',
            'versionName' => 'required|max:100',
            'versionCode' => 'required|max:100',
            'api_token' => 'required|max:100',
            'coinsToBeCredit' => 'required'

        ];
        $customMessages = [
            'required' => 'Please fill required :attribute'
        ];

        $this->validate($request, $rules, $customMessages);
        $token = $request->input('api_token');
        $coinsToBeCredit = $request->input('coinsToBeCredit');
        $check_token = User::where('API_TOKEN', $token)->select('USER_ID')->first();

        $userBalance = $this->getUserBalance($check_token->USER_ID); /** get user's current balance */
        $getSpinLimit = $this->getScratchCardLimit($check_token->USER_ID); /** get user's spin count balance */

        $spinLimit = env('SPIN_LIMIT');

        $openingClosingBalace = $this->getOpeningClosingBalace($check_token->USER_ID); /** get opening and closing balance */
        
        $oldClosingBalance = $openingClosingBalace->CLOSING_TOT_BALANCE ?? 0;
        $oldCurrentBalance = $openingClosingBalace->CURRENT_TOT_BALANCE ?? 0;
        
        $newCurrentBalance = $oldCurrentBalance + $coinsToBeCredit;
        $newClosingBalance = $oldCurrentBalance;

        date_default_timezone_set('Asia/Kolkata');
		$currentDate = date('Y-m-d H:i:s');

        $internalRefNo = "111" . $check_token->USER_ID;
		$internalRefNo = $internalRefNo . mt_rand(100, 999);
		$internalRefNo = $internalRefNo . $this->getDateTimeInMicroseconds();
		$internalRefNo = $internalRefNo . mt_rand(100, 999);

        if($getSpinLimit <= $spinLimit){

            try{
                $transData = [
                    "USER_ID" => $check_token->USER_ID,
                    "BALANCE_TYPE_ID" => 1,
                    "TRANSACTION_STATUS_ID" => 1, /** for coins credited succesfully */
                    "TRANSACTION_TYPE_ID" => 2, /** for coins credited from spin wheel */
                    "PAYOUT_COIN" => $coinsToBeCredit,
                    "PAYOUT_EMIAL" => "",
                    "PAY_MODE" => "",
                    "INTERNAL_REFERENCE_NO" => $internalRefNo,
                    "PAYOUT_NUMBER" => "",
                    "CURRENT_TOT_BALANCE" => $newCurrentBalance,
                    "CLOSING_TOT_BALANCE" => $newClosingBalance,
                    "TRANSACTION_DATE" => $currentDate
                ];

                $userNewBalance = $userBalance->BALANCE + $coinsToBeCredit;

                $this->creditOrDebitCoinsToUser($transData);
                $this->updateUserBalance($userNewBalance, $check_token->USER_ID);

                $res['status'] = '200';
                $res['message'] = 'Success';
                $res['type'] = 'Scratch_card_success';
                $res['totalAttempts'] = $getSpinLimit + 1;
                return response($res);

            }catch(\Illuminate\Database\QueryException $e){
                $res['status'] = false;
                $res['message'] = $e;
                $res['type'] = 'some_error_occured';
                $res['totalAttempts'] = $getSpinLimit;
                return response($res);
            }

        }else{
            $res['status'] = false;
            $res['message'] = 'Failed';
            $res['type'] = 'scratch_card_limit_exhausted';
            $res['totalAttempts'] = $getSpinLimit;
            return response($res);
        }
    }

    public function getSpinLimit($userId){

        $startTime = date('Y-m-d 00:00:00');
        $endTime = date('Y-m-d 23:59:59');

        $spinTransactionTypeId = 1;
        $spinTransactionStatusSuccess = 1;

       return DB::table('master_transaction_history')
            ->where(["USER_ID" => $userId, "TRANSACTION_TYPE_ID" => $spinTransactionTypeId, "TRANSACTION_STATUS_ID" => $spinTransactionStatusSuccess])
            ->whereBetween('TRANSACTION_DATE',[$startTime, $endTime])
            ->count();
    }

    public function getScratchCardLimit($userId){

        $startTime = date('Y-m-d 00:00:00');
        $endTime = date('Y-m-d 23:59:59');

        $spinTransactionTypeId = 2;
        $spinTransactionStatusSuccess = 1;

       return DB::table('master_transaction_history')
            ->where(["USER_ID" => $userId, "TRANSACTION_TYPE_ID" => $spinTransactionTypeId, "TRANSACTION_STATUS_ID" => $spinTransactionStatusSuccess])
            ->whereBetween('TRANSACTION_DATE',[$startTime, $endTime])
            ->count();
    }
}