@props(['menu'])

@php
    // helper to safely get menu properties
    $get = fn($key, $default = null) => data_get($menu, $key, $default);
    $image = $get('image', $get('foto', '/storage/default-food.jpg'));
    $name = $get('name', $get('nama_menu', 'Menu RasoSehat'));
    $description = $get('description', $get('deskripsi', 'Menu sehat dengan bahan pilihan terbaik untuk kesehatan Anda.'));
    $rating = $get('rating', 4.5);
    $priceRaw = $get('price', $get('harga', 0));
    // ensure numeric price
    $priceNumber = is_numeric($priceRaw) ? (int)$priceRaw : (int)preg_replace('/\D/', '', (string)$priceRaw);
    $formattedPrice = 'Rp ' . number_format($priceNumber, 0, ',', '.');
    $whatsapp = $get('whatsappNumber', $get('no_whatsapp', '6281234567890'));
    $restaurantName = $get('restaurantName', $get('nama_restoran', 'Restoran RasoSehat'));
    $prepTime = $get('prepTime', $get('waktu_persiapan', '30-45m'));
    $slug = $get('slug', $get('id', '#'));
    $restaurantSlug = $get('restaurantSlug', $get('restoran_slug', $get('restoran_id', '#')));
    $isTrending = (bool)$get('isTrending', $get('is_trending', false));
    $isVerified = (bool)$get('isVerified', $get('status_verifikasi') === 'disetujui');
    $location = $get('locationCoords', $get('alamat', '')) ?: $restaurantName;
    $city = $get('city', 'Padang');
    $calories = $get('calories', rand(250, 550));

    if ($calories < 300) {
        $calorieLabel = 'Rendah';
        $calorieColor = 'bg-green-500';
    } elseif ($calories < 450) {
        $calorieLabel = 'Sedang';
        $calorieColor = 'bg-yellow-500';
    } else {
        $calorieLabel = 'Tinggi';
        $calorieColor = 'bg-orange-500';
    }
@endphp

<div
    x-data="{
        isLiked: false,
        imageLoaded: false,
        inView: false,
        like(e){ e.stopPropagation(); this.isLiked = !this.isLiked },
        contactWA(e){ e.stopPropagation(); const phone = '{{ $whatsapp }}'.replace(/[^0-9+]/g,''); const msg = encodeURIComponent(`Halo, saya tertarik dengan menu {{ addslashes($name) }} dari {{ addslashes($restaurantName) }}`); window.open(`https://wa.me/${phone}?text=${msg}`) },
        viewLocation(e){ e.stopPropagation(); const loc = encodeURIComponent('{{ $location }}'); window.open(`https://www.google.com/maps/search/?api=1&query=${loc}`, '_blank') },
        onImgLoad(){ this.imageLoaded = true },
        initIntersection(){
            const el = $el;
            const io = new IntersectionObserver((entries)=>{
                entries.forEach(en=>{ if(en.isIntersecting){ this.inView = true; io.unobserve(el); } })
            },{ threshold: 0.12 });
            io.observe(el);
            // replace lucide icons
            if(window.lucide) setTimeout(()=>window.lucide.replace(), 50);
        }
    }"
    x-init="initIntersection()"
    x-bind:class="inView ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-5'"
    class="bg-white rounded-2xl shadow-md overflow-hidden border border-gray-100 hover:shadow-2xl transition-all group flex flex-col relative transform will-change-transform duration-500 ease-out"
