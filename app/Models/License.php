<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    //
    protected $fillable = ['cv_id','type'];
    public function cv()
    {
        return $this->belongsTo(Cv::class);
    }
}
