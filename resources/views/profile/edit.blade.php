@extends('adminlte::page')

@section('title', 'Meu Perfil')

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
    <div>
        <h1 class="fw-bold text-primary mb-1">
            <i class="fas fa-user-circle mr-2"></i> Meu Perfil
        </h1>
        <p class="text-muted mb-0">Gerencie seus dados pessoais, foto e segurança da conta.</p>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid profile-page">
    @if (session('status') === 'profile-updated')
        <div class="alert alert-success shadow-sm">Perfil atualizado com sucesso.</div>
    @endif

    @if (session('status') === 'password-updated')
        <div class="alert alert-success shadow-sm">Senha atualizada com sucesso.</div>
    @endif

    <div class="row">
        <div class="col-lg-4">
            <div class="card card-outline card-primary shadow-sm profile-summary-card">
                <div class="card-body text-center">
                    <div class="profile-avatar-wrap mb-3">
                        <img src="{{ $user->imagem_perfil_url }}"
                            alt="Foto do perfil"
                            class="profile-avatar">
                    </div>

                    <h3 class="profile-name mb-1">{{ $user->name }}</h3>
                    <p class="text-muted mb-2">{{ $user->email }}</p>

                    <div class="mb-3">
                        @forelse($user->roles as $role)
                            <span class="badge badge-primary mr-1 mb-1">{{ $role->name }}</span>
                        @empty
                            <span class="badge badge-secondary">Sem perfil</span>
                        @endforelse
                    </div>

                    <div class="text-left mt-4">
                        <h6 class="text-uppercase text-muted small mb-2">Setores vinculados</h6>
                        <div>
                            @if(method_exists($user, 'setores') && $user->setores->count())
                                @foreach($user->setores as $setor)
                                    <span class="badge badge-info mr-1 mb-1">{{ $setor->nome }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">Nenhum setor vinculado.</span>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <div class="text-left">
                        <div class="profile-mini-info mb-2">
                            <strong>Última atualização:</strong>
                            <span>{{ optional($user->updated_at)->format('d/m/Y H:i') ?? '-' }}</span>
                        </div>
                        <div class="profile-mini-info">
                            <strong>Conta criada em:</strong>
                            <span>{{ optional($user->created_at)->format('d/m/Y H:i') ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            @include('profile.partials.update-profile-information-form')
            @include('profile.partials.update-password-form')
        </div>
    </div>
</div>
@stop

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const photoInput = document.getElementById('imagem_perfil');
    const preview = document.getElementById('photo-preview');

    if (photoInput && preview) {
        photoInput.addEventListener('change', function () {
            const file = this.files[0];
            const label = this.nextElementSibling;

            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);

                if (label) {
                    label.textContent = file.name;
                }
            }
        });
    }

    document.querySelectorAll('.toggle-password').forEach(function (button) {
        button.addEventListener('click', function () {
            const target = document.querySelector(this.dataset.target);
            const icon = this.querySelector('i');

            if (!target) return;

            if (target.type === 'password') {
                target.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                target.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    const passwordInput = document.getElementById('update_password_password');
    const strengthFill = document.getElementById('password-strength-fill');
    const strengthText = document.getElementById('password-strength-text');

    if (passwordInput && strengthFill && strengthText) {
        passwordInput.addEventListener('input', function () {
            const value = this.value;
            let score = 0;

            if (value.length >= 8) score++;
            if (/[A-Z]/.test(value)) score++;
            if (/[0-9]/.test(value)) score++;
            if (/[^A-Za-z0-9]/.test(value)) score++;

            const levels = [
                { width: '10%', text: 'Digite uma nova senha.', color: '#dc3545' },
                { width: '25%', text: 'Senha fraca', color: '#dc3545' },
                { width: '50%', text: 'Senha média', color: '#ffc107' },
                { width: '75%', text: 'Senha boa', color: '#17a2b8' },
                { width: '100%', text: 'Senha forte', color: '#28a745' }
            ];

            const level = levels[score];
            strengthFill.style.width = level.width;
            strengthFill.style.background = level.color;
            strengthText.textContent = level.text;
        });
    }
});
</script>
@endpush
