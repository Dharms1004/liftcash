<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use DB;

class UserTransaction extends Controller
{
    public function getUserTransaction(Request $request)
    {
        $rules = [
            'userId' => 'required|max:10',
            'versionName' => 'required|max:100',
            'versionCode' => 'required|max:100',
            'api_token' => 'required|max:100',
            'limit' => 'required|max:100',
            'type' => 'required'


        ];
        $customMessages = [
            'required' => 'Please fill required :attribute'
        ];
        $this->validate($request, $rules, $customMessages);
        $userId = $request->input('userId');
        $token = $request->input('api_token');
        $limit = $request->input('limit');
        $check_token = User::where('API_TOKEN', $token)->select('USER_ID')->first();
 
        $table = $request->input('type') == 'coin' ? "master_transaction_history" : "master_transaction_history_diamond";
        
        $userTransaction = DB::table($table)
            ->join('transaction_type', "$table.TRANSACTION_TYPE_ID", '=', 'transaction_type.TRANSACTION_TYPE_ID')
            ->join('transaction_status', "$table.TRANSACTION_STATUS_ID", '=', 'transaction_status.TRANSACTION_STATUS_ID')
            ->select("$table.*", 'transaction_type.TRANSACTION_TYPE_NAME', 'transaction_status.TRANSACTION_STATUS_NAME')
            ->where("$table.USER_ID", $check_token->USER_ID)
            ->limit($limit)
            ->orderBy("$table.MASTER_TRANSACTTION_ID", 'desc')
            ->get();
        
        if (!empty($userTransaction)) {
            $statusData['status'] = '200';
            $statusData['message'] = 'Success';
            $statusData['type'] = 'user_transaction';
            foreach ($userTransaction as $userTransaction) {
                $res['userId'] = $userTransaction->USER_ID;
                $res['id'] = $userTransaction->MASTER_TRANSACTTION_ID;
                $res['amount'] = $userTransaction->PAYOUT_COIN;
                $res['transName'] = $userTransaction->TRANSACTION_TYPE_NAME;
                $res['transStatus'] = $userTransaction->TRANSACTION_STATUS_NAME;
                $res['date'] = $userTransaction->TRANSACTION_DATE;
                $transaction[] =  $res;
            }

            if(!empty($transaction)){
                $data=['data'=>$statusData,'transaction'=>$transaction];
                return response($data, 200);
            }else{
                $res['status'] = false;
                $res['message'] = 'No transactiions found';
                $res['type'] = 'user_transaction_failed';
                return response($res);
            }

        } else {
            $res['status'] = false;
            $res['message'] = 'Failed';
            $res['type'] = 'user_transaction_failed';
            return response($res);
        }
    }
}
