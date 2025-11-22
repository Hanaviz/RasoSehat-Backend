<footer class="bg-gradient-to-br from-green-800 via-green-900 to-emerald-900 text-white pt-5 pb-2 shadow-2xl relative overflow-hidden">

    {{-- BG dekoratif --}}
    <div class="absolute inset-0 opacity-5">
        <div class="absolute top-0 left-0 w-96 h-96 bg-green-400 rounded-full filter blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-emerald-400 rounded-full filter blur-3xl"></div>
    </div>

    <div class="max-w-[1400px] mx-auto px-3 sm:px-4 lg:px-8 relative z-10">

        {{-- Grid Utama --}}
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-y-4 gap-x-4 border-b border-green-700/30 pb-5 mb-3">

            {{-- Kolom 1: Logo + Slogan --}}
            <div class="col-span-2 lg:col-span-1 space-y-2">
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <div class="absolute inset-0 bg-green-400 rounded-full blur-md opacity-50"></div>
                        <img
                            src="/logo-RasoSehat.png"
                            alt="RasoSehat Logo"
                            class="relative w-7 h-7 rounded-full bg-white p-0.5 shadow-xl ring-2 ring-green-400/50"
                        />
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold tracking-tight">RasoSehat</h3>
                        <p class="text-[10px] text-green-300 font-medium">Hidup Sehat, Hidup Bahagia</p>
                    </div>
                </div>

                <p class="text-xs text-green-100 leading-relaxed">
                    Platform Panduan Makanan Sehat di Padang.
                </p>

                <a 
                    href="https://github.com/Hanaviz/RasoSehat-Frontend.git"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-1 text-[10px] text-green-300 hover:text-white transition-all duration-300 font-medium group"
                >
                    {{-- Ikon GitHub (SVG asli) --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 group-hover:rotate-12 transition-transform duration-300" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 .5C5.65.5.5 5.65.5 12c0 5.1 3.29 9.43 7.86 10.96.58.1.79-.25.79-.56 0-.28-.01-1.03-.02-2.03-3.2.7-3.88-1.54-3.88-1.54-.53-1.36-1.3-1.72-1.3-1.72-1.06-.73.08-.72.08-.72 1.17.08 1.78 1.2 1.78 1.2 1.04 1.78 2.73 1.26 3.4.96.1-.75.41-1.26.75-1.55-2.55-.29-5.23-1.28-5.23-5.7 0-1.26.45-2.29 1.2-3.09-.12-.3-.52-1.52.11-3.17 0 0 .97-.31 3.18 1.18a11.1 11.1 0 0 1 2.9-.39c.98 0 1.97.13 2.9.39 2.2-1.49 3.17-1.18 3.17-1.18.63 1.65.23 2.87.11 3.17.75.8 1.19 1.83 1.19 3.09 0 4.43-2.68 5.41-5.24 5.69.42.36.8 1.09.8 2.2 0 1.59-.02 2.87-.02 3.26 0 .31.21.67.8.56A10.99 10.99 0 0 0 23.5 12c0-6.35-5.15-11.5-11.5-11.5Z"/>
                    </svg>

                    <span class="border-b border-green-300/50 group-hover:border-white/50">Repository Proyek RPL</span>
                </a>
            </div>

            {{-- Kolom 2: Navigasi --}}
            <div class="col-span-1 space-y-1.5">
                <h4 class="font-bold text-sm sm:text-base mb-2 relative inline-block">
                    Navigasi
                    <span class="absolute bottom-0 left-0 w-8 h-0.5 bg-green-400"></span>
                </h4>

                <nav class="space-y-0.5">
                    <a href="/" class="group flex items-center text-xs text-green-200 hover:text-white transition-all duration-200">
                        <span class="w-0 group-hover:w-1.5 h-0.5 bg-green-400 mr-0 group-hover:mr-1.5 transition-all duration-200"></span>
                        Beranda
                    </a>
                    <a href="/about" class="group flex items-center text-xs text-green-200 hover:text-white transition-all duration-200">
                        <span class="w-0 group-hover:w-1.5 h-0.5 bg-green-400 mr-0 group-hover:mr-1.5 transition-all duration-200"></span>
                        Tentang Kami
                    </a>
                    <a href="/categories" class="group flex items-center text-xs text-green-200 hover:text-white transition-all duration-200">
                        <span class="w-0 group-hover:w-1.5 h-0.5 bg-green-400 mr-0 group-hover:mr-1.5 transition-all duration-200"></span>
                        Cari Kategori
                    </a>
                    <a href="/register-store" class="group flex items-center text-xs text-green-200 hover:text-white transition-all duration-200">
                        <span class="w-0 group-hover:w-1.5 h-0.5 bg-green-400 mr-0 group-hover:mr-1.5 transition-all duration-200"></span>
                        Daftar Toko
                    </a>
                </nav>
            </div>

            {{-- Kolom 3: Contact --}}
            <div class="col-span-1 space-y-1.5">
                <h4 class="font-bold text-sm sm:text-base mb-2 relative inline-block">
                    Hubungi Kami
                    <span class="absolute bottom-0 left-0 w-8 h-0.5 bg-green-400"></span>
                </h4>

                <div class="space-y-1">

                    {{-- Mail --}}
                    <div class="group flex items-center gap-2 text-xs text-green-200 hover:text-white transition duration-200">
                        <div class="p-1 bg-green-800/50 rounded group-hover:bg-green-700/50">
                            <x-lucide name="mail" class="text-green-300 w-3 h-3" />
                        </div>
                        <span>contact@rasosehat.com</span>
                    </div>

                    {{-- Phone --}}
                    <div class="group flex items-center gap-2 text-xs text-green-200 hover:text-white transition duration-200">
                        <div class="p-1 bg-green-800/50 rounded group-hover:bg-green-700/50">
                            <x-lucide name="phone" class="text-green-300 w-3 h-3" />
                        </div>
                        <span>+62 812-XXXX-XXXX (WA)</span>
                    </div>

                    {{-- Map --}}
                    <div class="group flex items-start gap-2 text-xs text-green-200 hover:text-white transition duration-200">
                        <div class="p-1 bg-green-800/50 rounded group-hover:bg-green-700/50">
                            <x-lucide name="map-pin" class="text-green-300 w-3 h-3" />
                        </div>
                        <span>Kota Padang</span>
                    </div>
                </div>
            </div>

            {{-- Kolom 4: Tim --}}
            <div class="col-span-2 lg:col-span-2 space-y-2">
                <h4 class="font-bold text-sm sm:text-base mb-2 relative inline-block">
                    Tim Pengembang
                    <span class="absolute bottom-0 left-0 w-8 h-0.5 bg-green-400"></span>
                </h4>

                <ul class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-2 gap-y-2 gap-x-2">

                    @php
                        $teamMembers = [
                            ["name" => "Nori Dwi Yulianti", "role" => "Project Manager"],
                            ["name" => "Adib Al-Hakim", "role" => "Architect"],
                            ["name" => "Muhammad Aldo Mulyawan", "role" => "Designer (Lo-fi)"],
                            ["name" => "Azka Shalu Ramadhan", "role" => "Designer (Hi-fi)"],
                            ["name" => "Fadil Umma Suhada", "role" => "Developer (Frontend)"],
                            ["name" => "Hanaviz", "role" => "Developer (Backend)"],
                        ];
                    @endphp

                    @foreach ($teamMembers as $member)
                        <li class="group flex items-start gap-1 hover:translate-x-0.5 transition duration-200">
                            <div class="p-1 bg-green-800/50 rounded group-hover:bg-green-700/50 mt-0.5">
                                <x-lucide name="user" class="text-green-300 w-3 h-3" />
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs font-medium">{{ $member['name'] }}</span>
                                <span class="text-[9px] text-green-300 italic -mt-0.5">{{ $member['role'] }}</span>
                            </div>
                        </li>
                    @endforeach

                </ul>
            </div>
        </div>

        {{-- Copyright --}}
        <div class="text-center pt-2 border-t border-green-700/50 space-y-0.5">
            <p class="text-[9px] text-green-200 font-medium">
                © {{ date('Y') }} RasoSehat. Dibuat dengan <span class="text-red-400 animate-pulse">❤</span> untuk Tugas Proyek RPL.
            </p>
            <p class="text-[9px] text-green-300/80 max-w-2xl mx-auto leading-relaxed">
                <span class="font-semibold">Disclaimer:</span> Informasi di website ini merupakan panduan dan hasil verifikasi literatur. Konsultasikan dengan profesional medis/gizi untuk diet khusus.
            </p>
        </div>

    </div>
</footer>
