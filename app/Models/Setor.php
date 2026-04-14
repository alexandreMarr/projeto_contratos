<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setor extends Model
{
    use HasFactory;

    protected $table = 'setores';

    protected $fillable = [
        'nome',
        'descricao',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'setor_user', 'setor_id', 'user_id')
            ->withPivot([
                'pode_visualizar',
                'pode_editar',
                'pode_aprovar',
                'pode_reprovar',
                'ativo',
            ])
            ->withTimestamps();
    }
}
