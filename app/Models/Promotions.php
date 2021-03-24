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
    protected $table = 'promotions';
    public $timestamps = false;
    use Authenticatable, Authorizable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'USER_ID', 'PROMOTION_TYPE', 'PROMOTION_CATEGORY', 'PROMOTION_NAME', 'PROMOTION_DETAILS', 'PROMOTION_STEPS', 'PROMOTION_THUMBNAIL', 'PROMOTION_BANNER', 'PROMOTION_URL','PROMOTION_OS','PROMOTION_ORIGIN','CAP','STARTS_FROM','ENDS_ON','STATUS','PROMOTION_APP','CREATED_BY'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
}
