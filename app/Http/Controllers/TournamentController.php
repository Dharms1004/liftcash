<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\ContestQuestions;
use App\Models\Contest;
use App\Models\ContestParticipants;
use App\Models\User;
use App\Models\Tournament;
use App\Models\TournamentWinner;
use App\Traits\common_methods;
use Illuminate\Support\Facades\DB;

class TournamentController extends Controller
{

  use common_methods;

  public function getAllTours(Request $request){

    $rules = [
        'api_token' => 'required|max:100'
    ];

    $customMessages = [
        'required' => 'Please fill required :attribute'
    ];

    $this->validate($request, $rules, $customMessages);
    $token = $request->input('api_token');
    $check_token = User::where('API_TOKEN', $token)->select('USER_ID')->first();

    $upcomingTours = Tournament::where('TOUR_START_TIME', ">=", date('Y-m-d H:i:s'))->where('TOUR_STATUS', 1)->get();
    $completedTours = Tournament::where('TOUR_END_TIME', "<", date('Y-m-d H:i:s'))->where('TOUR_STATUS', 1)->get();
    $registeringTours = DB::table('tr_tournament as tt')->join('tr_tournament_team as ttt', 'tt.TOUR_ID', '=', 'ttt.TEAM_TOUR_ID')->where('tt.TOUR_END_TIME', ">", date('Y-m-d H:i:s'))->where(['tt.TOUR_STATUS' => 1, 'ttt.USER_ID' => $check_token->USER_ID])->get();
   
    if(count($upcomingTours)){
        foreach($upcomingTours as $i => $activeTour){
       
            $upcoming[] =  [
                "tourId" => $activeTour->TOUR_ID,
                "tourName" => $activeTour->TOUR_NAME,
                "tourDesc" => $activeTour->TOUR_DESCRIPTION,
                "tourPrizeMoney" => $activeTour->TOUR_PRIZE_MONEY,
                "tourPrizeType" => $activeTour->TOUR_PRIZE_TYPE,
                "tourMinStatus" => $activeTour->TOUR_STATUS,
                "tourMaxTeam" => $activeTour->TOUR_MAX_TEAM_ALLOWED,
                "tourMinTeam" => $activeTour->TOUR_MIN_TEAM_REQUIRED,
                "tourMinPlayer" => $activeTour->TOUR_MIN_PLAYERS_REQUIRED,
                "tourMaxPlayer" => $activeTour->TOUR_MAX_PLAYERS_ALLOWED,
                "tourlogo" => $activeTour->TOUR_LOGO,
                "tourBanner" => $activeTour->TOUR_BANNER,
                "tourMiniBanner" => $activeTour->TOUR_MINI_BANNER,
                "tourStartTime" => $activeTour->TOUR_START_TIME,
                "tourEndTime" => $activeTour->TOUR_END_TIME,
                "tourRegStartTime" => $activeTour->TOUR_REGISTRATION_START_TIME,
                "tourRegEndTime" => $activeTour->TOUR_REGISTRATION_END_TIME,
            ];
        }
    }else{
        $upcoming = [];
    }

    if(count($completedTours)){
        foreach($completedTours as $i => $activeTour){
       
            $completed[] =  [
                "tourId" => $activeTour->TOUR_ID,
                "tourName" => $activeTour->TOUR_NAME,
                "tourDesc" => $activeTour->TOUR_DESCRIPTION,
                "tourPrizeMoney" => $activeTour->TOUR_PRIZE_MONEY,
                "tourPrizeType" => $activeTour->TOUR_PRIZE_TYPE,
                "tourMinStatus" => $activeTour->TOUR_STATUS,
                "tourMaxTeam" => $activeTour->TOUR_MAX_TEAM_ALLOWED,
                "tourMinTeam" => $activeTour->TOUR_MIN_TEAM_REQUIRED,
                "tourMinPlayer" => $activeTour->TOUR_MIN_PLAYERS_REQUIRED,
                "tourMaxPlayer" => $activeTour->TOUR_MAX_PLAYERS_ALLOWED,
                "tourlogo" => $activeTour->TOUR_LOGO,
                "tourBanner" => $activeTour->TOUR_BANNER,
                "tourMiniBanner" => $activeTour->TOUR_MINI_BANNER,
                "tourStartTime" => $activeTour->TOUR_START_TIME,
                "tourEndTime" => $activeTour->TOUR_END_TIME,
                "tourRegStartTime" => $activeTour->TOUR_REGISTRATION_START_TIME,
                "tourRegEndTime" => $activeTour->TOUR_REGISTRATION_END_TIME,
            ];
        }
    }else{
        $completed = [];
    }

    if(count($registeringTours)){
        foreach($registeringTours as $i => $activeTour){
       
            $registering[] =  [
                "tourId" => $activeTour->TOUR_ID,
                "tourName" => $activeTour->TOUR_NAME,
                "tourDesc" => $activeTour->TOUR_DESCRIPTION,
                "tourPrizeMoney" => $activeTour->TOUR_PRIZE_MONEY,
                "tourPrizeType" => $activeTour->TOUR_PRIZE_TYPE,
                "tourMinStatus" => $activeTour->TOUR_STATUS,
                "tourMaxTeam" => $activeTour->TOUR_MAX_TEAM_ALLOWED,
                "tourMinTeam" => $activeTour->TOUR_MIN_TEAM_REQUIRED,
                "tourMinPlayer" => $activeTour->TOUR_MIN_PLAYERS_REQUIRED,
                "tourMaxPlayer" => $activeTour->TOUR_MAX_PLAYERS_ALLOWED,
                "tourlogo" => $activeTour->TOUR_LOGO,
                "tourBanner" => $activeTour->TOUR_BANNER,
                "tourMiniBanner" => $activeTour->TOUR_MINI_BANNER,
                "tourStartTime" => $activeTour->TOUR_START_TIME,
                "tourEndTime" => $activeTour->TOUR_END_TIME,
                "tourRegStartTime" => $activeTour->TOUR_REGISTRATION_START_TIME,
                "tourRegEndTime" => $activeTour->TOUR_REGISTRATION_END_TIME,
            ];
        }
    }else{
        $registering = [];
    }


    $res['status'] = '200';
    $res['message'] = 'Success';
    $res['type'] = 'get_active_tours';
    $res['tourData']['upcoming'] = $upcoming;
    $res['tourData']['completed'] = $completed;
    $res['tourData']['registered'] = $registering;

    return response($res);

  }

