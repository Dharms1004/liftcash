<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Tournament;
use App\Models\TournamentWinner;
use App\Models\TournamentRegistration;
use App\Models\TournamentTeam;
use App\Traits\common_methods;
use Illuminate\Support\Facades\DB;

class TournamentController extends Controller
{

    use common_methods;

    public function getAllTours(Request $request)
    {

        $rules = [
            'api_token' => 'required|max:100'
        ];

        $customMessages = [
            'required' => 'Please fill required :attribute'
        ];

        $this->validate($request, $rules, $customMessages);
        $token = $request->input('api_token');
        $check_token = User::where('API_TOKEN', $token)->select('USER_ID')->first();

        $usersTeam = TournamentTeam::where(['USER_ID' => $check_token->USER_ID])->pluck('TEAM_ID');
        $upcomingTours = DB::table('tr_tournament as tt')->join('tr_tournament_registration as ttr', 'tt.TOUR_ID', '=', 'ttr.TOUR_ID')->where('tt.TOUR_STATUS', 1)->where('tt.TOUR_START_TIME', ">=", date('Y-m-d H:i:s'))->where(function($query) use ($usersTeam, $check_token) {
            foreach ($usersTeam as $team) {
                $team_id[] = $team;
           }
            $query->whereNotIn('ttr.TEAM_ID',$team_id);
            $query->whereNotIn('ttr.USER_ID',[$check_token->USER_ID]);
        })->get();
        $completedTours = Tournament::where('TOUR_END_TIME', "<", date('Y-m-d H:i:s'))->where('TOUR_STATUS', 1)->get();
        $registeringTours = DB::table('tr_tournament as tt')->join('tr_tournament_registration as ttr', 'tt.TOUR_ID', '=', 'ttr.TOUR_ID')->where('tt.TOUR_END_TIME', ">", date('Y-m-d H:i:s'))->where(['tt.TOUR_STATUS' => 1])->where(function($query) use ($usersTeam, $check_token) {
            foreach ($usersTeam as $team) {
                $team_id[] = $team;
           }
            $query->whereIn('ttr.TEAM_ID',$team_id);
            $query->orWhereIn('ttr.USER_ID',[$check_token->USER_ID]);
        })->get();


        if (count($upcomingTours)) {
            foreach ($upcomingTours as $i => $activeTour) {

                $upcoming[] =  [
                    "tourId" => $activeTour->TOUR_ID,
                    "tourType" => $activeTour->TOUR_TYPE,
                    "tourName" => $activeTour->TOUR_NAME,
                    "tourDesc" => $activeTour->TOUR_DESCRIPTION,
                    "tourPrizeMoney" => $activeTour->TOUR_PRIZE_MONEY,
                    "tourPrizeType" => $activeTour->TOUR_PRIZE_TYPE,
                    "tourMinStatus" => $activeTour->TOUR_STATUS,
                    "tourMaxTeam" => $activeTour->TOUR_MAX_TEAM_ALLOWED,
                    "tourMinTeam" => $activeTour->TOUR_MIN_TEAM_REQUIRED,
                    "tourMinPlayer" => $activeTour->TOUR_MIN_PLAYERS_REQUIRED,
                    "tourMaxPlayer" => $activeTour->TOUR_MAX_PLAYERS_ALLOWED,
                    "tourlogo" => env('TOUR_IMAGE_URL') . $activeTour->TOUR_LOGO,
                    "tourBanner" => env('TOUR_BANNER_IMAGE_URL') . $activeTour->TOUR_BANNER,
                    "tourMiniBanner" => env('TOUR_MINI_BANNER_IMAGE_URL') . $activeTour->TOUR_MINI_BANNER,
                    "tourStartTime" => date("d M Y h:i A", strtotime($activeTour->TOUR_START_TIME)),
                    "tourEndTime" => date("d M Y h:i A", strtotime($activeTour->TOUR_END_TIME)),
                    "tourRegStartTime" => date("d M Y h:i A", strtotime($activeTour->TOUR_REGISTRATION_START_TIME)),
                    "tourRegEndTime" => date("d M Y h:i A", strtotime($activeTour->TOUR_REGISTRATION_END_TIME)),
                    "tourOrgName" => $activeTour->ORG_NAME ?? "",
                    "tourOrgContact" => $activeTour->ORG_CONTACT ?? "",
                ];
            }
        } else {
            $upcoming = [];
        }

        if (count($completedTours)) {
            foreach ($completedTours as $i => $activeTour) {

                $completed[] =  [
                    "tourId" => $activeTour->TOUR_ID,
                    "tourType" => $activeTour->TOUR_TYPE,
                    "tourName" => $activeTour->TOUR_NAME,
                    "tourDesc" => $activeTour->TOUR_DESCRIPTION,
                    "tourPrizeMoney" => $activeTour->TOUR_PRIZE_MONEY,
                    "tourPrizeType" => $activeTour->TOUR_PRIZE_TYPE,
                    "tourMinStatus" => $activeTour->TOUR_STATUS,
                    "tourMaxTeam" => $activeTour->TOUR_MAX_TEAM_ALLOWED,
                    "tourMinTeam" => $activeTour->TOUR_MIN_TEAM_REQUIRED,
                    "tourMinPlayer" => $activeTour->TOUR_MIN_PLAYERS_REQUIRED,
                    "tourMaxPlayer" => $activeTour->TOUR_MAX_PLAYERS_ALLOWED,
                    "tourlogo" => env('TOUR_IMAGE_URL') . $activeTour->TOUR_LOGO,
                    "tourBanner" => env('TOUR_BANNER_IMAGE_URL') . $activeTour->TOUR_BANNER,
                    "tourMiniBanner" => env('TOUR_MINI_BANNER_IMAGE_URL') . $activeTour->TOUR_MINI_BANNER,
                    "tourStartTime" => date("d M Y h:i A", strtotime($activeTour->TOUR_START_TIME)),
                    "tourEndTime" => date("d M Y h:i A", strtotime($activeTour->TOUR_END_TIME)),
                    "tourRegStartTime" => date("d M Y h:i A", strtotime($activeTour->TOUR_REGISTRATION_START_TIME)),
                    "tourRegEndTime" => date("d M Y h:i A", strtotime($activeTour->TOUR_REGISTRATION_END_TIME)),
                    "tourOrgName" => $activeTour->ORG_NAME ?? "",
                    "tourOrgContact" => $activeTour->ORG_CONTACT ?? "",
                ];
            }
        } else {
            $completed = [];
        }

        if (count($registeringTours)) {
            foreach ($registeringTours as $i => $activeTour) {

                $registering[] =  [
                    "tourId" => $activeTour->TOUR_ID,
                    "tourType" => $activeTour->TOUR_TYPE,
                    "tourName" => $activeTour->TOUR_NAME,
                    "tourDesc" => $activeTour->TOUR_DESCRIPTION,
                    "tourPrizeMoney" => $activeTour->TOUR_PRIZE_MONEY,
                    "tourPrizeType" => $activeTour->TOUR_PRIZE_TYPE,
                    "tourMinStatus" => $activeTour->TOUR_STATUS,
                    "tourMaxTeam" => $activeTour->TOUR_MAX_TEAM_ALLOWED,
                    "tourMinTeam" => $activeTour->TOUR_MIN_TEAM_REQUIRED,
                    "tourMinPlayer" => $activeTour->TOUR_MIN_PLAYERS_REQUIRED,
                    "tourMaxPlayer" => $activeTour->TOUR_MAX_PLAYERS_ALLOWED,
                    "tourlogo" => env('TOUR_IMAGE_URL') . $activeTour->TOUR_LOGO,
                    "tourBanner" => env('TOUR_BANNER_IMAGE_URL') . $activeTour->TOUR_BANNER,
                    "tourMiniBanner" => env('TOUR_MINI_BANNER_IMAGE_URL') . $activeTour->TOUR_MINI_BANNER,
                    "tourStartTime" => date("d M Y h:i A", strtotime($activeTour->TOUR_START_TIME)),
                    "tourEndTime" => date("d M Y h:i A", strtotime($activeTour->TOUR_END_TIME)),
                    "tourRegStartTime" => date("d M Y h:i A", strtotime($activeTour->TOUR_REGISTRATION_START_TIME)),
                    "tourRegEndTime" => date("d M Y h:i A", strtotime($activeTour->TOUR_REGISTRATION_END_TIME)),
                    "tourOrgName" => $activeTour->ORG_NAME ?? "",
                    "tourOrgContact" => $activeTour->ORG_CONTACT ?? "",
                ];
            }
        } else {
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
                "tourType" => $activeTour->TOUR_TYPE,
                "tourName" => $activeTour->TOUR_NAME,
                "tourDesc" => $activeTour->TOUR_DESCRIPTION,
                "tourPrizeMoney" => $activeTour->TOUR_PRIZE_MONEY,
                "tourPrizeType" => $activeTour->TOUR_PRIZE_TYPE,
                "tourMinStatus" => $activeTour->TOUR_STATUS,
                "tourMaxTeam" => $activeTour->TOUR_MAX_TEAM_ALLOWED,
                "tourMinTeam" => $activeTour->TOUR_MIN_TEAM_REQUIRED,
                "tourMinPlayer" => $activeTour->TOUR_MIN_PLAYERS_REQUIRED,
                "tourMaxPlayer" => $activeTour->TOUR_MAX_PLAYERS_ALLOWED,
                "tourlogo" => env('TOUR_IMAGE_URL') . $activeTour->TOUR_LOGO,
                "tourBanner" => env('TOUR_BANNER_IMAGE_URL') . $activeTour->TOUR_BANNER,
                "tourMiniBanner" => env('TOUR_MINI_BANNER_IMAGE_URL') . $activeTour->TOUR_MINI_BANNER,
                "tourStartTime" => date("d M Y h:i A", strtotime($activeTour->TOUR_START_TIME)),
                "tourEndTime" => date("d M Y h:i A", strtotime($activeTour->TOUR_END_TIME)),
                "tourRegStartTime" => date("d M Y h:i A", strtotime($activeTour->TOUR_REGISTRATION_START_TIME)),
                "tourRegEndTime" => date("d M Y h:i A", strtotime($activeTour->TOUR_REGISTRATION_END_TIME)),
                "tourOrgName" => $activeTour->ORG_NAME,
                "tourOrgContact" => $activeTour->ORG_CONTACT,
                "tourRoomId" => $activeTour->ROOM_ID ?? "NA",
                "tourPassword" => $activeTour->PASSWORD ?? "NA",
                "tourRules" => json_decode($activeTour->TOUR_RULES),
            ];

            return response($statusData);
        } else {
            $res['status'] = false;
            $res['message'] = 'Failed';
            $res['type'] = 'failed_to_get_tour_detail';
            return response($res);
        }
    }

    public function getTournamentWinners(Request $request)
    {

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
        $tourWinner = DB::table('tr_winners as tw')->select("tw.TEAM_ID", "tw.TOUR_ID", "tw.TEAM_LOGO", "tw.TEAM_IMAGE", "tw.RANK", "tw.PRIZE_MONEY", "tt.TOUR_NAME", "ttt.TEAM_NAME")->join('tr_tournament_team as ttt', 'tw.TEAM_ID', '=', 'ttt.TEAM_ID')->join('tr_tournament as tt', 'tw.TOUR_ID', '=', 'tt.TOUR_ID')->where('tw.TOUR_ID', $tourId)->orderBy('tw.RANK')->get();

        if (count($tourWinner)) {
            foreach ($tourWinner as $key => $winner) {
                $win[$key] = [
                    'tour_id' => $winner->TOUR_ID,
                    'tour_name' => $winner->TOUR_NAME,
                    'team_id' => $winner->TEAM_ID,
                    'team_name' => $winner->TEAM_NAME,
                    'team_logo' => env('TR_TEAM_LOGO') . $winner->TEAM_LOGO,
                    'team_image' => env('TR_TEAM_IMAGE') . $winner->TEAM_IMAGE,
                    'rank' => $winner->RANK,
                    'prize_money' => $winner->PRIZE_MONEY

                ];
            }

            $statusData['status'] = '200';
            $statusData['message'] = 'Success';
            $statusData['type'] = 'get_single_tour';
            $statusData['winners'] = $win;

            return response($statusData);
        } else {
            $res['status'] = false;
            $res['message'] = 'Failed';
            $res['type'] = 'no_wiiners_declared_yet';
            return response($res);
        }
    }

    public function registerTeamInTour(Request $request)
    {

        $rules = [
            'tour_id' => 'required',
            'team_id' => 'required',

        ];
        $customMessages = [
            'required' => 'Please fill required :attribute'
        ];

        $this->validate($request, $rules, $customMessages);

        $token = $request->input('api_token');
        $check_token = User::where('API_TOKEN', $token)->select('USER_ID')->first();

        $tourId = $request->input('tour_id');
        $teamId = $request->input('team_id');
        $activeTour = Tournament::where('TOUR_REGISTRATION_END_TIME', ">=", date('Y-m-d H:i:s'))->where(['TOUR_STATUS' => 1, 'TOUR_ID' => $tourId])->first();

        if ($activeTour->TOUR_TYPE == 2) { //check that tournament is team type

            $registeredTeam = TournamentRegistration::where(['TOUR_ID' => $request->tour_id, 'TEAM_ID' => $request->team_id])->first();
            $availableTeam = TournamentTeam::where(['USER_ID' => $check_token->USER_ID, 'TEAM_ID' => $teamId])->first();

            if (!empty($activeTour)) { // ccheck if team belongs to same user 
                if (empty($registeredTeam)) { // check if tournament not started 
                    if (!empty($availableTeam)) { // check if team is alredy registered in same team
                        $teamRegistered = TournamentRegistration::create([
                            'TOUR_ID' => $tourId,
                            'TEAM_ID' => $teamId,
                            'STATUS' => 1,
                            'CREATED_AT'  => date('Y-m-d H:i:s'),
                        ]);

                        if (!empty($teamRegistered)) {
                            $res['status'] = '200';
                            $res['message'] = 'Success';
                            $res['type'] = 'Team Register sucessfully';
                            return response($res, 200);
                        } else {
                            $res['status'] = false;
                            $res['message'] = "unable to register team with this tournament";
                            $res['type'] = 'some_error_occured';
                            return response($res);
                        }
                    } else {
                        $res['status'] = false;
                        $res['message'] = 'Wrong Team selection.';
                        $res['type'] = 'seems_like_team_associated_with_other_user';
                        return response($res);
                    }
                } else {
                    $res['status'] = false;
                    $res['message'] = 'This team is already registered in this tournament.';
                    $res['type'] = 'already_registered';
                    return response($res);
                }
            } else {
                $res['status'] = false;
                $res['message'] = 'Tournament is already started or Not availble.';
                $res['type'] = 'no_tournament_found';
                return response($res);
            }
        } else {
            $res['status'] = false;
            $res['message'] = 'Only Team registrations are allowed.';
            $res['type'] = 'team_type_tournament';
            return response($res);
        }
    }

    public function registerPlayerInTour(Request $request)
    {
        $rules = [
            'tour_id' => 'required',
        ];
        $customMessages = [
            'required' => 'Please fill required :attribute'
        ];

        $this->validate($request, $rules, $customMessages);

        $token = $request->input('api_token');
        $check_token = User::where('API_TOKEN', $token)->select('USER_ID')->first();

        $tourId = $request->input('tour_id');
        $activeTour = Tournament::where('TOUR_REGISTRATION_END_TIME', ">=", date('Y-m-d H:i:s'))->where(['TOUR_STATUS' => 1, 'TOUR_ID' => $tourId])->first();
        
        if ($activeTour->TOUR_TYPE == 1) { //check that tournament is solo

            $registeredTeam = TournamentRegistration::where(['TOUR_ID' => $request->tour_id, 'USER_ID' => $check_token->USER_ID])->first();

            if (!empty($activeTour)) { // ccheck if team belongs to same user 
                if (empty($registeredTeam)) { // check if tournament not started 
                    $teamRegistered = TournamentRegistration::create([
                        'TOUR_ID' => $tourId,
                        'USER_ID' => $check_token->USER_ID,
                        'STATUS' => 1,
                        'CREATED_AT'  => date('Y-m-d H:i:s'),
                    ]);

                    if (!empty($teamRegistered)) {
                        $res['status'] = '200';
                        $res['message'] = 'Success';
                        $res['type'] = 'Player registered sucessfully';
                        return response($res, 200);
                    } else {
                        $res['status'] = false;
                        $res['message'] = "unable to register Player with this tournament";
                        $res['type'] = 'some_error_occured';
                        return response($res);
                    }
                } else {
                    $res['status'] = false;
                    $res['message'] = 'This Player is already registered in this tournament.';
                    $res['type'] = 'already_registered';
                    return response($res);
                }
            } else {
                $res['status'] = false;
                $res['message'] = 'Tournament is already started or Not availble.';
                $res['type'] = 'no_tournament_found';
                return response($res);
            }
        } else {
            $res['status'] = false;
            $res['message'] = 'Only Solo registrations are allowed.';
            $res['type'] = 'solo_type_tournament';
            return response($res);
        }
    }



    public function searchUser(Request $request)
    {

        $rules = [
            'username' => 'required',

        ];
        $customMessages = [
            'required' => 'Please fill required :attribute'
        ];

        $this->validate($request, $rules, $customMessages);

        $token = $request->input('api_token');
        $check_token = User::where('API_TOKEN', $token)->select('USER_ID')->first();

        $searchResult = User::select('SOCIAL_NAME', 'USER_ID', 'PHONE')->where('SOCIAL_NAME', 'like', '%' . $request->username . '%')->limit(10)->get()->toArray();

        if (!empty($searchResult)) {
            $res['status'] = '200';
            $res['message'] = 'Success';
            $res['type'] = 'Player list fetched sucessfully';
            $res['players'] = $searchResult;
            return response($res, 200);
        } else {
            $res['status'] = false;
            $res['message'] = "No player found with this username";
            return response($res);
        }
    }
}
