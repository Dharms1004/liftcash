<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentRegistration extends Model
{
    protected $table = 'tr_tournament_registration';
    public $timestamps = false;
    protected $primaryKey = 'ID';

    protected $fillable = ['ID','TOUR_ID','TEAM_ID','USER_ID', 'STATUS', 'CREATED_AT', 'UPDATED_AT'];

}
