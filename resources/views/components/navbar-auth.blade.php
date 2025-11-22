{{-- resources/views/components/navbar-auth.blade.php --}}

{{-- Data PHP untuk komponen (harus dipass dari view utama) --}}
@props(['userData', 'notifications'])

@php
    // Konversi mock data atau gunakan data asli yang dipass dari Controller
    $unreadCount = collect($notifications)->where('isRead', false)->count();

    // Pastikan URL logo menggunakan asset helper
    $logoSrc = asset('/logo-RasoSehat.png'); // Asumsi logo dipindahkan ke public/assets

    // Tentukan role/status user untuk menu
    $isStoreMember = $userData['isStoreMember'] ?? false;
@endphp

{{-- x-data Alpine.js menggantikan semua useState di React --}}
<div x-data="{ 
    isScrolled: false,
    isMobileMenuOpen: false,
    isMobileSearchOpen: false,
    showNotifications: false,
    showProfileMenu: false,
}" 
    {{-- Logic scroll menggantikan useEffect untuk scroll effect --}}
    x-init="
        window.addEventListener('scroll', () => { isScrolled = window.scrollY > 20 });

        // Logic handleClickOutside menggantikan useEffect untuk click outside
        $watch('showNotifications', value => { if (value) { document.addEventListener('click', e => { if (!e.target.closest('[x-ref=\'notificationRef\']')) showNotifications = false }, { once: true }) } });
        $watch('showProfileMenu', value => { if (value) { document.addEventListener('click', e => { if (!e.target.closest('[x-ref=\'profileRef\']')) showProfileMenu = false }, { once: true }) } });
        $watch('isMobileMenuOpen', value => { if (value) document.body.style.overflow = 'hidden'; else document.body.style.overflow = 'auto' });
        $watch('isMobileSearchOpen', value => { if (value) document.body.style.overflow = 'hidden'; else document.body.style.overflow = 'auto' });
    "
