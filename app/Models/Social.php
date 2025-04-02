<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Social extends Model
{
    //
    protected $fillable = ['cv_id','name','link'];
    public function cv()
    {
        return $this->belongsTo(Cv::class);
    }
}
