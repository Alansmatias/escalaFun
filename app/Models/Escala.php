<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Escala extends Model
{
    use HasFactory;

    protected $fillable = [
        'dia',
        'id_funcionario',
        'id_setor',
        'id_turno',
        'id_periodo',
        'status',
        'observacao'
    ];

    // Relacionamento: Uma escala pertence a um funcionário
    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class, 'id_funcionario');
    }

    // Relacionamento: Uma escala pertence a um setor
    public function setor()
    {
        return $this->belongsTo(Setor::class, 'id_setor');
    }

    // Relacionamento: Uma escala pertence a um turno
    public function turno()
    {
        return $this->belongsTo(Turno::class, 'id_turno');
    }

    // Relacionamento: Uma escala pertence a um período
    public function periodo()
    {
        return $this->belongsTo(Periodo::class, 'id_periodo');
    }
}