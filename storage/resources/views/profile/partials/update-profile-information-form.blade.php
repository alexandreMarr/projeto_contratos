<div class="card card-outline card-primary shadow-sm mb-4">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-id-card mr-1"></i> Informações do Perfil
        </h3>
    </div>

    <div class="card-body">
        <p class="text-muted mb-4">Atualize seu nome, email e foto de perfil.</p>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="name">Nome</label>
                        <input id="name" name="name" type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}" required autocomplete="username">
                        @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="imagem_perfil">Foto de Perfil</label>
                <div class="custom-file">
                    <input type="file" class="custom-file-input @error('imagem_perfil') is-invalid @enderror" id="imagem_perfil" name="imagem_perfil" accept="image/*">
                    <label class="custom-file-label" for="imagem_perfil">Escolher arquivo</label>
                </div>
                @error('imagem_perfil')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
            </div>

            <div class="row align-items-center mt-3">
                <div class="col-md-3">
                    <div class="profile-preview-box">
                        <img id="photo-preview" src="{{ $user->imagem_perfil_url }}" class="profile-preview-img" alt="Preview">
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="alert alert-light border mb-0">
                        <strong>Dica:</strong> use uma foto com boa iluminação e enquadramento central.
                        @if($user->imagem_perfil)
                            <div class="mt-2">
                                <button type="submit" name="remove_photo" value="1" class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-trash mr-1"></i> Remover foto atual
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-save mr-1"></i> Salvar dados
                </button>
            </div>
        </form>
    </div>
</div>
