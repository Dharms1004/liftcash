<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\UserWallet;
use DB;

class AppOpen extends Controller
{
    public function getUserAppOpen(Request $request)
    {
        $rules = [
            'userId' => 'required|max:10',
            'versionName' => 'required|max:100',
            'versionCode' => 'required|max:100',

        ];
        $customMessages = [
            'required' => 'Please fill required :attribute'
        ];
        $this->validate($request, $rules, $customMessages);
        $userId = $request->input('userId');
        $userBalance = UserWallet::where(['USER_ID' => $userId])->select(
            DB::raw("MAX(CASE WHEN BALANCE_TYPE = 1 THEN BALANCE ELSE 0 END) AS userCoin"),
            DB::raw("MAX(CASE WHEN BALANCE_TYPE = 2 THEN BALANCE ELSE 0 END) AS userAmmount")
        )->first();
        if ($userBalance) {
            $res['status'] = '302';
            $res['message'] = 'Success';
            $res['userId'] = $userId;
            // $res['userName'] = $userBalance->SOCIAL_NAME;
            // $res['eMail'] = $userBalance->SOCIAL_EMAIL;
            $res['forceUpdate'] = 'false';
            $res['currency'] = 'inr';
            $res['userCoin'] = $userBalance->userCoin;
            $res['userAmmount'] = $userBalance->userAmmount;
            // $res['profPic'] = $userBalance->DOB;
            $res['type'] = 'app_open';
            return response($res, 200);
        } else {
            $res['status'] = false;
            $res['message'] = 'Failed';
            $res['type'] = 'profile_update';
            return response($res);
        }
    }
}
