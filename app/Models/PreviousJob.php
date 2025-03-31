<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreviousJob extends Model
{
    use hasFactory;

    protected $fillable = [
        'cv_id', 'employer', 'jobTitle', 'startDate', 'endDate', 'description', 'city',
    ];

    public function cv()
    {
        return $this->belongsTo(Cv::class);
    }
}
