<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Promotions;
use App\Models\User;
use DB;
use App\Traits\common_methods;

class WalletData extends Controller
{
    use common_methods;

    public function getAllWalletData(Request $request)
    {
        $rules = [
            'userId' => 'required|max:10',
            'versionName' => 'required|max:100',
            'versionCode' => 'required|max:100',
            'api_token' => 'required|max:100',


        ];
        $customMessages = [
            'required' => 'Please fill required :attribute'
        ];
        $this->validate($request, $rules, $customMessages);
        $userId = $request->input('userId');
        $token = $request->input('api_token');
        $check_token = User::where('API_TOKEN', $token)->select('USER_ID')->first();
        $WalletData = $this->getUserBalance($userId);
        $userDiamond = DB::table('user_wallet')->select('BALANCE as userDiamond')->where(['USER_ID' => $userId, 'COIN_TYPE' => 2])->first();
        $payOutValues = env('PAYOUT_VALUES');
        $payOutValues = explode("|", $payOutValues);
        if (!empty($WalletData)) {
            $res['status'] = '200';
            $res['message'] = 'Success';
            $res['type'] = 'get_all_wallet_data';
            $res['paytmOpt'] = false;
            $res['paypalOpt'] = true;
            $res['threshold'] = $WalletData->BALANCE;
            $res['promoCoin'] = $WalletData->PROMO_BALANCE;
            $res['mainCoin'] = $WalletData->MAIN_BALANCE;
            $res['userDiamond'] = $userDiamond->userDiamond ?? 0;
            $res['payoutValues'] = $payOutValues;
            $res['thresholdValue'] = env('THRESHOLD_VALUES');
            return response($res, 200);
        } else {
            $res['status'] = false;
            $res['message'] = 'Failed';
            $res['type'] = 'get_all_wallet_data';
            return response($res);
        }
    }

}
