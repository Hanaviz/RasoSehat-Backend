<div>
    <!-- Well begun is half done. - Aristotle -->
</div>
<!-- category.blade.php -->
@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 pt-24 md:pt-28 pb-12">
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header Kategori --}}
        <div class="mb-8 p-6 rounded-2xl shadow-xl border-t-8 {{ $categoryInfo['colorClass'] }} border-green-500/50 bg-white">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-800 flex items-center gap-3">
                <x-lucide-utensils class="w-8 h-8 text-green-600" /> {{ $categoryInfo['name'] }}
            </h1>
            <p class="text-gray-600 mt-3 text-base max-w-3xl">
                {{ $categoryInfo['description'] }}
            </p>
            <p class="text-sm font-semibold text-green-700 mt-3">
                Ditemukan <strong>{{ count($categoryInfo['results']) }}</strong> menu sehat di kategori ini.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            {{-- Filter Sidebar --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-5 space-y-4 sticky top-28 border border-gray-200">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2 border-b pb-3 mb-2">
                        <x-lucide-filter class="w-5 h-5"/> Saring Menu
                    </h3>

                    <div class="space-y-2">
                        <h4 class="font-semibold text-gray-700 text-sm">Rating Minimum:</h4>
                        <input type="range" min="0" max="5" step="0.5" class="w-full h-2 bg-gray-200 rounded-lg" />
                    </div>

                    <div class="space-y-2 pt-2 border-t border-gray-100">
                        <h4 class="font-semibold text-gray-700 text-sm">Tipe Makanan:</h4>
                        <select class="select select-sm select-bordered w-full text-sm bg-gray-50">
                            <option>Semua Tipe</option>
                            <option>Main Course</option>
                            <option>Snack / Dessert</option>
                            <option>Minuman</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Daftar Hasil --}}
            <div class="lg:col-span-3">
                @if(count($categoryInfo['results']) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($categoryInfo['results'] as $item)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition duration-300">
                        <a href="{{ url('/menu/' . $item['slug']) }}" class="block">
                            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-full h-40 object-cover" />
                        </a>
                        <div class="p-4 space-y-2">
                            <a href="{{ url('/menu/' . $item['slug']) }}">
                                <h3 class="text-lg font-bold text-gray-800 hover:text-green-600 transition">{{ $item['name'] }}</h3>
                            </a>

                            <div class="flex items-center justify-between text-sm text-gray-500">
                                <p class="font-semibold text-gray-700">{{ $item['restaurant'] }}</p>
                                <div class="px-2 py-0.5 rounded text-xs font-semibold {{ str_contains($item['healthTag'], 'Rendah') ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $item['healthTag'] }}
                                </div>
                            </div>

                            <div class="flex items-center justify-between border-t pt-2 border-gray-100">
                                <div class="flex items-center gap-1 text-yellow-500 text-sm">
                                    <x-lucide-star class="w-4 h-4 fill-current" />
                                    <span class="font-semibold text-gray-700">{{ $item['rating'] }}</span>
                                </div>
                                <p class="flex items-center gap-1 text-xs text-gray-600">
                                    <x-lucide-map-pin class="w-3 h-3" /> {{ $item['distance'] }} km
                                </p>
                                <p class="text-green-600 font-bold">Rp {{ $item['price'] }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center p-12 bg-white rounded-xl shadow-lg border-2 border-dashed border-gray-300">
                    <p class="text-xl font-semibold text-gray-700">Belum ada menu di kategori ini.</p>
                    <p class="text-gray-500 mt-2">Segera daftar menu Anda jika memenuhi kriteria <strong>{{ $categoryInfo['name'] }}</strong>!</p>
                </div>
                @endif

            </div>
        </div>

    </div>
</div>
@endsection