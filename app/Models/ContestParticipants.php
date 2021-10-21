<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContestParticipants extends Model
{
    protected $table = 'contest_participants';
    // const UPDATED_AT = 'PARTICIPATED_DATE';
    public $timestamps = false;
    protected $primaryKey = 'PARTICIPATION_ID';

}
