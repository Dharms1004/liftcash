<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\OfferClicked;
use App\Models\User;
use App\Traits\common_methods;

class ContestCounter extends Controller
{
  
  use common_methods;

  public function index(Request $request)
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
    $token = $request->input('api_token');
    $coinsToBeCredit = $request->input('coinsToBeCredit');
    $check_token = User::where('API_TOKEN', $token)->select('USER_ID')->first();

    $scratchValue = $this->getScratchCardLimit($check_token->USER_ID); /** get user's scratch count balance */
    $spinValue =$this->getSpinLimit($check_token->USER_ID); /** get user's spin count balance */
    // $watchVideoValue =$this->getWatchVideoLimit($check_token->USER_ID); /** get user's watch video count balance */
    $diesValue =$this->getDiesLimit($check_token->USER_ID); /** get user's dies count balance */

    $scratchRemainingValue =  env('SCRATCH_LIMIT') - $scratchValue;
    $spinRemainingValue = env('SPIN_LIMIT') - $spinValue;
    // $watchVideoRemainingValue = env('SPIN_LIMIT') - $scratchValue;
    $diesRemainingValue = env('DIES_LIMIT') - $diesValue;

    if($check_token->USER_ID){
        $res['data'] = [
            "scratchValue" => $scratchValue,
            "scratchRemainingValue" => $scratchRemainingValue,
            "spinValue" => $spinValue,
            "spinRemainingValue" => $spinRemainingValue,
            "diesValue" => $diesValue,
            "diesRemainingValue" => $diesRemainingValue,
        ];
        $res['status'] = '200';
        $res['message'] = 'Success';
        $res['type'] = 'Limits_get_success';
        return response($res);

    }else{
        $res['status'] = false;
        $res['message'] = "unable to fetch details";
        $res['type'] = 'some_error_occured';
        return response($res);
    }



  }
}
