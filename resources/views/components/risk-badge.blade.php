@props(['level' => 'low'])

<span {{ $attributes->merge(['class' => 'risk-badge risk-' . $level]) }}>
    {{ ucfirst($level) }}
</span>