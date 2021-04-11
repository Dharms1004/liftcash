<?php

namespace App\Traits;

use App\Models\Users;
use App\Models\UserWallet;
use App\Models\MasterTransactionHistory;
use DB;
use DateTime;

trait common_methods
{
    public function getUserBalance($userId){

        return UserWallet::select("BALANCE")->where(['USER_ID' => $userId])->first();
    }

    public function creditOrDebitCoinsToUser($data)
    {
        MasterTransactionHistory::insert($data);
    }

    public function updateUserBalance($data, $userId)
    {
        UserWallet::where(['USER_ID' => $userId])->update(['BALANCE' => $data]);
    }

    public function getOpeningClosingBalace($userId)
    {
        return MasterTransactionHistory::where(['USER_ID' => $userId])->orderBy('TRANSACTION_DATE','desc')->first();
    }

    public function getDateTimeInMicroseconds()
	{
		$t = microtime(true);
		$micro = sprintf("%06d", ($t - floor($t)) * 1000000);
		$d = new DateTime(date('Y-m-d H:i:s.' . $micro, $t));
		return $d->format("ymdHisu");
	}
    

}