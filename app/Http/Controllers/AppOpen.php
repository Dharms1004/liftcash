<?php

namespace App\Http\Controllers;

use App\Models\MasterTransactionHistory;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\UserWallet;
use App\Models\UserMapping;
use DB;
use App\Traits\common_methods;
use Illuminate\Support\Facades\DB as FacadesDB;

class AppOpen extends Controller
{
    use common_methods;

    public function getUserAppOpen(Request $request)
    {
        $rules = [
            'userId' => 'required|max:10',
            'versionName' => 'required|max:100',
            'versionCode' => 'required|max:100',
            'api_token' => 'required|max:100'
        ];

        $currentAppVer = env('APP_VERSION_CODE');

        $forceUpdate = $currentAppVer > $request->versionName ?  true :  false;

        $customMessages = [
            'required' => 'Please fill required :attribute'
        ];

        $this->validate($request, $rules, $customMessages);
        $userId = $request->input('userId');
        $token  = $request->input('api_token');

        $userBalance = DB::table('users')->join('user_wallet', 'users.USER_ID', '=', 'user_wallet.USER_ID')->select('users.USER_ID', 'users.REFFER_CODE', 'users.REFFER_ID', 'users.CREATED_AT', 'user_wallet.BALANCE as userCoin', 'user_wallet.PROMO_BALANCE as userPromoCoin',  'user_wallet.MAIN_BALANCE as userMainCoin')->where(['users.USER_ID' => $userId, 'user_wallet.COIN_TYPE' => 1])->first();
        $userDiamond = DB::table('user_wallet')->select('BALANCE as userDiamond')->where(['USER_ID' => $userId, 'COIN_TYPE' => 2])->first();
        /**check user consistence and credit bonus to both user and refferer */
        $popData = DB::table('headings')->select('HEADING', 'MESSAGE', 'THUMBNAIL', 'ACTION_URL', 'IS_BUTTON', 'STATUS')->where(['STATUS' => 1])->first();
        $popArray = array();
        
        if($popData){
            $popArray = [
                'heading' => $popData->HEADING,
                'message' => $popData->MESSAGE,
                'image' => env('POPUP_URL').$popData->THUMBNAIL,
                'url' => $popData->ACTION_URL,
                'is_button' => $popData->IS_BUTTON == 1 ? true : false
            ];
        }
        
        $this->creditBonusToReffererAndUser($userBalance);

        if ($userBalance) {
            $res['status'] = '200';
            $res['message'] = 'Success';
            $res['userId'] = $userId;
            $res['forceUpdate'] = $forceUpdate;
            $res['currency'] = '₹';
            $res['userCoin'] = $userBalance->userCoin;
            $res['userPromoCoin'] = $userBalance->userPromoCoin;
            $res['userMainCoin'] = $userBalance->userMainCoin;
            $res['userDiamond'] = $userDiamond->userDiamond ?? 0;
            $res['popUp'] = env('HOME_PAGE_POPUP');
            $res['popData'] = $popArray;
            $res['type'] = 'app_open';
            return response($res, 200);
        } else {
            $res['status'] = false;
            $res['message'] = 'Failed';
            $res['type'] = 'app_open_failed';
            return response($res);
        }
    }

    public function creditBonusToReffererAndUser($user){

        /**check if signup bonus not availed */
        $signUpBonus = MasterTransactionHistory::select('*')->where(['TRANSACTION_TYPE_ID' => 4, 'TRANSACTION_STATUS_ID' => 1, 'USER_ID' => $user->USER_ID])->first();
        $reffererBonus = UserMapping::where(['REFERRER_USER_ID' => $user->REFFER_ID,'REFERRAL_USER_ID' => $user->USER_ID])->get();
        $reffererBonusCount = isset($reffererBonus->BONUS_STATUS) ? $reffererBonus->BONUS_STATUS : "";

        if(empty($signUpBonus)){
            $bonusAmount = env("SIGNUP_BONUS");

            $userBalance = $this->getUserBalance($user->USER_ID); /** get user's current balance */

            date_default_timezone_set('Asia/Kolkata');
            $currentDate = date('Y-m-d H:i:s');

            $internalRefNo = "111" . $user->USER_ID;
            $internalRefNo = $internalRefNo . mt_rand(100, 999);
            $internalRefNo = $internalRefNo . $this->getDateTimeInMicroseconds();
            $internalRefNo = $internalRefNo . mt_rand(100, 999);

            $currentTotBalance = $userBalance->BALANCE;
            $closingTotBalance = $currentTotBalance + $bonusAmount;

            $transData = [
                "USER_ID" => $user->USER_ID,
                "BALANCE_TYPE_ID" => 1,
                "TRANSACTION_STATUS_ID" => 1, /** for coins credited succesfully */
                "TRANSACTION_TYPE_ID" => 4, /** for coins credited For SignUp Bonus */
                "PAYOUT_COIN" => $bonusAmount,
                "PAYOUT_EMIAL" => "",
                "PAY_MODE" => "",
                "INTERNAL_REFERENCE_NO" => $internalRefNo,
                "PAYOUT_NUMBER" => "",
                "CURRENT_TOT_BALANCE" => $currentTotBalance,
                "CLOSING_TOT_BALANCE" => $closingTotBalance,
                "TRANSACTION_DATE" => $currentDate
            ];

            $this->creditOrDebitCoinsToUser($transData);

            $userNewBalance = $userBalance->BALANCE + $bonusAmount;
            $userNewPromoBalance = $userBalance->PROMO_BALANCE + $bonusAmount;

            $this->updateUserBalance($userNewBalance, $userNewPromoBalance, $user->USER_ID);


            /**credit refferal bonus */
            if(!empty($user->REFFER_ID && $reffererBonusCount == 0)){
                $date1 = new \DateTime($user->CREATED_AT);
                $date2 = new \DateTime(date('Y-m-d h:m:s'));

                $diff = $date2->diff($date1);
                $hours = $diff->h;
                $hours = $hours + ($diff->days*24);

                if($hours >= 24){

                    $userBalance = $this->getUserBalance($user->REFFER_ID); /** get user's current balance */

                    $bonusAmount = env('REFFERAL_BONUS'); /**get bonus amount to be credit */

                    /**calculate opening closing balance */
                    $currentTotBalance = $userBalance->BALANCE;
                    $closingTotBalance = $currentTotBalance + $bonusAmount;

                    date_default_timezone_set('Asia/Kolkata');
                    $currentDate = date('Y-m-d H:i:s');

                    $internalRefNo = "111" . $user->REFFER_ID;
                    $internalRefNo = $internalRefNo . mt_rand(100, 999);
                    $internalRefNo = $internalRefNo . $this->getDateTimeInMicroseconds();
                    $internalRefNo = $internalRefNo . mt_rand(100, 999);

                        $transData = [
                            "USER_ID" => $user->REFFER_ID,
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
                    $this->updateUserBalance($userNewBalance, $userNewPromoBalance, $user->REFFER_ID);

                    UserMapping::where(['REFERRER_USER_ID' => $user->REFFER_ID,'REFERRAL_USER_ID' => $user->USER_ID])->update(['BONUS_STATUS' => 1]);


                }
            }
        }
    }
}
