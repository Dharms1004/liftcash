<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContestQuestions extends Model
{
    protected $table = "contest_questions";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    const UPDATED_AT = 'UPDATE_ON';
    const CREATED_AT = 'CREATED_ON';
    protected $primaryKey = 'QUESTION_ID';
    protected $fillable = ['CONTEST_ID','QUESTION','OPTION_A','OPTION_B','OPTION_C','OPTION_D','QUESTION_STATUS','CREATED_BY','UPDATED_BY'];

}
