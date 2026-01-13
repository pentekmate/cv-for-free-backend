<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    public function cv()
    {
        return $this->hasOne(CV::class, 'cv_type_id');
    }

    public function colors()
    {
        return $this->hasMany(Color::class);
    }

    protected $fillable = ['name', 'colors', 'PDF', 'img'];
}
