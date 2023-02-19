<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Promotions;
use App\Models\User;
use DB;
use App\Traits\common_methods;

class Jokes extends Controller
{
    use common_methods;

    public function getJokeCategory(Request $request){

        $jokeCat = DB::table('joke_category')->where(['STATUS' => 1])->get();

        if (!$jokeCat->isEmpty()) {
            foreach ($jokeCat as $cat) {

                $res['catId'] = $cat->ID;
                $res['catName'] = $cat->CATEGORY_NAME;
                $allJokes[] = $res;

            }

            $jokeResult = ['status' => '200', 'message' => 'Success', 'type' => 'get_all_joke_category','jokesCat' => $allJokes];

            return response($jokeResult, 200);
        }else{
            $res['status'] = false;
            $res['message'] = 'Unable to get jokes categories.';
            $res['type'] = 'get_all_joke_category';
            return response($res);
        }
    }

    public function getJokesByCat(Request $request)
    {

        $jokes = DB::table('jokes')->where(['STATUS' => 1, "JOKE_CAT" => $request->catId])->get();
        
        if (!$jokes->isEmpty()) {
            foreach ($jokes as $joke) {

                $res['jokeId'] = $joke->ID;
                $res['jokeTitle'] = $joke->JOKE_TITLE;
                $res['joke'] = $joke->JOKE;
                $allGames[] = $res;

            }

            $gameResult = ['status' => '200', 'message' => 'Success', 'type' => 'get_all_jokes','jokes' => $allGames];

            return response($gameResult, 200);
        } else {
            $res['status'] = false;
            $res['message'] = 'Unable to get jokes of this categories.';
            $res['type'] = 'get_all_jokes_by_category';
            return response($res);
        }
    }

    public function getJokeById(Request $request){

        $jokes = DB::table('jokes')->where(['STATUS' => 1, "ID" => $request->jokeId])->get();
       
        if (!$jokes->isEmpty()) {
            foreach ($jokes as $joke) {

                $res['jokeId'] = $joke->ID;
                $res['jokeTitle'] = $joke->JOKE_TITLE;
                $res['joke'] = $joke->JOKE;
                $allGames[] = $res;

            }

            $gameResult = ['status' => '200', 'message' => 'Success', 'type' => 'get_jokes','jokes' => $allGames];

            return response($gameResult, 200);
        } else {
            $res['status'] = false;
            $res['message'] = 'Unable to get joke.';
            $res['type'] = 'get_jokes_by_id';
            return response($res);
        }
    }
}
