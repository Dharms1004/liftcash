<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class Profile extends Controller
{
    public function update(Request $request)
    {
        $rules = [
            //'userId' => 'required|max:10',
            'phone' => 'required|digits:10',
            'locale' => 'required',
            'userName' => 'required',
            'occupation' => 'required',
            'dob' => 'required',
            'profilePic' => 'required',
            'gender' => 'required',
        ];
        $customMessages = [
            'required' => 'Please fill required :attribute'
        ];
        $this->validate($request, $rules, $customMessages);
        $token = $request->input('api_token');
        $phone = $request->input('phone');
        $locale = $request->input('locale');
        $userName = $request->input('userName');
        $occupation = $request->input('occupation');
        $dob = $request->input('dob');
        $profilePic = $request->input('profilePic');
        $gender = $request->input('gender');
        $check_token = User::where('API_TOKEN', $token)->select('USER_ID')->first();
        $profileUpdate = User::where('USER_ID', $check_token->USER_ID)->update([
            'PHONE' => $phone,
            'USER_LOCALE' => $locale,
            'USER_NAME' => $userName,
            'OCCUPATION' => $occupation,
            'DOB' => $dob,
            'PROFILE_PIC' => $profilePic,
            'GENDER' => $gender
        ]);
        $userData = User::where('API_TOKEN', $token)->select( 'USER_ID', 'PHONE', 'SOCIAL_EMAIL', 'DEVICE_TYPE', 'DEVICE_ID', 'SOCIAL_TYPE', 'SOCIAL_NAME', 'USER_NAME', 'OCCUPATION', 'DOB', 'PROFILE_PIC', 'GENDER', 'COUNTRY_CODE', 'USER_LOCALE', 'QUALIFICATION', 'STATE')->first();
        
        if ($profileUpdate && !empty($userData)) {
            $res['status'] = '200';
            $res['message'] = 'Success';
            $res['userId'] = $userData->USER_ID;
            $res['userName'] = $userData->USER_NAME;
            $res['eMail'] = $userData->SOCIAL_EMAIL;
            $res['gender'] = $userData->GENDER;
            $res['location'] = $userData->STATE;
            $res['occupation'] = $userData->OCCUPATION;
            $res['dob'] = $userData->DOB;
            $res['profPic'] = $userData->PROFILE_PIC;
            $res['type'] = 'profile_update';
            return response($res, 200);
        } else {
            $res['status'] = false;
            $res['message'] = 'Failed';
            $res['type'] = 'profile_update';
            return response($res);
        }
    }

    public function getProfileInfo(Request $request)
    {

        $token = $request->input('api_token');
        $check_token = User::where('API_TOKEN', $token)->select('USER_ID')->first();
        $userId = $request->input('userId');
        $email = $request->input('email');
        $profileData = User::where(['USER_ID' => $check_token->USER_ID])->first();
        if ($profileData) {
            $res['status'] = '200';
            $res['message'] = 'Success';
            $res['userId'] = $profileData->USER_ID;
            $res['userName'] = $profileData->SOCIAL_NAME;
            $res['eMail'] = $profileData->SOCIAL_EMAIL;
            $res['gender'] = $profileData->GENDER;
            $res['location'] = $profileData->STATE;
            $res['occupation'] = $profileData->OCCUPATION;
            $res['dob'] = $profileData->DOB;
            $res['profPic'] = $profileData->DOB;
            $res['type'] = 'profile_get';
            return response($res, 200);
        } else {
            $res['status'] = false;
            $res['message'] = 'Failed';
            $res['type'] = 'profile_get';
            return response($res);
        }
    }
}
