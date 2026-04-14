<div class="dashboard-kpi dashboard-theme-{{ $card['tema'] ?? 'blue' }}">
    <div class="dashboard-kpi__icon">
        <i class="{{ $card['icone'] ?? 'fas fa-chart-pie' }}"></i>
    </div>
    <div class="dashboard-kpi__body">
        <div class="dashboard-kpi__label">{{ $card['titulo'] }}</div>
        <div class="dashboard-kpi__value">{{ $card['valor'] }}</div>
        <div class="dashboard-kpi__meta">{{ $card['descricao'] ?? '' }}</div>
    </div>
</div>
