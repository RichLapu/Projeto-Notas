<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Note extends Model
{
    use HasFactory, SoftDeletes;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($note) {
            $note->public_id = (string) Str::uuid();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}