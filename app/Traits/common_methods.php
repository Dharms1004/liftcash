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

    public function updateUserBalance($totBalance ,$userNewPromoBalance ,$userId)
    {
        UserWallet::where(['USER_ID' => $userId])->update(['BALANCE' => $totBalance, 'PROMO_BALANCE' => $userNewPromoBalance]);
    }

    public function updateUserBalanceMain($totBalance, $mainBalance, $userId)
    {
        UserWallet::where(['USER_ID' => $userId])->update(['BALANCE' => $totBalance, 'MAIN_BALANCE' => $mainBalance]);
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

    public function getSpinLimit($userId){

        $startTime = date('Y-m-d 00:00:00');
        $endTime = date('Y-m-d 23:59:59');

        $spinTransactionTypeId = 1;
        $spinTransactionStatusSuccess = 1;

       return DB::table('master_transaction_history')
            ->where(["USER_ID" => $userId, "TRANSACTION_TYPE_ID" => $spinTransactionTypeId, "TRANSACTION_STATUS_ID" => $spinTransactionStatusSuccess])
            ->whereBetween('TRANSACTION_DATE',[$startTime, $endTime])
            ->count();
    }

    public function getScratchCardLimit($userId){

        $startTime = date('Y-m-d 00:00:00');
        $endTime = date('Y-m-d 23:59:59');

        $spinTransactionTypeId = 2;
        $spinTransactionStatusSuccess = 1;

       return DB::table('master_transaction_history')
            ->where(["USER_ID" => $userId, "TRANSACTION_TYPE_ID" => $spinTransactionTypeId, "TRANSACTION_STATUS_ID" => $spinTransactionStatusSuccess])
            ->whereBetween('TRANSACTION_DATE',[$startTime, $endTime])
            ->count();
    }

    public function getDiesLimit($userId){

        $startTime = date('Y-m-d 00:00:00');
        $endTime = date('Y-m-d 23:59:59');

        $spinTransactionTypeId = 9;
        $spinTransactionStatusSuccess = 1;

       return DB::table('master_transaction_history')
            ->where(["USER_ID" => $userId, "TRANSACTION_TYPE_ID" => $spinTransactionTypeId, "TRANSACTION_STATUS_ID" => $spinTransactionStatusSuccess])
            ->whereBetween('TRANSACTION_DATE',[$startTime, $endTime])
            ->count();
    }



}
