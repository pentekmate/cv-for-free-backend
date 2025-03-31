<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    //
    public function cv()
    {
        return $this->belongsTo(Cv::class);
    }
}
