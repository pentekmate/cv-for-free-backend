<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Social extends Model
{
    //
    public function cv()
    {
        return $this->belongsTo(Cv::class);
    }
}
