<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setor extends Model
{
    use HasFactory;

    // atributos podem ser atribuídos em massa (fillable property)
    protected $fillable = ['nome', 'ativo'];

     // Relacionamento muitos-para-muitos com Setor
    public function funcionarios()
    {
        return $this->belongsToMany(Funcionario::class, 'funsetor', 'id_funcionario', 'id_setor');
    }

}
