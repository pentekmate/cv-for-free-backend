<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    //
    public function cv()
    {
        return $this->belongsTo(Cv::class);
    }
}