>
    {{-- Gambar & overlay --}}
    <a href="{{ url('/menu/' . $slug) }}" class="block relative shrink-0 cursor-pointer overflow-hidden bg-gray-100">
        {{-- Skeleton --}}
        <div x-show="!imageLoaded" class="absolute inset-0 bg-linear-to-r from-gray-200 via-gray-300 to-gray-200 animate-pulse"></div>

        <img :src="'{{ $image }}'" alt="{{ $name }}" x-on:load="onImgLoad()" class="w-full h-56 object-cover group-hover:scale-110 transition-transform duration-500" :class="imageLoaded ? 'opacity-100' : 'opacity-0'" />

        {{-- Gradient overlay --}}
        <div class="absolute inset-0 bg-linear-to-t from-black/50 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

        {{-- Top badges --}}
        <div class="absolute top-3 left-3 right-3 flex items-start justify-between">
            @if($isVerified)
                <div class="bg-linear-to-r from-green-500 to-green-600 text-white px-3 py-1.5 rounded-full shadow-lg flex items-center gap-1.5 text-xs font-bold backdrop-blur-sm" title="Menu Terverifikasi RasoSehat">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">Verified</span>
                </div>
            @endif

            <button x-on:click.stop.prevent="like($event)" x-bind:class="isLiked ? 'bg-red-500 text-white' : 'bg-white/90 text-gray-600 hover:bg-red-50 hover:text-red-500'" class="p-2 rounded-full shadow-lg backdrop-blur-md transition-all">
                <i data-lucide="heart" class="w-5 h-5" x-bind:class="isLiked ? 'fill-current' : ''"></i>
            </button>
        </div>

        {{-- Bottom info bar --}}
        <div class="absolute bottom-0 left-0 right-0 p-3 flex items-end justify-between">
            <div class="flex flex-col gap-1">
                <div class="{{ $calorieColor }} text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-lg backdrop-blur-sm flex items-center gap-1.5">
                    <i data-lucide="flame" class="w-4 h-4"></i>
                    <span>{{ $calories }} Kkal</span>
                </div>
                <span class="text-white text-[10px] font-semibold bg-black/40 backdrop-blur-sm px-2 py-0.5 rounded">Kalori {{ $calorieLabel }}</span>
            </div>

            <div class="bg-white/95 backdrop-blur-sm text-gray-800 px-3 py-1.5 rounded-lg shadow-lg flex items-center gap-1.5">
                <i data-lucide="star" class="w-4 h-4 text-yellow-400"></i>
                <span class="text-sm font-bold">{{ number_format($rating, 1) }}</span>
            </div>
        </div>
    </a>

    {{-- Detail konten --}}
    <div class="p-4 space-y-3 grow flex flex-col">
        <a href="{{ url('/menu/' . $slug) }}" class="cursor-pointer">
            <h3 class="font-bold text-gray-900 mb-1.5 text-base sm:text-lg group-hover:text-green-600 transition-colors line-clamp-1">{{ $name }}</h3>
            <p class="text-xs text-gray-600 mb-3 line-clamp-2 leading-relaxed">{{ $description }}</p>
        </a>

        <a href="{{ url('/restaurant/' . $restaurantSlug) }}" class="flex items-center gap-2 text-xs text-gray-600 hover:text-green-600 transition-colors cursor-pointer group/restaurant pb-3 border-b border-gray-100">
            <div class="p-1.5 bg-gray-100 rounded-lg group-hover/restaurant:bg-green-50 transition-colors">
                <i data-lucide="store" class="w-3.5 h-3.5"></i>
            </div>
            <span class="font-medium truncate">{{ $restaurantName }}</span>
            <div class="flex items-center gap-1 ml-auto shrink-0">
                <i data-lucide="clock" class="w-3 h-3"></i>
                <span class="font-medium">{{ $prepTime }}</span>
            </div>
        </a>

        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div class="flex flex-col">
                <span class="text-xs text-gray-500 font-medium">Kisaran Harga</span>
                <span class="text-green-600 font-bold text-xl">{{ $formattedPrice }}</span>
            </div>
            <div class="flex items-center gap-1 text-xs text-gray-600 bg-gray-50 px-3 py-2 rounded-lg">
                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-green-600"></i>
                <span class="font-medium">{{ $city }}</span>
            </div>
        </div>

        <div class="mt-auto pt-2">
            <div class="grid grid-cols-2 gap-2">
                <button x-on:click.stop.prevent="contactWA($event)" class="bg-linear-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold px-4 py-2.5 rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 text-sm">
                    <i data-lucide="phone" class="w-4 h-4"></i>
                    <span>Hubungi</span>
                </button>

                <button x-on:click.stop.prevent="viewLocation($event)" class="bg-white hover:bg-gray-50 text-gray-700 font-semibold px-4 py-2.5 rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 text-sm border-2 border-gray-200 hover:border-green-500">
                    <i data-lucide="navigation" class="w-4 h-4"></i>
                    <span>Lokasi</span>
                </button>
            </div>

            <p class="text-[10px] text-gray-500 text-center mt-2">Hubungi langsung penjual via WhatsApp</p>
        </div>

        @if($isTrending)
            <div class="flex items-center gap-1.5 text-xs font-semibold text-orange-600 bg-orange-50 px-3 py-1.5 rounded-lg">
                <i data-lucide="trending-up" class="w-3.5 h-3.5"></i>
                <span>Trending Hari Ini</span>
            </div>
        @endif
    </div>

    <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none rounded-2xl"></div>

    {{-- Inline scripts: Alpine dan Lucide --}}
    <script>
        // Load lucide if not loaded
        (function(){
            if(!window.lucide){
                const s=document.createElement('script');
                s.src='https://cdn.jsdelivr.net/npm/lucide@0.250.0/dist/lucide.min.js';
                s.onload = ()=> window.lucide && window.lucide.replace();
                document.head.appendChild(s);
            } else { window.lucide.replace(); }
        })();
        // Ensure Alpine is available; if not, load lightweight Alpine from CDN
        (function(){
            if(!window.Alpine){
                const a=document.createElement('script');
                a.src='https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js';
                a.defer=true; document.head.appendChild(a);
            }
        })();
    </script>

</div>