<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'imagem_perfil',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $appends = [
        'imagem_perfil_url',
    ];

    public function getImagemPerfilUrlAttribute(): string
    {
        if (!empty($this->imagem_perfil) && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->imagem_perfil)) {
            return url('storage/' . ltrim($this->imagem_perfil, '/')) . '?v=' . optional($this->updated_at)->timestamp;
        }

        return asset('vendor/adminlte/dist/img/Nova_364_azul.png');
    }

    public function adminlte_image()
    {
        return $this->imagem_perfil_url;
    }

    public function adminlte_desc()
    {
        return $this->roles->pluck('name')->join(', ') ?: 'Usuário do sistema';
    }

    public function adminlte_profile_url()
    {
        return route('profile.edit');
    }

    public function setores()
    {
        return $this->belongsToMany(Setor::class, 'setor_user', 'user_id', 'setor_id')
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
