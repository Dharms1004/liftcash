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
            'api_token' => 'required|max:100'
        ];
        $customMessages = [
            'required' => 'Please fill required :attribute'
        ];
        $this->validate($request, $rules, $customMessages);
        $userId = $request->input('userId');
        $token  = $request->input('api_token');
       
        $userBalance = DB::table('users')->join('user_wallet', 'users.USER_ID', '=', 'user_wallet.USER_ID')->select('users.REFFER_CODE', 'user_wallet.BALANCE as userCoin')->where(['users.USER_ID' => $userId])->first();

        if ($userBalance) {
            $res['status'] = '200';
            $res['message'] = 'Success';
            $res['userId'] = $userId;
            $res['forceUpdate'] = 'false';
            $res['currency'] = 'INR';
            $res['userCoin'] = $userBalance->userCoin;
            $res['type'] = 'app_open';
            return response($res, 200);
        } else {
            $res['status'] = false;
            $res['message'] = 'Failed';
            $res['type'] = 'app_open_failed';
            return response($res);
        }
    }
}