  public function getTour(Request $request)
  {
    $rules = [
        'limit' => 'required|max:100',
        'tour_id' => 'required',

    ];
    $customMessages = [
        'required' => 'Please fill required :attribute'
    ];

    $this->validate($request, $rules, $customMessages);
    $limit = $request->input('limit');
    $tourId = $request->input('tour_id');
    $activeTour = Tournament::where('TOUR_ID', $tourId)->first();
    if (!empty($activeTour)) {
        $statusData['status'] = '200';
        $statusData['message'] = 'Success';
        $statusData['type'] = 'get_single_tour';
        $statusData['tour_details'] =  [
            "tourId" => $activeTour->TOUR_ID,
            "tourName" => $activeTour->TOUR_NAME,
            "tourDesc" => $activeTour->TOUR_DESCRIPTION,
            "tourPrizeMoney" => $activeTour->TOUR_PRIZE_MONEY,
            "tourPrizeType" => $activeTour->TOUR_PRIZE_TYPE,
            "tourMinStatus" => $activeTour->TOUR_STATUS,
            "tourMaxTeam" => $activeTour->TOUR_MAX_TEAM_ALLOWED,
            "tourMinTeam" => $activeTour->TOUR_MIN_TEAM_REQUIRED,
            "tourMinPlayer" => $activeTour->TOUR_MIN_PLAYERS_REQUIRED,
            "tourMaxPlayer" => $activeTour->TOUR_MAX_PLAYERS_ALLOWED,
            "tourlogo" => $activeTour->TOUR_LOGO,
            "tourBanner" => $activeTour->TOUR_BANNER,
            "tourMiniBanner" => $activeTour->TOUR_MINI_BANNER,
            "tourStartTime" => $activeTour->TOUR_START_TIME,
            "tourEndTime" => $activeTour->TOUR_END_TIME,
            "tourRegStartTime" => $activeTour->TOUR_REGISTRATION_START_TIME,
            "tourRegEndTime" => $activeTour->TOUR_REGISTRATION_END_TIME,
        ];
       
        return response($statusData);
    } else {
        $res['status'] = false;
        $res['message'] = 'Failed';
        $res['type'] = 'failed_to_get_tour_detail';
        return response($res);
    }
  }

  public function getTournamentWinners(Request $request){

    $rules = [
        'tour_id' => 'required',
        'api_token' => 'required|max:100'

    ];

    $customMessages = [
        'required' => 'Please fill required :attribute'
    ];

    $this->validate($request, $rules, $customMessages);
    $limit = $request->input('limit');
    $tourId = $request->input('tour_id');
    $tourWinner = DB::table('tr_winners as tw')->select("tw.TEAM_ID", "tw.TOUR_ID", "tw.RANK", "tw.PRIZE_MONEY" ,"tt.TOUR_NAME", "ttt.TEAM_NAME")->join('tr_tournament_team as ttt', 'tw.TEAM_ID', '=', 'ttt.TEAM_ID')->join('tr_tournament as tt', 'tw.TOUR_ID', '=', 'tt.TOUR_ID')->where('tw.TOUR_ID', $tourId)->orderBy('tw.RANK')->get();
 
    if(count($tourWinner)){
        foreach ($tourWinner as $key => $winner) {
            $win[$key] = [
                'tour_id' => $winner->TOUR_ID,
                'tour_name' => $winner->TOUR_NAME,
                'team_id' => $winner->TEAM_ID,
                'team_name' => $winner->TEAM_NAME,
                'rank' => $winner->RANK,
                'prize_money' => $winner->PRIZE_MONEY

            ];
        }

        $statusData['status'] = '200';
        $statusData['message'] = 'Success';
        $statusData['type'] = 'get_single_tour';
        $statusData['winners'] = $win;

        return response($statusData);

    }else{
        $res['status'] = false;
        $res['message'] = 'Failed';
        $res['type'] = 'no_wiiners_declared_yet';
        return response($res);
    }
    
  }

}
