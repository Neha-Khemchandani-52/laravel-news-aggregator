<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Author extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'bio', // if you add bio field later
    ];

    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}
