<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $guarded = [];

    use HasFactory;

    public function produtos() {
        return $this->hasMany("App\Models\Produto");
    }
}
