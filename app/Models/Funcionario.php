<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Funcionario extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'telefone', 'contrato', 'domingo', 'ativo'];

    // Define uma relação "um para muitos" (one-to-many) com FunFolga
    public function funFolgas()
    {
        return $this->hasMany(FunFolga::class, 'id_funcionario');
    }

    // Relacionamento muitos-para-muitos com Setor
    public function setores()
    {
        return $this->belongsToMany(Setor::class, 'funsetor', 'id_funcionario', 'id_setor');
    }

    // Relacionamento muitos-para-muitos com Setor
    public function turnos()
    {
        return $this->belongsToMany(Turno::class, 'funturno', 'id_funcionario', 'id_turno');
    }

    // Relacionamento um para muitos com Escala
    public function escalas()
    {
        return $this->hasMany(Escala::class, 'id_funcionario');
    }
}
