<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Promotions;
use App\Models\User;
use DB;
use App\Traits\common_methods;

class Games extends Controller
{
    use common_methods;

    public function getAllGames(Request $request)
    {
        // dd($request->api_token);
        $country = $this->getUserCountryId($request->api_token);

        $countryId = $country->COUNTRY_CODE ?? 99;

        $games = DB::table('games')->where(['status' => 1, "country_id" => $countryId])->get();
        if ($games) {
            foreach ($games as $gameData) {

                $res['gameName'] = $gameData->name;
                $res['gameImage'] = env('GAME_URL').$gameData->image;
                $res['gameUrl'] = $gameData->url;
                $res['type'] = 'get_all_games';
                $allGames[] = $res;

            }

            $gameResult = ['status' => '200', 'message' => 'Success', 'type' => 'get_all_games','games' => $allGames];

            return response($gameResult, 200);
        } else {
            $res['status'] = false;
            $res['message'] = 'Failed';
            $res['type'] = 'get_all_games';
            return response($res);
        }
    }
}
