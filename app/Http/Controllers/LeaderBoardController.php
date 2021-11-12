<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\ContestQuestions;
use App\Models\Contest;
use App\Models\ContestParticipants;
use App\Models\User;
use App\Traits\common_methods;
use DB;

class LeaderBoardController extends Controller
{

  use common_methods;

  public function index(Request $request)
  {
    $rules = [
        'userId' => 'required|max:10',
        'api_token' => 'required|max:100',
        'contestId' => 'required',
    ];

    $customMessages = [
        'required' => 'Please fill required :attribute'
    ];

    $this->validate($request, $rules, $customMessages);
    $token = $request->input('api_token');
    $check_token = User::where('API_TOKEN', $token)->select('USER_ID')->first();

    $answer = env('CONTEST_ANSWER');

    $winnersList = \DB::table('contest_participants AS cp')
                    ->select(array('cp.USER_ID', 'cp.CONTEST_ID', DB::raw('COUNT(cp.USER_ID) as CONTEST_PLAYED'), 'u.USER_NAME', 'u.PROFILE_PIC', 'c.CONTEST_NAME', 'c.CONTEST_TITLE'))
                    ->join('users as u', 'cp.USER_ID', '=', 'u.USER_ID')
                    ->join('contest as c', 'cp.CONTEST_ID', '=', 'c.CONTEST_ID')
                    ->where(['cp.CONTEST_ID' => $request->contestId, 'cp.STATUS' => '1', 'ANSWER' => $answer])
                    ->groupBy('cp.USER_ID')
                    ->orderBy('CONTEST_PLAYED', 'desc')
                    ->limit(10)
                    ->get();

    if(!empty($winnersList)){
        $rank = 1;
        foreach($winnersList as $key => $winner){

            if($key && $winnersList[$key - 1]->CONTEST_PLAYED != $winner->CONTEST_PLAYED){
                $rank++;
            }

            $res['winners'][] = [
                'contestId' => $winner->CONTEST_ID,
                'contestName' => $winner->CONTEST_NAME,
                'contestTitle' => $winner->CONTEST_TITLE,
                'contestPlayed' => $winner->CONTEST_PLAYED,
                'userName' => $winner->USER_NAME,
                'profileImage' => $winner->PROFILE_PIC,
                'rank' => $rank,
            ];

        }

        $res['status'] = '200';
        $res['message'] = 'Success';
        $res['type'] = 'get_active_contest';
        return response($res);

    }else{
        $res['status'] = false;
        $res['message'] = "Currently there is no winner!";
        $res['type'] = 'some_error_occured';
        return response($res);
    }
  }

}
