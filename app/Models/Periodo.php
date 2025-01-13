<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Periodo extends Model
{
    use HasFactory;

    protected $fillable = ['dataIni', 'dataFim'];

    // Relacionamento: Um período possui muitas escalas
    public function escalas()
    {
        return $this->hasMany(Escala::class, 'id_periodo');
    }
}
