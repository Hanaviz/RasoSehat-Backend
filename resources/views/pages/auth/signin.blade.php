{{-- resources/views/pages/auth/signin.blade.php --}}

@extends('layouts.auth')

@section('title', 'Masuk Akun')

@section('content')
<div x-data="{ 
    showPassword: false,
    isLoading: false,
    isSuccess: false,
    
    // Fungsi submit disederhanakan untuk menggunakan form submission Laravel
    handleSubmit() {
        if (this.$refs.email.value === '' || this.$refs.password.value === '') {
            alert('Mohon isi email dan password');
            return;
        }
        this.isLoading = true;
        // Biarkan form submission ke Controller Laravel (POST /signin)
        this.$refs.loginForm.submit(); 
    }
}" 
    class="min-h-screen flex"
>
    
    {{-- Memanggil Inline Styles (dibiarkan di sini untuk demo, idealnya pindah ke CSS) --}}
    <style>
        @keyframes fadeUp { from { opacity: 0; transform: translateY(12px);} to { opacity: 1; transform: translateY(0);} }
        .fade-in-up { animation: fadeUp .5s cubic-bezier(.2,.9,.2,1) both; }
        .delay-1 { animation-delay: .06s; }
        .delay-2 { animation-delay: .14s; }
        .delay-3 { animation-delay: .22s; }
        .delay-4 { animation-delay: .30s; }
        .card-hover { transition: transform .28s cubic-bezier(.2,.9,.2,1), box-shadow .28s; will-change: transform; }
        .card-hover:hover { transform: translateY(-6px) scale(1.007); box-shadow: 0 18px 40px rgba(2,6,23,0.12); }
        .input-focus { transition: box-shadow .18s, transform .18s; }
        .input-focus:focus { box-shadow: 0 6px 20px rgba(16,185,129,0.12); transform: translateY(-1px); }
        .btn-press:active { transform: translateY(1px) scale(.995); }
        .decorative-blob { filter: blur(34px); opacity: .12; pointer-events: none; }
        @media (max-width: 1023px) { .decorative-blob { display: none; } }
    </style>

    {{-- Left Side - Hero Sidebar (Menggunakan Blade Component) --}}
    <x-hero-sidebar />

    {{-- Right Side - Sign In Form --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 bg-gray-50 relative">
        {{-- decorative blurred blob behind the form (desktop) --}}
        <div class="absolute -left-12 -top-12 w-44 h-44 rounded-full bg-gradient-to-tr from-green-300 to-green-500 decorative-blob" style="mix-blend-mode: screen" />
        <div class="w-full max-w-md">
            
            {{-- Mobile Logo --}}
            <div class="lg:hidden flex items-center justify-center gap-3 mb-8">
                <div class="w-12 h-12 bg-green-600 rounded-full flex items-center justify-center">
                    <svg viewBox="0 0 24 24" fill="none" class="w-8 h-8">
                        <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2M12 16C13.1 16 14 16.9 14 18C14 19.1 13.1 20 12 20C10.9 20 10 19.1 10 18C10 16.9 10.9 16 12 16M18 8C19.1 8 20 8.9 20 10C20 11.1 19.1 12 18 12C16.9 12 16 11.1 16 10C16 8.9 16.9 8 18 8M6 8C7.1 8 8 8.9 8 10C8 11.1 7.1 12 6 12C4.9 12 4 11.1 4 10C4 8.9 4.9 8 6 8Z" fill="white"/>
                        <circle cx="12" cy="10" r="6" stroke="white" stroke-width="1.5" fill="none"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">RasoSehat</h2>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-8 space-y-6 card-hover relative z-20">
                <div class="text-center space-y-2">
                    <h2 class="text-3xl font-bold text-gray-800 fade-in-up delay-1">Selamat Datang</h2>
                    <p class="text-gray-600 fade-in-up delay-2">Temukan makanan sehat di sekitar anda</p>
                </div>

                {{-- FORM LARAVEL --}}
                <form method="POST" action="{{ route('login') }}" x-ref="loginForm" @submit.prevent="handleSubmit">
                    @csrf
                    
                    <div class="space-y-5">
                        {{-- Field Email --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email
                            </label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                x-ref="email"
                                placeholder="contoh: sehat@gmail.com"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all outline-none input-focus fade-in-up delay-2 @error('email') border-red-500 @enderror"
                                required
                            />
                            @error('email')
                                <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Field Password --}}
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Password
                            </label>
                            <div class="relative">
                                <input
                                    id="password"
                                    name="password"
                                    :type="showPassword ? 'text' : 'password'" {{-- Alpine.js for show/hide --}}
                                    x-ref="password"
                                    placeholder="Masukan password anda"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all outline-none pr-12 input-focus fade-in-up delay-3 @error('password') border-red-500 @enderror"
                                    required
                                />
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword" {{-- Alpine.js toggle --}}
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 transition-colors"
                                >
                                    <template x-if="showPassword">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye-off"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c4.77 0 8.35 3.33 10 7-1.4 2.37-3.6 4.09-5.93 4.92"></path><path d="M2.72 2.72A10 10 0 0 0 2 12c1.74 3.67 5.32 7 10 7a10.43 10.43 0 0 0 5.82-1.48"></path><path d="m2 2 20 20"></path></svg>
                                    </template>
                                    <template x-if="!showPassword">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    </template>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Submit Button --}}
                        <button
                            type="submit"
                            :disabled="isLoading"
                            class="w-full bg-gradient-to-r from-green-600 to-green-500 text-white py-3 rounded-lg font-semibold hover:from-green-700 hover:to-green-600 transition-all duration-300 transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl btn-press fade-in-up delay-4"
                            x-on:click="isLoading = true"
                        >
                            <span x-show="isLoading" class="flex items-center justify-center gap-2">
                                <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Memproses...
                            </span>

                            <span x-show="!isLoading && !isSuccess">Submit</span>

                            <span x-show="isSuccess" class="flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="none">
                                    <path d="M6 10l2 2 6-6" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Berhasil
                            </span>
                        </button>

                        <div class="text-center">
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm text-green-600 hover:text-green-700 font-medium transition-colors">
                                    Lupa Password?
                                </a>
                            @else
                                {{-- Jika route tidak tersedia, tampilkan link ke URL reset default --}}
                                <a href="{{ url('/password/reset') }}" class="text-sm text-green-600 hover:text-green-700 font-medium transition-colors">
                                    Lupa Password?
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
                {{-- END FORM --}}

                <div class="pt-4 border-t border-gray-200 text-center">
                    <p class="text-gray-600 text-sm">
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="text-green-600 hover:text-green-700 font-semibold transition-colors">
                            Daftar sekarang
                        </a>
                    </p>
                </div>
            </div>

            <p class="text-center text-xs text-gray-500 mt-6">
                © 2025 RasoSehat. Semua hak dilindungi
            </p>
        </div>
    </div>
</div>
@endsection