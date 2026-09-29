@props([
    'label' => '',
    'value' => '0',
    'icon' => 'fas fa-chart-bar',
    'color' => 'primary',
    'subtitle' => null,
])

<div class="stat-card">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h6 class="text-muted mb-1">{{ $label }}</h6>
            <h3 class="mb-0">{{ $value }}</h3>
        </div>
        <div class="stat-icon bg-{{ $color }} text-white">
            <i class="{{ $icon }}"></i>
        </div>
    </div>
    @if($subtitle)
        <small class="text-muted">{{ $subtitle }}</small>
    @endif
</div>