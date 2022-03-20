<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'SOCIAL_EMAIL','SOCIAL_NAME','DEVICE_TYPE','DEVICE_ID','DEVICE_NAME','SOCIAL_TYPE','SOCIAL_ID','PROFILE_PIC','ADVERTISING_ID','VERSION_NAME','VERSION_CODE','API_TOKEN','REFFER_ID','REFFER_CODE','FCM_TOKEN'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'API_TOKEN'
    ];
}
