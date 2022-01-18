<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\UserWallet;
use App\Models\UserMapping;
use App\Traits\common_methods;

class UserController extends Controller
{
    use common_methods;

    public function register(Request $request)
    {
        //check if user is alredy exist check start
        $rules = [
            'socialEmail' => 'required|email'
        ];
        $customMessages = [
            'required' => 'Please fill email :attribute'
        ];
        $this->validate($request, $rules, $customMessages);
        $email    = $request->input('socialEmail');

        try {
            $login = User::where('SOCIAL_EMAIL', $email)->limit(1)->first();
            if (!empty($login) && $login->count() > 0) { //user already signup return token in response
                return $this->login($login);
            } else {
                //registration start
                $rules = [
                    'socialEmail' => 'required|email|unique:users,SOCIAL_EMAIL',
                    'deviceId' => 'required',
                    'socialType' => 'required',
                    'socialId' => 'required',
                    'versionName' => 'required',
                    'versionCode' => 'required',
                ];
                $customMessages = [
                    'required' => 'Please fill attribute :attribute'
                ];
                $this->validate($request, $rules, $customMessages);
                try {
                    $hasher = app()->make('hash');
                    $socialEmail = $request->input('socialEmail');
                    $socialName = $request->input('socialName');
                    $deviceType = $request->input('deviceType');
                    $deviceId = $request->input('deviceId');
                    $deviceName = $request->input('deviceName');
                    $socialType = $request->input('socialType');
                    $socialId = $request->input('socialId');
                    $socialImgurl = $request->input('socialImgurl');
                    $advertisingId = $request->input('advertisingId');
                    $versionName = $request->input('versionName');
                    $versionCode = $request->input('versionCode');
                    $utmSource = $request->input('utmSource');
                    $utmMedium = $request->input('utmMedium');
                    $utmTerm = $request->input('utmTerm');
                    $utmContent = $request->input('utmContent');
                    $utmCampaign = $request->input('utmCampaign');
                    $refferalCode = $request->input('refferal_code') ? $request->input('refferal_code') : null;

                    if($refferalCode){
                        $refferData = User::select('USER_ID')->where('REFFER_CODE', $refferalCode)->first();
                        $refferId = $refferData->USER_ID;
                    }else{
                        $refferId = null;
                    }

                    // $password = $hasher->make($request->input('password'));
                    $api_token = sha1($socialEmail . time());
                    $userCreate = User::create([
                        'SOCIAL_EMAIL' => $socialEmail,
                        'SOCIAL_NAME' => $socialName,
                        'DEVICE_TYPE' => $deviceType,
                        'DEVICE_ID' => $deviceId,
                        'DEVICE_NAME' => $deviceName,
                        'SOCIAL_TYPE' => $socialType,
                        'REFFER_ID' => $refferId,
                        'REFFER_CODE' => $this->generateRefferalCode(8),
                        'SOCIAL_ID' => $socialId,
                        'PROFILE_PIC' => $socialImgurl,
                        'ADVERTISING_ID' => $advertisingId,
                        'VERSION_NAME' => $versionName,
                        'VERSION_CODE' => $versionCode,
                        'API_TOKEN' => $api_token
                    ]);
                    if (!empty($userCreate->id)) {

                        /**create user wallet and credit bonus amount */
                        $this->createUserWallet($userCreate->id);

                        /**credit refferal bonus */
                        if(!empty($refferId)){
                            $this->mapReffererUser($refferId, $userCreate->id);
                        }

                        $res['status'] = '200';
                        $res['message'] = 'Success';
                        $res['userId'] = $userCreate->id;
                        $res['socialName'] = $userCreate->SOCIAL_NAME;
                        $res['socialImgurl'] = $userCreate->PROFILE_PIC;
                        $res['type'] = 'register';
                        $res['api_token'] = $userCreate->API_TOKEN;
                        return response($res, 200);
                    } else {
                        $res['status'] = '201';
                        $res['message'] = 'Failed';
                        return response($res, 201);
                    }
                } catch (\Illuminate\Database\QueryException $ex) {
                    $res['status'] = false;
                    $res['message'] = $ex->getMessage();
                    return response($res, 500);
                }
                //registration end
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            $res['success'] = false;
            $res['message'] = $ex->getMessage();
            return response($res, 500);
        }
        //check if user is alrady exist check end
    }
    private function login($login)
    { //if user already signup then logged in
        try {
            $api_token = sha1($login->socialEmail . time());
            $update_token = User::where('USER_ID', $login->USER_ID)->update(['API_TOKEN' => $api_token]);

            if ($update_token) {
                $res['status'] = '200';
                $res['message'] = 'Success';
                $res['userId'] = $login->USER_ID;
                $res['socialName'] = $login->SOCIAL_NAME;
                $res['socialImgurl'] = $login->PROFILE_PIC;
                $res['type'] = 'login';
                $res['api_token'] = $api_token;
                return response($res, 200);
            } else {
                $res['status'] = '201';
                $res['message'] = 'Login_Failed';
                return response($res, 200);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            $res['status'] = false;
            $res['message'] = $ex->getMessage();
            return response($res, 500);
        }
    }

    public function createUserWallet($userId)
    {

        $bonusAmount = 0;
        date_default_timezone_set('Asia/Kolkata');

        UserWallet::insert([
            'COIN_TYPE' => 1,
            'BALANCE_TYPE' => 1,
            'PROMO_BALANCE' => $bonusAmount,
            'MAIN_BALANCE' => 0,
            'BALANCE' => $bonusAmount,
            'CREATED_DATE' => date("Y-m-d h:i:s"),
            'USER_ID' => $userId
        ]);

        UserWallet::insert([
            'COIN_TYPE' => 2,
            'BALANCE_TYPE' => 2,
            'PROMO_BALANCE' => 0,
            'MAIN_BALANCE' => 0,
            'BALANCE' => 0,
            'CREATED_DATE' => date("Y-m-d h:i:s"),
            'USER_ID' => $userId
        ]);

		$currentDate = date('Y-m-d H:i:s');

        $internalRefNo = "111" . $userId;
		$internalRefNo = $internalRefNo . mt_rand(100, 999);
		$internalRefNo = $internalRefNo . $this->getDateTimeInMicroseconds();
		$internalRefNo = $internalRefNo . mt_rand(100, 999);

        $currentTotBalance = 0;
        $closingTotBalance = $bonusAmount;

        $transData = [
            "USER_ID" => $userId,
            "BALANCE_TYPE_ID" => 1,
            "TRANSACTION_STATUS_ID" => 1, /** for coins credited succesfully */
            "TRANSACTION_TYPE_ID" => 10, /** for wallet created */
            "PAYOUT_COIN" => $bonusAmount,
            "PAYOUT_EMIAL" => "",
            "PAY_MODE" => "",
            "INTERNAL_REFERENCE_NO" => $internalRefNo,
            "PAYOUT_NUMBER" => "",
            "CURRENT_TOT_BALANCE" => $currentTotBalance,
            "CLOSING_TOT_BALANCE" => $closingTotBalance,
            "TRANSACTION_DATE" => $currentDate
        ];

        $transDataDia = [
            "USER_ID" => $userId,
            "BALANCE_TYPE_ID" => 1,
            "TRANSACTION_STATUS_ID" => 1, /** for coins credited succesfully */
            "TRANSACTION_TYPE_ID" => 10, /** for wallet created */
            "PAYOUT_COIN" => 0,
            "PAYOUT_EMIAL" => "",
            "PAY_MODE" => "",
            "INTERNAL_REFERENCE_NO" => $internalRefNo,
            "PAYOUT_NUMBER" => "",
            "CURRENT_TOT_BALANCE" => 0,
            "CLOSING_TOT_BALANCE" => 0,
            "TRANSACTION_DATE" => $currentDate
        ];

        $this->creditOrDebitCoinsToUser($transData);
        $this->creditOrDebitDiamondToUser($transDataDia);
        
    }

    // This function will return a random
    // string of specified length
    public function generateRefferalCode($length_of_string)
    {
        // String of all alphanumeric character
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        // Shufle the $str_result and returns substring
        // of specified length
        return substr(str_shuffle($str_result),0, $length_of_string);
    }


    /**credit refferal bonus to user */

    public function creditRefferalBonusToUser($userId){

        $userBalance = $this->getUserBalance($userId); /** get user's current balance */

        $bonusAmount = env('REFFERAL_BONUS'); /**get bonus amount to be credit */

        /**calculate opening closing balance */
        $currentTotBalance = $userBalance->BALANCE;
        $closingTotBalance = $currentTotBalance + $bonusAmount;

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
                "TRANSACTION_TYPE_ID" => 7, /** for refferal bonus credit */
                "PAYOUT_COIN" => $bonusAmount,
                "PAYOUT_EMIAL" => "",
                "PAY_MODE" => "",
                "INTERNAL_REFERENCE_NO" => $internalRefNo,
                "PAYOUT_NUMBER" => "",
                "CURRENT_TOT_BALANCE" => $currentTotBalance,
                "CLOSING_TOT_BALANCE" => $closingTotBalance,
                "TRANSACTION_DATE" => $currentDate
            ];

            $userNewBalance = $userBalance->BALANCE + $bonusAmount;
            $userNewPromoBalance = $userBalance->PROMO_BALANCE + $bonusAmount;

            $this->creditOrDebitCoinsToUser($transData);
            $this->updateUserBalance($userNewBalance, $userNewPromoBalance, $userId);

           return true;

        }catch(\Illuminate\Database\QueryException $e){
           return false;
        }

    }

    public function mapReffererUser($refferId, $userId){

        date_default_timezone_set('Asia/Kolkata');
		$from = date('Y-m-d 00:00:00');
		$now = date('Y-m-d H:i:s');
        $todayRefferer = UserMapping::where(['REFERRER_USER_ID' => $refferId])->whereBetween('REGISTERED_DATE_TIME', [$from, $now])->get();

        if($todayRefferer->count() <= 10){

            $userMapping = UserMapping::create([
                'REFERRER_USER_ID' => $refferId,
                'REFERRAL_USER_ID' => $userId
            ]);
        }

    }


}
