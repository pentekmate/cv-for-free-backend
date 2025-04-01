<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CV extends Model
{
    /** @use HasFactory<\Database\Factories\CVFactory> */
    use HasFactory;

    protected $table = 'cvs';

    protected $fillable = ['tier', 'user_id', 'userName', 'image', 'firstName', 'lastName', 'phoneNumber', 'email', 'country', 'city', 'jobTitle', 'introduce', 'age', 'ethnic'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function previousJobs(): HasMany
    {
        return $this->hasMany(PreviousJob::class, 'cv_id');
    }

    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class);
    }

    public function schools(): HasMany
    {
        return $this->hasMany(School::class);
    }

    public function socials(): HasMany
    {
        return $this->hasMany(Social::class);
    }

    public function languages(): HasMany
    {
        return $this->hasMany(Language::class);
    }

    public function licenses(): HasMany
    {
        return $this->hasMany(License::class);
    }
}
