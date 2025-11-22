@props(['name', 'class' => ''])

@php
    $path = base_path("node_modules/lucide/icons/{$name}.svg");
@endphp

@if(file_exists($path))
    {!! str_replace('<svg', '<svg class="'.$class.'"', file_get_contents($path)) !!}
@else
    <!-- icon not found -->
@endif
