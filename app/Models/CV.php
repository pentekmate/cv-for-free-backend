<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ReflectionClass;

class CV extends Model
{
    /** @use HasFactory<\Database\Factories\CVFactory> */
    use HasFactory;

    protected $table = 'cvs';

    // protected $fillable = ['tier', 'cv_type_id', 'user_id', 'userName', 'image', 'firstName', 'lastName', 'phoneNumber', 'email', 'country', 'city', 'jobTitle', 'introduce', 'age', 'ethnic'];
    protected $fillable = ['blob','cv_type_id','user_id'];
    public function type()
    {
        return $this->belongsTo(Template::class, 'cv_type_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @Relation
     */
    public function previousJobs(): HasMany
    {
        return $this->hasMany(PreviousJob::class, 'cv_id');
    }

    /**
     * @Relation
     */
    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class, 'cv_id');
    }

    /**
     * @Relation
     */
    public function schools(): HasMany
    {
        return $this->hasMany(School::class, 'cv_id');
    }

    /**
     * @Relation
     */
    public function socials(): HasMany
    {
        return $this->hasMany(Social::class, 'cv_id');
    }

    /**
     * @Relation
     */
    public function languages(): HasMany
    {
        return $this->hasMany(Language::class, 'cv_id');
    }

    /**
     * @Relation
     */
    public function licenses(): HasMany
    {
        return $this->hasMany(License::class, 'cv_id');
    }

    public static function getSupportedRelations()
    {
        $relations = [];
        $reflextionClass = new ReflectionClass(get_called_class());

        foreach ($reflextionClass->getMethods() as $method) {
            $doc = $method->getDocComment();

            if ($doc && strpos($doc, '@Relation') !== false) {
                $relations[] = $method->getName();
            }
        }

        return $relations;
    }

    public function scopeWithAll($query)
    {
        $query->with($this->getSupportedRelations());
    }
}
