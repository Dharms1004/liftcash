<?php

namespace App\Traits;

use App\Models\Users;
use App\Models\UserWallet;
use App\Models\MasterTransactionHistory;
use App\Models\MasterTransactionHistoryDiamond;
use DB;
use DateTime;

trait common_methods
{
    public function getUserBalance($userId){

        return UserWallet::select("BALANCE","PROMO_BALANCE","MAIN_BALANCE")->where(['USER_ID' => $userId, 'COIN_TYPE' => 1])->first();
    }

    public function getUserDiamondBalance($userId){

        return UserWallet::select("BALANCE","PROMO_BALANCE","MAIN_BALANCE")->where(['USER_ID' => $userId, 'COIN_TYPE' => 2])->first();
    }

    public function updateUserDiamondMain($mainBalance, $promoBalance, $totBalance, $userId)
    {
        UserWallet::where(['USER_ID' => $userId, 'COIN_TYPE' => 2])->update(['BALANCE' => $totBalance, 'MAIN_BALANCE' => $mainBalance, 'PROMO_BALANCE' => $promoBalance]);
    }

    public function creditOrDebitDiamondToUser($data)
    {
        MasterTransactionHistoryDiamond::insert($data);
    }

    public function creditOrDebitCoinsToUser($data)
    {
        MasterTransactionHistory::insert($data);
    }

    public function updateUserBalance($totBalance ,$userNewPromoBalance ,$userId)
    {
        UserWallet::where(['USER_ID' => $userId, 'COIN_TYPE' => 1])->update(['BALANCE' => $totBalance, 'PROMO_BALANCE' => $userNewPromoBalance]);
    }

    public function updateUserBalanceMain($totBalance, $mainBalance, $userId)
    {
        UserWallet::where(['USER_ID' => $userId, 'COIN_TYPE' => 1])->update(['BALANCE' => $totBalance, 'MAIN_BALANCE' => $mainBalance]);
    }

    public function updateUserFinalBalance($mainBalance, $promoBalance, $totBalance, $userId)
    {
        UserWallet::where(['USER_ID' => $userId, 'COIN_TYPE' => 1])->update(['BALANCE' => $totBalance, 'MAIN_BALANCE' => $mainBalance, 'PROMO_BALANCE' => $promoBalance]);
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

        $spinTransactionTypeIdDiamond = 10;
        $spinTransactionStatusSuccessDiamond = 10;

       $count = DB::table('master_transaction_history')
            ->where(["USER_ID" => $userId, "TRANSACTION_TYPE_ID" => $spinTransactionTypeId, "TRANSACTION_STATUS_ID" => $spinTransactionStatusSuccess])
            ->whereBetween('TRANSACTION_DATE',[$startTime, $endTime])
            ->count();

        $countDiamond = DB::table('master_transaction_history_diamond')
            ->where(["USER_ID" => $userId, "TRANSACTION_TYPE_ID" => $spinTransactionTypeIdDiamond, "TRANSACTION_STATUS_ID" => $spinTransactionStatusSuccessDiamond])
            ->whereBetween('TRANSACTION_DATE',[$startTime, $endTime])
            ->count();

        return $countDiamond + $count;
        
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
