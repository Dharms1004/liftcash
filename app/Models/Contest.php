<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contest extends Model
{
    protected $table = "contest";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    const UPDATED_AT = 'UPDATE_ON';
    const CREATED_AT = 'CREATED_ON';
    protected $primaryKey = 'CONTEST_ID';
    protected $fillable = ['CONTEST_NAME','CONTEST_TITLE','CONTEST_DESCRIPTION','CONTEST_VIDEO_URL','CONTEST_IMAGE_LINK','CONTEST_VIDEO_DESCRIPTION','CONTEST_STATUS','CONTEST_TERMS_CONDITIONS','CREATED_BY','UPDATED_BY'];

}
