<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DashboardWidget extends Model
{
    use HasFactory;

    protected $table = 'dashboard_widgets';

    protected $fillable = [
        'user_id',
        'titulo',
        'tipo',
        'metric_key',
        'configuracao',
        'cor',
        'icone',
        'ordem',
        'visivel_para_todos',
        'ativo',
    ];

    protected $casts = [
        'configuracao' => 'array',
        'visivel_para_todos' => 'boolean',
        'ativo' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
