<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class School extends Model
{
    protected $fillable = ['schoolName', 'degree', 'beginDate', 'endDate', 'city', 'cv_id'];

    public function cv(): BelongsTo
    {
        return $this->belongsTo(Cv::class);
    }
}
