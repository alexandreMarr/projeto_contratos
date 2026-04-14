<div class="card card-outline card-warning shadow-sm">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-lock mr-1"></i> Atualizar Senha
        </h3>
    </div>

    <div class="card-body">
        <p class="text-muted mb-4">Defina uma senha forte para proteger sua conta.</p>

        <form method="post" action="{{ route('password.update') }}">
            @csrf
            @method('put')

            <div class="form-group">
                <label for="update_password_current_password">Senha Atual</label>
                <div class="input-group">
                    <input id="update_password_current_password" name="current_password" type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" autocomplete="current-password">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#update_password_current_password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                @error('current_password', 'updatePassword')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="update_password_password">Nova Senha</label>
                <div class="input-group">
                    <input id="update_password_password" name="password" type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" autocomplete="new-password">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#update_password_password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <small id="password-strength-text" class="form-text text-muted">Digite uma nova senha.</small>
                @error('password', 'updatePassword')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="update_password_password_confirmation">Confirmar Nova Senha</label>
                <div class="input-group">
                    <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" autocomplete="new-password">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#update_password_password_confirmation">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                @error('password_confirmation', 'updatePassword')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
            </div>

            <div class="password-strength-bar mt-2 mb-4">
                <div id="password-strength-fill" class="password-strength-fill"></div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-warning px-4">
                    <i class="fas fa-key mr-1"></i> Atualizar senha
                </button>
            </div>
        </form>
    </div>
</div>
