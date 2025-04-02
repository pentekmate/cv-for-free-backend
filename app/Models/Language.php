<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Language extends Model
{
    //

    protected $fillable = ['languageName','languageLevel','cv_id'];
    public function cv():BelongsTo
    {
        return $this->belongsTo(Cv::class);
    }
}
