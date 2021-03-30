<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\UserWallet;

class UserController extends Controller
{
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
                    'deviceId' => 'required|alpha_num|min:5|max:150',
                    'socialType' => 'required|alpha_num|min:5|max:150',
                    'socialId' => 'required|alpha_num|min:5|max:150',
                    'versionName' => 'required|min:5|max:150',
                    'versionCode' => 'required|min:5|max:150',
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
                    // $password = $hasher->make($request->input('password'));
                    $api_token = sha1($socialEmail . time());
                    $userCreate = User::create([
                        'SOCIAL_EMAIL' => $socialEmail,
                        'SOCIAL_NAME' => $socialName,
                        'DEVICE_TYPE' => $deviceType,
                        'DEVICE_ID' => $deviceId,
                        'DEVICE_NAME' => $deviceName,
                        'SOCIAL_TYPE' => $socialType,
                        'SOCIAL_ID' => $socialId,
                        'PROFILE_PIC' => $socialImgurl,
                        'ADVERTISING_ID' => $advertisingId,
                        'VERSION_NAME' => $versionName,
                        'VERSION_CODE' => $versionCode,
                        'API_TOKEN' => $api_token
                    ]);
                    if (!empty($userCreate->id)) {
                        $this->createUserWallet($userCreate->id);
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

        for ($i = 1; $i <= 1; $i++) {
            $userWalletCreate = UserWallet::create([
                'BALANCE_TYPE' => $i,
                'BALANCE' => 0,
                'CREATED_DATE' => date("Y-m-d h:i:s"),
                'USER_ID' => $userId
            ]);
        }
    }
}
