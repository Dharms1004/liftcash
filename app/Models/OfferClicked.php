<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class OfferClicked extends Model implements AuthenticatableContract, AuthorizableContract
{
    protected $table = 'offer_clicks';
    public $timestamps = false;
    use Authenticatable, Authorizable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'USER_ID', 'OFFER_ID', 'CLICK_ID', 'IP_ADDRESS', 'CLICKED_ON',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
}
