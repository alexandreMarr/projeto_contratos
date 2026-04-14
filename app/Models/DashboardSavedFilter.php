<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DashboardSavedFilter extends Model
{
    use HasFactory;

    protected $table = 'dashboard_saved_filters';

    protected $fillable = [
        'user_id',
        'nome',
        'filtros_json',
        'publico',
        'ativo',
    ];

    protected $casts = [
        'filtros_json' => 'array',
        'publico' => 'boolean',
        'ativo' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
