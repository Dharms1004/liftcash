<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Promotions;
use App\Models\User;
use DB;

class Misc extends Controller
{
    public function getActiveCountries(Request $request)
    {
        $country = DB::table('country')->where(['STATUS' => 1])->whereNotIn('ID',[240])->get();
        if ($country) {
            foreach ($country as $countryData) {

                $res['countryId'] = $countryData->ID;
                $res['countryName'] = $countryData->NAME;
                $res['type'] = 'get_all_country';
                $allCountryData[] = $res;

            }

            $gameResult = ['status' => '200', 'message' => 'Success', 'type' => 'get_all_country','country' => $allCountryData];

            return response($gameResult, 200);
        } else {
            $res['status'] = false;
            $res['message'] = 'Failed';
            $res['type'] = 'get_all_country';
            return response($res);
        }
    }
}
