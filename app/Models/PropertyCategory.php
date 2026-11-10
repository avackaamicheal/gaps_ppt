<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PropertyCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'code_prefix',
        'description',
    ];

    /**
     * Get all properties in this category.
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'category_id');
    }
}
