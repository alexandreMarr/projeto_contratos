<div class="card card-outline card-secondary">
    <div class="card-header"><h3 class="card-title">Histórico do Processo</h3></div>
    <div class="card-body p-0">
        <table class="table table-sm table-striped mb-0">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Evento</th>
                    <th>Descrição</th>
                    <th>Usuário</th>
                </tr>
            </thead>
            <tbody>
                @forelse($processo->historicos->sortByDesc('created_at') as $historico)
                    <tr>
                        <td>{{ $historico->created_at->format('d/m/Y H:i') }}</td>
                        <td><span class="badge badge-secondary">{{ $historico->tipo_evento }}</span></td>
                        <td>{{ $historico->descricao }}</td>
                        <td>{{ $historico->user->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted">Nenhum histórico encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
