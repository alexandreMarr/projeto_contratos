<div class="card dashboard-panel shadow-sm border-0 mb-4 h-100">
    <div class="card-header border-0 bg-white d-flex align-items-center justify-content-between">
        <div>
            <h3 class="card-title font-weight-bold mb-0">{{ $title }}</h3>
            @isset($subtitle)
                <small class="text-muted">{{ $subtitle }}</small>
            @endisset
        </div>
        <span class="badge badge-light border">Gráfico</span>
    </div>
    <div class="card-body position-relative">
        <div class="chart-empty-state d-none" id="{{ $id }}Empty">Sem dados para o filtro atual.</div>
        <div style="height: 320px;">
            <canvas id="{{ $id }}"></canvas>
        </div>
    </div>
</div>
