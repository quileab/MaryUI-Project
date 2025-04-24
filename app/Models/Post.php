<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title',
        'content',
        'author',
        'category',
        'image',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'published' => 'Published',
            'archived' => 'Archived',
            default => 'Unknown',
        };
    }
}
