<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{

    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        "link",
        "name"
    ];

    /**
     * @return HasMany
     */
    public function prices(): HasMany
    {
        return $this->hasMany(Price::class, 'link', 'link');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'link', 'link');
    }

}