>

    {{-- Mobile Search Experience (Modal) --}}
    {{-- Menggantikan div dengan conditional opacity dan transform di React --}}
    <div 
        x-show="isMobileSearchOpen"
        class="fixed inset-0 z-[60] transition-all duration-300"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="display: none;"
    >
        {{-- Backdrop --}}
        <div 
            class="absolute inset-0 bg-black/20 backdrop-blur-sm"
            @click="isMobileSearchOpen = false"
        />

        {{-- Search Panel --}}
        <div 
            class="absolute inset-x-0 bottom-0 bg-white rounded-t-3xl shadow-lg transform transition-all duration-300 ease-out-expo"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
            x-transition:leave="ease-in duration-300"
            x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full"
        >
            <div class="flex items-center justify-between px-4 pt-3 pb-2">
                <div class="w-8" />
                <h2 class="text-lg font-semibold text-gray-800">Pencarian</h2>
                <button 
                  @click="isMobileSearchOpen = false"
                  class="p-2 -mr-2 hover:bg-gray-100 rounded-full transition-all duration-200"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
            </div>

            <div class="flex justify-center">
                <div class="w-10 h-1 bg-gray-200 rounded-full my-1" />
            </div>

            {{-- Search Logic Disederhanakan / Pindahkan ke Livewire --}}
            <form action="{{ route('search') }}" method="GET"> 
                <div class="px-4 pt-2 pb-4">
                    <div class="relative">
                        <input
                            type="text"
                            name="q" {{-- Parameter Query Laravel --}}
                            placeholder="Cari makanan sehat..."
                            class="w-full pl-12 pr-4 py-3.5 bg-gray-100 text-base rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:bg-white transition-all duration-200"
                            autofocus
                            {{-- Gunakan Livewire Component untuk search suggestion yang kompleks --}}
                        />
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </form> 
            
            {{-- Quick Access Section (Hanya HTML statis dari mock) --}}
            <div class="px-4 pb-6 max-h-[50vh] overflow-y-auto">
                {{-- Di sini tempat Livewire Search Results atau statis HTML untuk Quick Access --}}
                <div class="text-center p-4 text-gray-500">
                    Fitur Search Suggestion perlu diimplementasikan menggunakan Livewire.
                </div>
            </div>
        </div>
    </div>
    
    {{-- Mobile Menu Overlay (Sidebar Kanan) --}}
    <div 
        x-show="isMobileMenuOpen"
        class="fixed inset-0 bg-black/50 z-[55] transition-opacity duration-300"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="display: none;"
        @click="isMobileMenuOpen = false"
    >
        <div 
            class="fixed inset-y-0 right-0 w-[280px] bg-white shadow-xl transform transition-transform duration-300"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="ease-in duration-300"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            @click.stop
        >
            <div class="p-5">
                {{-- Header Menu --}}
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">Menu</h3>
                    <button 
                        @click="isMobileMenuOpen = false"
                        class="p-2 hover:bg-gray-100 rounded-lg transition-colors duration-200"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- User Info Mobile --}}
                <div class="mb-6 p-4 bg-green-50 rounded-xl">
                  <div class="flex items-center gap-3">
                    <img 
                      src="{{ $userData['avatar'] ?? 'https://ui-avatars.com/api/?name=User' }}" 
                      alt="{{ $userData['name'] ?? 'Pengguna' }}"
                      class="w-12 h-12 rounded-full ring-2 ring-green-500"
                    />
                    <div class="flex-1 min-w-0">
                      <h4 class="font-semibold text-gray-800 truncate">{{ $userData['name'] ?? 'Pengguna' }}</h4>
                      <p class="text-sm text-gray-500 truncate">{{ $userData['email'] ?? 'email@example.com' }}</p>
                    </div>
                  </div>
                </div>

                {{-- Menu Links Mobile --}}
                <div class="space-y-2">
                  <a
                    href="{{ route('profile') }}"
                    class="flex items-center gap-3 w-full px-4 py-3 text-left text-gray-700 hover:bg-gray-50 rounded-lg transition-colors duration-200"
                    @click="isMobileMenuOpen = false"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="font-medium">Profil Saya</span>
                  </a>

                  @if (!$isStoreMember)
                    <a
                      href="{{ route('register-store') }}"
                      class="flex items-center gap-3 w-full px-4 py-3 text-left text-green-600 bg-green-50 hover:bg-green-100 rounded-lg transition-colors duration-200"
                      @click="isMobileMenuOpen = false"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                      </svg>
                      <span class="font-semibold">Daftar Sebagai Penjual</span>
                    </a>
                  @else
                    <a
                      href="{{ route('my-store') }}"
                      class="flex items-center gap-3 w-full px-4 py-3 text-left text-gray-700 hover:bg-gray-50 rounded-lg transition-colors duration-200"
                      @click="isMobileMenuOpen = false"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                      </svg>
                      <span class="font-medium">Dashboard Toko</span>
                    </a>
                  @endif

                  <div class="border-t border-gray-200 my-2"></div>

                  <form method="POST" action="{{ route('logout') }}"> {{-- Ganti dengan route logout Laravel --}}
                    @csrf
                    <button
                      type="submit"
                      class="flex items-center gap-3 w-full px-4 py-3 text-left text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200"
                      @click="isMobileMenuOpen = false"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                      </svg>
                      <span class="font-medium">Keluar</span>
                    </button>
                  </form>
                </div>
            </div>
        </div>
    </div>


    {{-- MAIN NAVBAR START --}}
    <nav
      {{-- Menggantikan dynamic class React dengan x-bind:class Alpine.js --}}
      :class="{ 
          'bg-white shadow-lg': isScrolled,
          'bg-gradient-to-r from-green-500 via-green-600 to-green-500 shadow-md': !isScrolled 
      }"
      class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
    >
      <div class="w-full px-3 sm:px-4 md:px-6 lg:px-8 xl:max-w-[1400px] xl:mx-auto">
        <div
          :class="{
              'py-2 sm:py-2.5 md:py-3': isScrolled,
              'py-3 sm:py-4 md:py-5 lg:py-6': !isScrolled
          }"
          class="flex items-center justify-between gap-2 sm:gap-3 md:gap-4 lg:gap-6 transition-all duration-300"
        >
          {{-- Logo --}}
          <div class="flex items-center flex-shrink-0">
            <a
              href="{{ route('home') }}" {{-- Menggunakan helper route Laravel --}}
              class="flex items-center gap-2 sm:gap-3 md:gap-4 group cursor-pointer"
            >
              <div :class="{ 
                  'w-8 h-8 sm:w-9 sm:h-9 md:w-10 md:h-10': isScrolled,
                  'w-10 h-10 sm:w-11 sm:h-11 md:w-12 md:h-12': !isScrolled
              }" class="relative transition-all duration-300">
                <div :class="{
                    'ring-1 ring-green-500': isScrolled,
                    'ring-1 ring-white/90': !isScrolled
                }" class="w-full h-full bg-white rounded-full flex items-center justify-center overflow-hidden p-1">
                  <img 
                    src="{{ $logoSrc }}" 
                    alt="RasoSehat"
                    class="w-full h-full object-contain transform scale-125 transition-transform duration-300"
                  />
                </div>
              </div>

              <span :class="{
                  'text-gray-800 text-lg sm:text-xl md:text-2xl': isScrolled,
                  'text-white text-xl sm:text-2xl md:text-3xl': !isScrolled
              }" class="font-bold transition-all duration-300">
                RasoSehat
              </span>
            </a>
          </div>

          {{-- Search Bar - Desktop Only --}}
          <div class="hidden md:flex flex-1 justify-center min-w-0 max-w-[800px]">
            {{-- Tempatkan Livewire Component untuk Search dinamis di sini --}}
            {{-- <livewire:navbar-search /> --}}
            
            {{-- Placeholder Search Bar Statis --}}
            <form action="{{ route('search') }}" method="GET" class="w-full relative">
                <div class="flex items-center w-full transition-all duration-300 shadow-md hover:shadow-lg rounded-lg bg-white/90 backdrop-blur-md">
                    <div class="flex-shrink-0 pl-4 pr-2 transition-all duration-300">
                      <svg xmlns="http://www.w3.org/2000/svg" class="transition-all duration-300 text-gray-400 group-hover:text-gray-500 w-5 h-5 sm:h-5 sm:w-5 md:h-6 md:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 13.65z" />
                      </svg>
                    </div>
                    
                    <input
                      type="text"
                      name="q"
                      placeholder="Cari makanan sehat di sekitar Anda..."
                      class="flex-1 min-w-0 focus:outline-none bg-transparent transition-all duration-300 text-gray-600 placeholder-gray-400 px-2 py-3 text-base"
                    />

                    <button
                      type="submit"
                      class="flex-shrink-0 transition-all duration-300 flex items-center gap-2 mr-2 px-4 py-2 text-base text-gray-500 hover:text-green-600"
                    >
                      <span class="hidden">Cari</span>
                    </button>
                </div>
            </form>
          </div>

          {{-- Right Side: Actions --}}
          <div class="flex items-center gap-2 md:gap-3 flex-shrink-0">
            {{-- Mobile Search Button --}}
            <button 
              @click="isMobileSearchOpen = true"
              class="md:hidden relative p-2 rounded-lg hover:bg-white/10 active:scale-95 transform transition-all duration-200"
            >
              <svg xmlns="http://www.w3.org/2000/svg" 
                :class="isScrolled ? 'text-gray-800' : 'text-white'" 
                class="w-6 h-6" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </button>

            {{-- Store Button - Desktop Only --}}
            @if (!$isStoreMember)
              <a
                href="{{ route('register-store') }}"
                class="hidden md:flex group relative overflow-hidden transition-all duration-300 font-semibold whitespace-nowrap rounded-lg items-center gap-2 text-sm px-3 py-2 text-green-600 hover:shadow-lg bg-white/90 backdrop-blur-sm"
              >
                <span class="relative z-10">Daftarkan Toko</span>
              </a>
            @else
              <a
                href="{{ route('my-store') }}"
                class="hidden md:flex group relative overflow-hidden transition-all duration-300 font-semibold whitespace-nowrap rounded-lg items-center gap-2 text-sm px-3 py-2 text-green-600 hover:shadow-lg bg-white/90 backdrop-blur-sm"
              >
                <span class="relative z-10">Dashboard Toko</span>
              </a>
            @endif

            {{-- Notification Button - Desktop --}}
            <div class="hidden md:block relative" x-ref="notificationRef"> {{-- x-ref menggantikan useRef --}}
              <button
                @click="showNotifications = !showNotifications"
                :class="{ 
                  'hover:bg-gray-100 text-gray-700': isScrolled,
                  'hover:bg-white/10 text-white': !isScrolled
                }"
                class="relative p-2 rounded-lg transition-all duration-300"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                
                @if ($unreadCount > 0)
                  <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center animate-pulse">
                    {{ $unreadCount }}
                  </span>
                @endif
              </button>

              {{-- Notification Dropdown --}}
              <div 
                x-show="showNotifications"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-50 origin-top-right"
                style="display: none;"
              >
                <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-green-50 to-green-100">
                  <h3 class="font-semibold text-gray-800">Notifikasi</h3>
                  {{-- Logic markAllAsRead harus ada di Livewire atau AJAX --}}
                  <button type="button" class="text-sm text-green-600 hover:text-green-700 font-medium">
                    Tandai Semua Dibaca
                  </button>
                </div>
                
                <div class="max-h-96 overflow-y-auto">
                  @forelse ($notifications as $notification)
                    <button
                      {{-- MarkAsRead logic harus menggunakan AJAX/Livewire --}}
                      type="button"
                      class="w-full p-4 text-left hover:bg-gray-50 transition-colors duration-150 border-b border-gray-50 {{ !$notification['isRead'] ? 'bg-green-50/30' : '' }}"
                    >
                      <div class="flex gap-3">
                        <div class="flex-shrink-0 text-2xl">
                          {{ $notification['icon'] }}
                        </div>
                        <div class="flex-1 min-w-0">
                          <div class="flex items-start justify-between gap-2">
                            <h4 class="font-medium {{ !$notification['isRead'] ? 'text-gray-900' : 'text-gray-700' }}">
                              {{ $notification['title'] }}
                            </h4>
                            @if (!$notification['isRead'])
                              <span class="w-2 h-2 bg-green-500 rounded-full flex-shrink-0 mt-1.5"></span>
                            @endif
                          </div>
                          <p class="text-sm text-gray-500 mt-1 line-clamp-2">
                            {{ $notification['message'] }}
                          </p>
                          <p class="text-xs text-gray-400 mt-2">
                            {{ $notification['time'] }}
                          </p>
                        </div>
                      </div>
                    </button>
                  @empty
                    <div class="p-8 text-center text-gray-400">Tidak ada notifikasi</div>
                  @endforelse
                </div>
                
                <div class="p-3 border-t border-gray-100 bg-gray-50">
                  <a
                    href="{{ route('notifications') }}"
                    class="block text-center text-sm text-green-600 hover:text-green-700 font-medium"
                  >
                    Lihat Semua Notifikasi
                  </a>
                </div>
              </div>
            </div>

            {{-- Profile Button - Desktop --}}
            <div class="hidden md:block relative" x-ref="profileRef"> {{-- x-ref menggantikan useRef --}}
              <button
                @click="showProfileMenu = !showProfileMenu"
                :class="{ 
                  'hover:bg-gray-100': isScrolled,
                  'hover:bg-white/10': !isScrolled
                }"
                class="flex items-center gap-2 p-1.5 rounded-lg transition-all duration-300"
              >
                <img 
                  src="{{ $userData['avatar'] ?? 'https://ui-avatars.com/api/?name=User&background=16a34a&color=fff' }}" 
                  alt="{{ $userData['name'] ?? 'Pengguna' }}"
                  :class="{ 
                    'w-8 h-8 ring-green-500': isScrolled,
                    'w-9 h-9 ring-white': !isScrolled
                  }"
                  class="rounded-full ring-2 transition-all duration-300"
                />
                <svg xmlns="http://www.w3.org/2000/svg" 
                    :class="{ 
                      'rotate-180': showProfileMenu,
                      'text-gray-600': isScrolled,
                      'text-white': !isScrolled
                    }"
                    class="w-4 h-4 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>

              {{-- Profile Dropdown --}}
              <div 
                x-show="showProfileMenu"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-50 origin-top-right"
                style="display: none;"
              >
                <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-green-50 to-green-100">
                  <div class="flex items-center gap-3">
                    <img 
                      src="{{ $userData['avatar'] ?? 'https://ui-avatars.com/api/?name=User' }}" 
                      alt="{{ $userData['name'] ?? 'Pengguna' }}"
                      class="w-12 h-12 rounded-full ring-2 ring-green-500"
                    />
                    <div class="flex-1 min-w-0">
                      <h4 class="font-semibold text-gray-800 truncate">{{ $userData['name'] ?? 'Pengguna' }}</h4>
                      <p class="text-sm text-gray-500 truncate">{{ $userData['email'] ?? 'email@example.com' }}</p>
                    </div>
                  </div>
                </div>
                
                <div class="py-2">
                  <a
                    href="{{ route('profile') }}"
                    class="flex items-center gap-3 px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition-colors duration-150"
                    @click="showProfileMenu = false"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="font-medium">Profil Saya</span>
                  </a>

                  <a
                    href="{{ route('settings') }}"
                    class="flex items-center gap-3 px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition-colors duration-150"
                    @click="showProfileMenu = false"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="font-medium">Pengaturan</span>
                  </a>
                </div>

                <div class="border-t border-gray-100 py-2">
                  <form method="POST" action="{{ route('logout') }}"> {{-- Ganti dengan route logout Laravel --}}
                    @csrf
                    <button
                      type="submit"
                      class="flex items-center gap-3 px-4 py-2.5 text-red-600 hover:bg-red-50 transition-colors duration-150 w-full"
                      @click="showProfileMenu = false"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                      </svg>
                      <span class="font-medium">Keluar</span>
                    </button>
                  </form>
                </div>
              </div>
            </div>

            {{-- Mobile Menu Button --}}
            <button 
              @click="isMobileMenuOpen = true"
              class="md:hidden p-2 rounded-lg hover:bg-white/10 transition-all duration-300"
            >
              <svg xmlns="http://www.w3.org/2000/svg" :class="isScrolled ? 'text-gray-800' : 'text-white'" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </nav>
    {{-- MAIN NAVBAR END --}}
    
    {{-- Tambahkan custom style untuk animasi CSS --}}
    <style>
      @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
      }
      .animate-slideDown { animation: slideDown 0.2s ease-out; }
      /* Tambahkan class animasi fade-in/fade-out yang sama dengan transisi Alpine */
    </style>
</div>