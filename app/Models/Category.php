<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // Permite que o Controller grave nestas colunas diretamente
    protected $fillable = ['name', 'color'];

    public function notes()
    {
        return $this->belongsToMany(Note::class);
    }
}
