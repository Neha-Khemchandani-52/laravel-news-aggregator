<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_id',
        'fingerprint',
        'title',
        'description',
        'content',
        'url',
        'url_to_image',
        'author_id',
        'published_at',
        'raw',
    ];

    protected $casts = [
        'raw' => 'array',
        'published_at' => 'datetime',
    ];

    public function source()
    {
        return $this->belongsTo(Source::class);
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'article_category');
    }
}

