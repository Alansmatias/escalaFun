<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    use HasFactory;

    // atributos podem ser atribuídos em massa (fillable property)
    protected $fillable = ['nome', 'ativo'];
}
