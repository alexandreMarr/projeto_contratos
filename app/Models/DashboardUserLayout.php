<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DashboardUserLayout extends Model
{
    use HasFactory;

    protected $table = 'dashboard_user_layouts';

    protected $fillable = [
        'user_id',
        'nome',
        'layout_json',
        'filtros_json',
        'padrao',
        'ativo',
    ];

    protected $casts = [
        'layout_json' => 'array',
        'filtros_json' => 'array',
        'padrao' => 'boolean',
        'ativo' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
