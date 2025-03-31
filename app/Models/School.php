<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    //
    public function cv()
    {
        return $this->belongsTo(Cv::class);
    }
}
