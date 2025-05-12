<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Tier;
class TierFeature extends Model
{
      protected $fillable = ['tier_id', 'label', 'value', 'is_checked'];

    public function tier()
    {
        return $this->belongsTo(Tier::class);
    }
}
