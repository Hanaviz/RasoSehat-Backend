{{-- resources/views/pages/public/herosection.blade.php --}}

@extends('layouts.app')

@section('content')

    @php
        // =====================================================================
        // MOCK DATA SEMENTARA (jika Controller belum mengirimkan data)
        // =====================================================================
        $menus = $menus ?? [
            [
                'id' => 1,
                'slug' => 'nasi-gule-sehat',
                'name' => 'Nasi Gule Sehat',
                'description' => 'Nasi gule bergizi, rendah gula dan lemak sehat untuk energi harian.',
                'rating' => 4.7,
                'price' => 25000,
                'image' => '/storage/mock-food-1.jpg',
                'whatsappNumber' => '6281234567890',
                'restaurantName' => 'Warung Sehat',
                'restaurantSlug' => 'warung-sehat',
                'prepTime' => '25m',
                'isTrending' => true,
                'isVerified' => true,
                'city' => 'Padang',
                'calories' => 320,
                'category_slug' => 'main-course'
            ],
            [
                'id' => 2,
                'slug' => 'salad-buah-bugar',
                'name' => 'Salad Buah Bugar',
                'description' => 'Campuran buah segar, dressing rendah gula.',
                'rating' => 4.3,
                'price' => 18000,
                'image' => '/storage/mock-food-2.jpg',
                'whatsappNumber' => '628119999999',
                'restaurantName' => 'Fresh Corner',
                'restaurantSlug' => 'fresh-corner',
                'prepTime' => '10m',
                'isTrending' => false,
                'isVerified' => false,
                'city' => 'Padang',
                'calories' => 220,
                'category_slug' => 'salad-bowl'
            ],
            [
                'id' => 3,
                'slug' => 'soto-sehat-ku',
                'name' => 'Soto Sehat Ku',
                'description' => 'Soto hangat dengan kaldu rendah lemak, penuh rempah.',
                'rating' => 4.9,
                'price' => 30000,
                'image' => '/storage/mock-food-3.jpg',
                'whatsappNumber' => '628117777777',
                'restaurantName' => 'Soto Mantap',
                'restaurantSlug' => 'soto-mantap',
                'prepTime' => '35m',
                'isTrending' => true,
                'isVerified' => true,
                'city' => 'Padang',
                'calories' => 480,
                'category_slug' => 'main-course'
            ],
        ];

        $allCategories = $allCategories ?? [
            ['name' => 'Rendah Gula', 'icon' => '🍯', 'slug' => 'rendah-gula'],
            ['name' => 'Rendah Kalori', 'icon' => '🥒', 'slug' => 'rendah-kalori'],
            ['name' => 'Main Course', 'icon' => '🥩', 'slug' => 'main-course'],
            ['name' => 'Salad & Bowl', 'icon' => '🥗', 'slug' => 'salad-bowl'],
        ];

        $menusGrouped = collect($menus)->groupBy('category_slug');

        $menusByCategories = [];

        foreach ($menusGrouped as $slug => $items) {
            $categoryInfo = collect($allCategories)->firstWhere('slug', $slug);

            $menusByCategories[] = [
                'title' => $categoryInfo['name'] ?? ucfirst(str_replace('-', ' ', $slug)),
                'slug' => $slug,
                'items' => $items->toArray()
            ];
        }

        $heroSlides = $heroSlides ?? [
            [
                'id' => 1,
                'image' => 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?w=1920&h=1080&fit=crop',
                'slogan' => "Padang Penuh Rasa.",
                'subtext' => "Pilihan menu rendah kolesterol."
            ],
        ];

    @endphp

    {{-- =====================================================================
    HERO SECTION + CAROUSEL + ALPINE LOGIC
    ===================================================================== --}}

    <div x-data="{
            currentSlide: 0,
            location: '',
            showMapModal: false,
            selectedCoords: null,
            showAllCategories: false,
            mapError: '',
            mapLoading: false,

            heroSlidesCount: {{ count($heroSlides ?? []) }},

            nextSlide() {
                this.currentSlide = (this.currentSlide + 1) % this.heroSlidesCount;
            },
            prevSlide() {
                this.currentSlide = (this.currentSlide - 1 + this.heroSlidesCount) % this.heroSlidesCount;
            },
            goToSlide(i) {
                this.currentSlide = i;
            },
            handleExplore() {
                if (this.location.trim()) {
                    window.location.href = `{{ route('search') }}?loc=${encodeURIComponent(this.location.trim())}`;
                }
            },
            handleGetCurrentLocation() {
                this.location = 'Limau Manih, Padang';
            },
            handleOpenMapModal() {
                this.showMapModal = true;
                this.$nextTick(() => window.initMapLogic(this));
            },
            handleCloseMapModal() {
                this.showMapModal = false;
            }
        }" x-init="
            setInterval(() => {
                currentSlide = (currentSlide + 1) % heroSlidesCount;
            }, 5000);

            window.createSlug = name => name
                .toLowerCase()
                .replace(/\s+/g, '-')
                .replace(/[^\w-]+/g, '');
        " class="pt-16 sm:pt-20 md:pt-24 lg:pt-28 bg-gradient-to-b from-green-50 to-white pb-8">

        {{-- ============================================================
        HERO CAROUSEL
        ============================================================ --}}
        <div class="max-w-[1400px] mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
            <div class="relative mb-16 sm:mb-20 md:mb-24">
                <div class="relative rounded-2xl overflow-hidden shadow-xl group">

                    {{-- SLIDES --}}
                    <div class="relative h-48 sm:h-64 md:h-72 lg:h-80 overflow-hidden">
                        @foreach ($heroSlides as $slide)
                            <div x-cloak x-show="currentSlide === {{ $loop->index }}"
                                x-transition:enter="transition ease-in-out duration-700"
                                x-transition:enter-start="opacity-0 translate-x-full"
                                x-transition:enter-end="opacity-100 translate-x-0"
                                x-transition:leave="transition ease-in-out duration-700"
                                x-transition:leave-start="opacity-100 translate-x-0"
                                x-transition:leave-end="opacity-0 -translate-x-full" class="absolute inset-0">
                                <img src="{{ $slide['image'] }}" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/40"></div>

                                <div class="absolute inset-0 flex flex-col justify-center items-center text-center p-4 sm:p-8">
                                    <h2
                                        class="text-xl sm:text-3xl md:text-4xl font-serif font-black text-white drop-shadow-lg max-w-2xl">
                                        {{ $slide['slogan'] }}
                                    </h2>
                                    <p class="mt-2 text-sm sm:text-base font-semibold italic text-green-200 max-w-xl">
                                        {{ $slide['subtext'] }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- PREV & NEXT --}}
                    <button @click="prevSlide()"
                        class="absolute left-2 sm:left-4 top-1/2 -translate-y-1/2 p-2 bg-white/90 hover:bg-white rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-all">←</button>

                    <button @click="nextSlide()"
                        class="absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 p-2 bg-white/90 hover:bg-white rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-all">→</button>

                    {{-- DOTS --}}
                    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
                        @foreach ($heroSlides as $slide)
                            <button @click="goToSlide({{ $loop->index }})"
                                :class="currentSlide === {{ $loop->index }} ? 'bg-white w-6 h-2' : 'bg-white/50 w-2 h-2'"
                                class="rounded-full transition-all duration-300"></button>
                        @endforeach
                    </div>
                </div>

                {{-- ============================================================
                LOCATION CHOOSER
                ============================================================ --}}
                <div class="max-w-md mx-auto -mt-12 relative z-10">
                    <div class="bg-white rounded-xl shadow-2xl p-6 sm:p-8">
                        <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-4 text-center">Cari Makanan di Sekitar Anda
                        </h3>

                        <form @submit.prevent="handleExplore">
                            <div class="flex gap-2">
                                <div class="relative flex-1">
                                    <input type="text" x-model="location" @click="handleOpenMapModal()"
                                        placeholder="Masukkan Area / Kecamatan"
                                        class="w-full pl-10 pr-4 py-2 sm:py-3 border rounded-lg cursor-pointer" readonly />
                                </div>

                                <button type="submit"
                                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold shadow-lg">
                                    Cari
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- ============================================================
                GOOGLE MAPS MODAL
                ============================================================ --}}
                <div x-cloak x-show="showMapModal"
                    class="fixed inset-0 bg-black/50 z-[100] flex items-center justify-center p-4">
                    <div @click.outside="handleCloseMapModal()"
                        class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl flex flex-col max-h-[90vh]">

                        <div class="p-6 border-b flex items-center justify-between">
                            <h2 class="text-2xl font-bold">Pilih Lokasi dari Peta</h2>
                            <button @click="handleCloseMapModal()" class="p-2 rounded-lg hover:bg-gray-100">✕</button>
                        </div>

                        <div class="flex-1 overflow-hidden">
                            <div id="map" class="w-full min-h-[400px]"></div>
                        </div>

                        <div class="p-6 border-t flex gap-3 bg-gray-50">
                            <button @click="handleCloseMapModal()" class="flex-1 border rounded-lg py-2">Batal</button>

                            <button @click="window.handleUseMapLocationJS()" :disabled="!selectedCoords"
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white rounded-lg py-2">Gunakan Lokasi
                                Ini</button>
                        </div>

                    </div>
                </div>

            </div>

            {{-- ============================================================
            CATEGORIES
            ============================================================ --}}
            <div class="bg-white rounded-2xl shadow-md p-6 mb-6 -mt-8 border-2 border-green-200">
                <h2 class="text-xl font-bold mb-4">Kategori</h2>

                <div class="grid grid-cols-4 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-4">
                    @foreach ($allCategories as $category)
                        <a x-show="showAllCategories || {{ $loop->index < 8 ? 1 : 0 }}"
                            :href="'{{ route('category.show', ['categorySlug' => '__SLUG__']) }}'.replace('__SLUG__', window.createSlug('{{ $category['name'] }}'))"
                            class="flex flex-col items-center bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-xl shadow hover:shadow-lg transition">
                            <div class="text-3xl mb-2">{{ $category['icon'] ?? '🍽️' }}</div>
                            <span class="text-sm font-medium">{{ $category['name'] }}</span>
                        </a>
                    @endforeach
                </div>

                <div class="text-center mt-4">
                    <button @click="showAllCategories = !showAllCategories" class="text-green-600 font-semibold">
                        <span x-show="!showAllCategories">Show More</span>
                        <span x-show="showAllCategories">Show Less</span>
                    </button>
                </div>
            </div>

            {{-- ============================================================
            FEATURED TEXT
            ============================================================ --}}
            <div class="text-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Temukan makanan sehat terbaik di Padang!</h1>
                <p class="text-gray-600">Nikmati pilihan kuliner sehat, bergizi, dan dekat dengan lokasi Anda.</p>
            </div>

            {{-- ============================================================
            FOOD CARDS BY CATEGORY
            ============================================================ --}}
            @foreach ($menusByCategories as $cat)
                <div class="bg-white rounded-2xl shadow-md p-6 border-2 border-green-200 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-2xl font-bold text-green-600">{{ $cat['title'] }}</h2>

                        <a href="{{ route('category.show', ['categorySlug' => $cat['slug']]) }}"
                            class="text-green-600 hover:text-green-700">
                            Lihat Semua
                        </a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($cat['items'] as $menu)
                            <x-hero-menu-card :menu="$menu" />
                        @endforeach
                    </div>
                </div>
            @endforeach

        </div>
    </div>


    {{-- ============================================================
    GOOGLE MAPS SCRIPT HELPERS
    ============================================================ --}}
    <script>
        let mapInstance = null;
        let markerInstance = null;

        window.initMapLogic = (alpine) => {
            if (!window.google || !window.google.maps) {
                alpine.mapError = "Google Maps belum dimuat.";
                return;
            }

            const initialCoords = { lat: -0.9471, lng: 100.4172 };
            alpine.selectedCoords = initialCoords;

            mapInstance = new google.maps.Map(document.getElementById('map'), {
                zoom: 13,
                center: initialCoords,
            });

            markerInstance = new google.maps.Marker({
                position: initialCoords,
                map: mapInstance,
                draggable: true
            });

            google.maps.event.addListener(mapInstance, "click", (e) => {
                markerInstance.setPosition(e.latLng);
                alpine.selectedCoords = { lat: e.latLng.lat(), lng: e.latLng.lng() };
            });

            google.maps.event.addListener(markerInstance, "dragend", () => {
                const pos = markerInstance.getPosition();
                alpine.selectedCoords = { lat: pos.lat(), lng: pos.lng() };
            });
        };

        window.handleUseMapLocationJS = () => {
            const component = document.querySelector("[x-data]");
            const alpine = component.__x.$data;

            if (!alpine.selectedCoords) return;

            alpine.location = `${alpine.selectedCoords.lat.toFixed(4)}, ${alpine.selectedCoords.lng.toFixed(4)}`;
            alpine.showMapModal = false;
        };
    </script>

@endsection