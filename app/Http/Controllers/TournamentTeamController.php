<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\TournamentTeam;
use App\Models\TeamPlayer;
use App\Models\User;
use App\Traits\common_methods;

class TournamentTeamController extends Controller
{

  use common_methods;

  public function createTourTeam(Request $request){

    $rules = [
        'api_token' => 'required|max:100',
        // 'tour_id' => 'required|max:100',
        'team_name' => 'required|max:100',
        'team_desc' => 'required|max:100',
        'contact_person' => 'required|max:10',
    ];

    $customMessages = [
        'required' => 'Please fill required :attribute'
    ];

    $this->validate($request, $rules, $customMessages);
    $token = $request->input('api_token');
    $check_token = User::where('API_TOKEN', $token)->select('USER_ID')->first();

        $tourTeam = TournamentTeam::create([
            'USER_ID' => $check_token->USER_ID,
            // 'TEAM_TOUR_ID' => $request->tour_id,
            'TEAM_NAME' => $request->team_name,
            'TEAM_DESCRIPTION' => $request->team_desc,
            'TEAM_CONTACT' => $request->contact_person,
            'TEAM_STATUS' => 1,
            'CREATED_BY' => $check_token->USER_ID,
            'CREATED_AT'  => date('Y-m-d H:i:s'),
        ]);
        
        if(!empty($tourTeam)){
            $res['status'] = '200';
            $res['message'] = 'Success';
            $res['type'] = 'Team created sucessfully';
            return response($res, 200);
        }else{
            $res['status'] = false;
            $res['message'] = "unable to create team";
            $res['type'] = 'some_error_occured';
            return response($res);
        }

  }

  public function registerPlayer(Request $request){

    $rules = [
        'api_token' => 'required|max:100',
        'team_id' => 'required|max:100',
        'player_name' => 'required|max:100',
        'player_mobile' => 'required|max:100'
    ];

    $customMessages = [
        'required' => 'Please fill required :attribute'
    ];

    $this->validate($request, $rules, $customMessages);
    $token = $request->input('api_token');
    $check_token = User::where('API_TOKEN', $token)->select('USER_ID')->first();
    $checkIfTeamExist = TeamPlayer::where(['PLAYER_MOBILE' =>  $request->player_mobile, 'PLAYER_TEAM_ID' => $request->team_id])->first();
    
    if(empty($checkIfTeamExist)){
        
        $tourTeam = TeamPlayer::create([
            'PLAYER_TEAM_ID' => $request->team_id,
            'PLAYER_NAME' => $request->player_name,
            'PLAYER_MOBILE' => $request->player_mobile,
            'PLAYER_STATUS' => 1,
            'CREATED_BY' => $check_token->USER_ID,
            'CREATED_AT'  => date('Y-m-d H:i:s'),
        ]);
        
        if(!empty($tourTeam)){
            $res['status'] = '200';
            $res['message'] = 'Success';
            $res['type'] = 'Registered sucessfully';
            return response($res, 200);
        }else{
            $res['status'] = false;
            $res['message'] = "unable to register in a team";
            $res['type'] = 'some_error_occured';
            return response($res);
        }

    }else{
        $res['status'] = false;
        $res['message'] = "Player is already registered in a team.";
        $res['type'] = 'some_error_occured';
        return response($res, 400);
    }

  }

  public function getMyTeam(Request $request){

    $rules = [
        'api_token' => 'required|max:100',
        'team_id' => 'required|max:100',
    ];

    $customMessages = [
        'required' => 'Please fill required :attribute'
    ];

    $this->validate($request, $rules, $customMessages);
    $token = $request->input('api_token');
    $check_token = User::where('API_TOKEN', $token)->select('USER_ID')->first();

    $getTeam = TournamentTeam::where(['USER_ID' => $check_token->USER_ID, 'TEAM_ID' => $request->team_id])->first();

    if(!empty($getTeam)){        
        $res['status'] = '200';
        $res['message'] = 'Success';
        $res['team']['name'] = $getTeam->TEAM_NAME;
        $res['team']['desc'] = $getTeam->TEAM_DESCRIPTION;
        $res['team']['team_contact'] = $getTeam->TEAM_CONTACT;

        $teamPlayers = TeamPlayer::where(['PLAYER_TEAM_ID' => $getTeam->TEAM_ID])->get();

        if(count($teamPlayers)){
            foreach ($teamPlayers as $player) {
                $res['team']['teamPlayer'][] = [
                    'playerName' => $player->PLAYER_NAME,
                    'playerMobile' => $player->PLAYER_MOBILE,
                ];
            }
        }else{
            $res['team']['teamPlayer'] = [];
        }

        return response($res, 200);
    }else{
        $res['status'] = false;
        $res['message'] = "No teams found.";
        $res['type'] = 'some_error_occured';
        return response($res, 400);
    }

  }

