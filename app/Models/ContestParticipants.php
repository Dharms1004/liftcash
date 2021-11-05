<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContestParticipants extends Model
{
    protected $table = 'contest_participants';
    public $timestamps = false;
    protected $primaryKey = 'PARTICIPATION_ID';

    protected $fillable = ['PARTICIPATION_ID','CONTEST_ID','USER_ID', 'ANSWER', 'STATUS'];

}
