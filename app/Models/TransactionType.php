<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class TransactionType extends Model implements AuthenticatableContract, AuthorizableContract
{
    protected $table = 'transaction_type';
    public $timestamps = false;
    use Authenticatable, Authorizable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'TRANSACTION_TYPE_NAME','TRANSACTION_DESCRIPTION'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    
}