  public function addTeamWithPlayers(Request $request){
    $rules = [
        'api_token' => 'required|max:100',
        'tour_id' => 'required|max:100',
        'team_name' => 'required|max:100',
        'player_count' => 'required',
        'team_desc' => 'required|max:100',
        'contact_person' => 'required|max:10'
    ];
    
    $customMessages = [
        'required' => 'Please fill required :attribute'
    ];

    $this->validate($request, $rules, $customMessages);
    $token = $request->input('api_token');
    $check_token = User::where('API_TOKEN', $token)->select('USER_ID')->first();

        $tourTeam = TournamentTeam::create([
            'USER_ID' => $check_token->USER_ID,
            // 'TEAM_TOUR_ID' => $request->tour_id,
            'TEAM_NAME' => $request->team_name,
            'TEAM_DESCRIPTION' => $request->team_desc,
            'TEAM_CONTACT' => $request->contact_person,
            'TEAM_STATUS' => 1,
            'CREATED_BY' => $check_token->USER_ID,
            'CREATED_AT'  => date('Y-m-d H:i:s'),
        ]);
        
        if(!empty($tourTeam)){

            for ($i=1; $i <= $request->player_count; $i++) { 
                $playersName = 'player_name'.$i;
                $playersMobile = 'player_mobile'.$i;
                $players[] = [
                    'PLAYER_TEAM_ID' => $tourTeam->TEAM_ID,
                    'PLAYER_NAME' => $request->$playersName,
                    'PLAYER_MOBILE' => $request->$playersMobile,
                    'PLAYER_STATUS' => 1,
                    'CREATED_BY' => $check_token->USER_ID,
                    'CREATED_AT'  => date('Y-m-d H:i:s')  
                ];              
            }
           
            $tourTeam = TeamPlayer::insert($players);

            $res['status'] = '200';
            $res['message'] = 'Success';
            $res['type'] = 'Team created sucessfully';
            return response($res, 200);
        }else{
            $res['status'] = false;
            $res['message'] = "unable to create team";
            $res['type'] = 'some_error_occured';
            return response($res);
        }
  }

  public function getAllTeams(Request $request){
    
    $rules = [
        'api_token' => 'required|max:100',
    ];

    $customMessages = [
        'required' => 'Please fill required :attribute'
    ];

    $this->validate($request, $rules, $customMessages);
    $token = $request->input('api_token');
    $check_token = User::where('API_TOKEN', $token)->select('USER_ID')->first();

    $getTeams = TournamentTeam::where(['USER_ID' => $check_token->USER_ID])->get();

    if(!empty($getTeams)){   
        $res['status'] = '200';
        $res['message'] = 'Success';
        foreach ($getTeams as $key => $team) {   
            $newTeam[$key]['name'] = $team->TEAM_NAME;
            $newTeam[$key]['desc'] = $team->TEAM_DESCRIPTION;
            $newTeam[$key]['team_contact'] = $team->TEAM_CONTACT;

            $teamPlayers = TeamPlayer::where(['PLAYER_TEAM_ID' => $team->TEAM_ID])->get();

            if(count($teamPlayers)){
                foreach ($teamPlayers as $player) {
                    $newTeam[$key]['teamPlayer'][] = [
                        'playerName' => $player->PLAYER_NAME,
                        'playerMobile' => $player->PLAYER_MOBILE,
                    ];
                }
            }else{
                $newTeam[$key]['teamPlayer'] = [];
            }

            $res['team'] = $newTeam;
        }
        return response($res, 200);
    }else{
        $res['status'] = false;
        $res['message'] = "No teams found.";
        $res['type'] = 'some_error_occured';
        return response($res, 400);
    }

  }

}
