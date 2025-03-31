<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use hasFactory;

    protected $fillable = ['skillName', 'skillLevel', 'cv_id'];

    public function cv()
    {
        return $this->belongsTo(Cv::class);
    }
}
