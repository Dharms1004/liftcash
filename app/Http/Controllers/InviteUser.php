<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class InviteUser extends Controller
{
    public function invite(Request $request)
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

        $user = User::select('REFFER_CODE')->where(['USER_ID' => $userId])->first();

        if ($user) {
            $res['status'] = true;
            $res['message'] = "Success";
            $res['refferCode'] = $user->REFFER_CODE;
            $res['inviteUrl'] = env('INVITE_URL');

            $data = ["data" => $res];
            return response($data);

        } else {
            $res['status'] = false;
            $res['message'] = 'unable to fetch Invite Code.';
            
            $data = ["data" => $res];
            return response($data);
        }
    }
}
