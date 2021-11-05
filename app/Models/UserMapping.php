<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMapping extends Model
{
    protected $table = "user_mapping";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public $timestamps =false;

    protected $primaryKey = 'RF_USER_MAPPING_ID';
    protected $fillable = ['RF_USER_MAPPING_ID','REFERRER_USER_ID','REFERRAL_USER_ID','BONUS_STATUS','STATUS','REGISTERED_DATE_TIME'];

}
