<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentTeam extends Model
{
    protected $table = 'tr_tournament_team';
    public $timestamps = false;
    protected $primaryKey = 'TEAM_ID';

    protected $fillable = ['TEAM_ID','USER_ID','TEAM_TOUR_ID', 'TEAM_NAME', 'TEAM_DESCRIPTION', 'TEAM_CONTACT', 'TEAM_MANAGER_NAME', 'TEAM_MANAGER_EMAIL', 'TEAM_STATUS', 'CREATED_BY', 'CREATED_AT'];

}
