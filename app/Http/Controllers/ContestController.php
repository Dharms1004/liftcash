<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\ContestQuestions;
use App\Models\Contest;
use App\Models\ContestParticipants;
use App\Models\User;
use App\Traits\common_methods;

class ContestController extends Controller
{

  use common_methods;

  public function index(Request $request)
  {
    $rules = [
        'userId' => 'required|max:10',
        'api_token' => 'required|max:100'
    ];

    $customMessages = [
        'required' => 'Please fill required :attribute'
    ];

    $this->validate($request, $rules, $customMessages);
    $token = $request->input('api_token');
    $check_token = User::where('API_TOKEN', $token)->select('USER_ID')->first();

    $activeContest = Contest::where('CONTEST_STATUS',1)->first();

    $contestQuestions = ContestQuestions::where('CONTEST_ID', $activeContest->CONTEST_ID)->get();

    $completedToday = ContestParticipants::where(['USER_ID' => $check_token->USER_ID, 'STATUS' => '1', 'CONTEST_ID' => $activeContest->CONTEST_ID])->whereDate('PARTICIPATED_DATE', date('Y-m-d'))->first();

    $contestCompletedOnSameDay = empty($completedToday) ? true : false;

    foreach($contestQuestions as $i => $question){
        $contestQuestionsRes[] =  [
            "questionId" => $question->QUESTION_ID,
            "question" => $question->QUESTION,
            "option_a" => $question->OPTION_A,
            "option_b" => $question->OPTION_B,
            "option_c" => $question->OPTION_C,
            "option_d" => $question->OPTION_D,
        ];
    }

    if(!empty($activeContest)){
        $res['contest']['data'] = [
            "contestId" => $activeContest->CONTEST_ID,
            "contestStatus" => $contestCompletedOnSameDay,
            "contestType" => "free",
            "contestName" => $activeContest->CONTEST_NAME,
            "contestTitle" => $activeContest->CONTEST_TITLE,
            "contestDetails" => $activeContest->CONTEST_DESCRIPTION,
            "contestBanner" => env('CONTEST_URL').$activeContest->CONTEST_IMAGE_LINK,
            "contestTerms" => $activeContest->CONTEST_TERMS_CONDITIONS,
            "contestQuestion" => [$contestQuestionsRes]
        ];
        $res['status'] = '200';
        $res['message'] = 'Success';
        $res['type'] = 'get_active_contest';

        return response($res);

    }else{
        $res['status'] = false;
        $res['message'] = "No contest running";
        $res['type'] = 'some_error_occured';
        return response($res);
    }
  }

  public function contestAnswer(Request $request){

    $rules = [
        'userId' => 'required|max:10',
        'api_token' => 'required|max:100',
        'contest_id' => 'required|max:100',
    ];

    $customMessages = [
        'required' => 'Please fill required :attribute'
    ];

    $this->validate($request, $rules, $customMessages);
    $token = $request->input('api_token');
    $check_token = User::where('API_TOKEN', $token)->select('USER_ID')->first();

    $status = $request->status;

    if($check_token->USER_ID){

        $submittedAnswer = ContestParticipants::where(['CONTEST_ID' => $request->contest_id, 'USER_ID' => $check_token->USER_ID])->whereDate('PARTICIPATED_DATE',date('Y-m-d'))->first();

        $attempStatus = isset($submittedAnswer->STATUS) ? $submittedAnswer->STATUS : 0;

        if($attempStatus == 0){

            $newans = [$request->question_id => $request->answer];

            if(!empty($submittedAnswer)){
                $ans = json_decode($submittedAnswer->ANSWER,true);
                $ans += $newans;

                $answer = json_encode($ans);
            }else{
                $answer = json_encode($newans);
            }

            $newAns = ContestParticipants::updateOrCreate([
                'CONTEST_ID'   => $request->get('contest_id'),
                'USER_ID'   => $check_token->USER_ID,
                'PARTICIPATED_DATE'   => $submittedAnswer->PARTICIPATED_DATE ?? date('Y-m-d'),
            ],[
                'ANSWER'     => $answer,
                'STATUS' => $status
            ]);

            if($newAns){
                $res['status'] = '200';
                $res['message'] = 'Success';
                $res['type'] = 'answer_success';
                return response($res);
            }else{
                $res['status'] = false;
                $res['message'] = "unable to submit answers";
                $res['type'] = 'some_error_occured';
                return response($res);
            }

        }else{
            $res['status'] = false;
            $res['message'] = "Contest Allready Completed";
            $res['type'] = 'some_error_occured';
            return response($res);
        }

    }else{
        $res['status'] = false;
        $res['message'] = "unable to submit answers";
        $res['type'] = 'some_error_occured';
        return response($res);
    }

  }

}
