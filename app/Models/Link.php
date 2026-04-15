<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;

    protected $fillable = ['slug', 'original_url'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
