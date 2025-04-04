<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tier extends Model
{
    protected $fillable = ["name", "pdf_limit"];


    public function users()
    {
        return $this->hasMany(User::class, 'tier_id'); // 'tier_id' a hivatkozás kulcs
    }
}
