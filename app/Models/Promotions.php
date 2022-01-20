<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class UserWallet extends Model implements AuthenticatableContract, AuthorizableContract
{
    protected $table = 'user_wallet';
    public $timestamps = false;
    use Authenticatable, Authorizable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'COIN_TYPE', 'BALANCE_TYPE', 'MAIN_BALANCE', 'PROMO_BALANCE', 'BALANCE','CREATED_DATE','USER_ID','UPDATE_DATE'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
}
