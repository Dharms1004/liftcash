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
        $check_token = User::where('API_TOKEN', $token)->select('USER_ID')->first();
        $profileUpdate = User::where('USER_ID', $check_token->USER_ID)->update([
            'PHONE' => $phone,
            'USER_LOCALE' => $locale,
            'USER_NAME' => $userName,
            'OCCUPATION' => $occupation,
            'DOB' => $dob,
            'PROFILE_PIC' => $profilePic
        ]);
        
        if ($profileUpdate) {
            $res['status'] = '301';
            $res['message'] = 'Success';
            $res['userId'] = $check_token->USER_ID;
            $res['userName'] = $userName;
            $res['eMail'] = $userName;
            $res['gender'] = $userName;
            $res['location'] = $userName;
            $res['occupation'] = $userName;
            $res['dob'] = $userName;
            $res['profPic'] = $userName;
            $res['type'] = 'profile_update';
            return response($res, 200);
        } else {
            $res['status'] = false;
            $res['message'] = 'Failed';
            $res['type'] = 'profile_update';
            return response($res);
        }
    }

    public function getProfileInfo(Request $request){
        
        $token = $request->input('api_token');
        $check_token = User::where('API_TOKEN', $token)->select('USER_ID')->first();
        $userId=$request->input('userId');
        $email=$request->input('email');
        $profileData=User::where(['USER_ID' =>$check_token->USER_ID])->first();
        if ($profileData) {
            $res['status'] = '302';
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
