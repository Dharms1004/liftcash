<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class MasterTransactionHistory extends Model implements AuthenticatableContract, AuthorizableContract
{
    protected $table = 'master_transaction_history';
    public $timestamps = false;
    use Authenticatable, Authorizable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'USER_ID','BALANCE_TYPE_ID','TRANSACTION_STATUS_ID','TRANSACTION_TYPE_ID','TRANSACTION_AMOUNT','TRANSACTION_DATE','TRANSACTION_DATE','INTERNAL_REFERENCE_NO','CURRENT_TOT_BALANCE','CLOSING_TOT_BALANCE'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    
}
