@props(['name', 'class' => ''])

@php
    $iconPath = base_path("node_modules/lucide/icons/{$name}.svg");
@endphp

@if(file_exists($iconPath))
    {!! str_replace('<svg', '<svg class="'.$class.'"', file_get_contents($iconPath)) !!}
@else
    <!-- Ikon tidak ditemukan -->
@endif
