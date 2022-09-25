<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamPlayer extends Model
{
    protected $table = 'tr_team_player';
    public $timestamps = false;
    protected $primaryKey = 'PLAYER_ID';

    protected $fillable = ['PLAYER_ID','PLAYER_TEAM_ID','PLAYER_NAME', 'PLAYER_MOBILE', 'CREATED_BY', 'CREATED_AT', 'PLAYER_STATUS'];

}
