<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentWinner extends Model
{
    protected $table = 'tr_winners';
    public $timestamps = false;
    protected $primaryKey = 'ID';

    protected $fillable = ['ID','TOUR_ID','	TEAM_ID', 'RANK', 'PRIZE_MONEY', 'CREATED_BY', 'UPDATED_BY', 'CREATED_ON', 'UPDATED_ON'];

}
