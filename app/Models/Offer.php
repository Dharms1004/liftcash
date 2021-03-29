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
    protected $table = 'offer';
    public $timestamps = false;
    use Authenticatable, Authorizable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'OFFER_TYPE', 'OFFER_CATEGORY', 'OFFER_NAME', 'OFFER_DETAILS', 'OFFER_STEPS', 'OFFER_THUMBNAIL', 'OFFER_BANNER', 'OFFER_URL', 'OFFER_OS','OFFER_ORIGIN','CAP','FALLBACK_URL','STARTS_FROM','ENDS_ON','STATUS','OFFER_APP','CREATED_BY'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
}
