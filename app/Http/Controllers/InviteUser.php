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
            'api_token' => 'required',
            'versionName' => 'required',
        ];
        $customMessages = [
            'required' => 'Please fill email :attribute'
        ];
        $this->validate($request, $rules, $customMessages);
        $user = User::all();
        if ($user) {
            $res['status'] = true;
            $res['message'] = $user;
            return response($res);
        } else {
            $res['status'] = false;
            $res['message'] = 'Cannot find user!';

            return response($res);
        }
    }
}
