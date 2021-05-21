<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\OfferClicked;
use App\Models\User;

class OfferClick extends Controller
{
    public function clickOffer(Request $request)
    {
        $rules = [
            'userId' => 'required||max:10',
            'offerId' => 'required||max:20',
            'versionName' => 'required',
            'versionName' => 'required'
        ];
        $customMessages = [
            'required' => 'Please fill required :attribute'
        ];
        $this->validate($request, $rules, $customMessages);
        $token = $request->input('api_token');
        $userId = $request->input('userId');
        $offerId = $request->input('offerId');
        $versionName = $request->input('versionName');
        $versionName = $request->input('versionName');

        $check_token = User::where('API_TOKEN', $token)->select('USER_ID')->first();

        $offerClicked = OfferClicked::create([
            'USER_ID' => $userId,
            'OFFER_ID' => $offerId
        ]);
        
        if (!empty($offerClicked)) {
            $res['status'] = '200';
            $res['message'] = 'Success';
            $res['actionType'] = 'Browser';
            $res['actionUrl'] = 'https://spinpay.app/';
            $res['type'] = 'offer_click';
            return response($res, 200);
        } else {
            $res['status'] = false;
            $res['message'] = 'Failed';
            $res['type'] = 'offer_click';
            return response($res);
        }
    }
}
