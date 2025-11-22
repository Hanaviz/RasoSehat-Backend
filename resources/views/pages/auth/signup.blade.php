{{-- resources/views/pages/auth/signup.blade.php --}}

@extends('layouts.auth')

@section('title', 'Daftar Akun Baru')

@section('content')
<div x-data="{
    // Variabel Alpine.js menggantikan formData dan state lainnya
    showPassword: false,
    isLoading: false,
    
    // Inisialisasi data form untuk validasi front-end (opsional, tapi berguna)
    formData: {
        nama: '{{ old('name') }}',
        email: '{{ old('email') }}', // Asumsi Nomor HP / Email menggunakan input 'email'
        password: '',
        password_confirmation: '',
    },

    // Handle submit (menggantikan handleSubmit React)
    handleSubmit() {
        this.isLoading = true;
        this.$refs.registerForm.submit(); // Submit form ke Controller Laravel
    }
}"
    {{-- Animasi transisi yang disimulasikan dari framer-motion --}}
    class="min-h-screen flex"
>
    
    {{-- Inline styles untuk animasi CSS (fade-in-up) --}}
    <style>
        .fade-in-up { animation: fadeUp .5s cubic-bezier(.2,.9,.2,1) both; }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(12px);} to { opacity: 1; transform: translateY(0);} }
        .card-hover { transition: transform .28s cubic-bezier(.2,.9,.2,1), box-shadow .28s; will-change: transform; }
        .card-hover:hover { transform: translateY(-6px) scale(1.007); box-shadow: 0 18px 40px rgba(2,6,23,0.12); }
        .input-focus { transition: box-shadow .18s, transform .18s; }
        .input-focus:focus { box-shadow: 0 6px 20px rgba(16,185,129,0.12); transform: translateY(-1px); }
        .btn-press:active { transform: translateY(1px) scale(.995); }
        .decorative-blob { filter: blur(34px); opacity: .12; pointer-events: none; }
        @media (max-width: 1023px) { .decorative-blob { display: none; } }
    </style>

    {{-- Left Side - Hero Sidebar (Blade Component) --}}
    <x-hero-sidebar />

    {{-- Right Side - Sign Up Form --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 bg-gray-50 relative">
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
                    <h2 class="text-3xl font-bold text-gray-800 fade-in-up">Daftar Sekarang</h2>
                    <p class="text-gray-600 fade-in-up">
                        Sudah punya akun RasoSehat?
                        <a href="{{ route('login') }}" class="text-green-600 hover:text-green-700 font-semibold transition-colors">
                            Masuk
                        </a>
                    </p>
                </div>

                {{-- FORM LARAVEL REGISTER --}}
                <form method="POST" action="{{ route('register') }}" x-ref="registerForm" @submit.prevent="handleSubmit">
                    @csrf
                    
                    <div class="space-y-5">
                        {{-- Nama --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama
                            </label>
                            <input
                                id="name"
                                name="name"
                                type="text"
                                value="{{ old('name') }}"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all outline-none @error('name') border-red-500 @enderror"
                                required
                            />
                            @error('name')
                                <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tanggal Lahir (Disimpan sebagai 3 input, tapi Laravel biasanya butuh 1 field) --}}
                        {{-- Di sini kita hanya menangani input terpisah untuk UI, dan kita bisa menggunakan 3 field berbeda di database atau gabungkan di backend. --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Lahir
                            </label>
                            <div class="grid grid-cols-3 gap-3">
                                {{-- Jika Anda menggunakan field terpisah di DB: tanggal_lahir_day, month, year --}}
                                <input
                                    type="number"
                                    name="tanggal_lahir_day"
                                    placeholder="DD"
                                    value="{{ old('tanggal_lahir_day') }}"
                                    class="px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all outline-none text-center @error('tanggal_lahir') border-red-500 @enderror"
                                    min="1"
                                    max="31"
                                />
                                <input
                                    type="number"
                                    name="tanggal_lahir_month"
                                    placeholder="MM"
                                    value="{{ old('tanggal_lahir_month') }}"
                                    class="px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all outline-none text-center @error('tanggal_lahir') border-red-500 @enderror"
                                    min="1"
                                    max="12"
                                />
                                <input
                                    type="number"
                                    name="tanggal_lahir_year"
                                    placeholder="YYYY"
                                    value="{{ old('tanggal_lahir_year') }}"
                                    class="px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all outline-none text-center @error('tanggal_lahir') border-red-500 @enderror"
                                    min="1900"
                                    max="{{ date('Y') }}"
                                />
                            </div>
                            @error('tanggal_lahir')
                                <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Jenis Kelamin --}}
                        <div>
                            <label for="jenisKelamin" class="block text-sm font-medium text-gray-700 mb-2">
                                Jenis Kelamin
                            </label>
                            <div class="relative">
                                <select
                                    id="jenisKelamin"
                                    name="jenis_kelamin"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all outline-none appearance-none bg-white @error('jenis_kelamin') border-red-500 @enderror"
                                    required
                                >
                                    <option value="">Pilih jenis kelamin</option>
                                    <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none"><path d="m6 9 6 6 6-6"></path></svg>
                            </div>
                            @error('jenis_kelamin')
                                <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email (Di SignUp.jsx ini adalah Nomor HP / Email) --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email
                            </label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all outline-none @error('email') border-red-500 @enderror"
                                placeholder="contoh: sehat@gmail.com"
                                required
                            />
                            @error('email')
                                <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        {{-- Nomor HP (Opsional, tapi penting) --}}
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Nomor HP (Opsional)
                            </label>
                            <input
                                id="phone"
                                name="phone"
                                type="tel"
                                value="{{ old('phone') }}"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all outline-none @error('phone') border-red-500 @enderror"
                                placeholder="08xxxxxxxxxx"
                            />
                            @error('phone')
                                <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Kata Sandi --}}
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Kata Sandi
                            </label>
                            <div class="relative">
                                <input
                                    id="password"
                                    name="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all outline-none pr-12 @error('password') border-red-500 @enderror"
                                    required
                                />
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
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
                        
                        {{-- Konfirmasi Kata Sandi (Wajib untuk validasi Laravel) --}}
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Konfirmasi Kata Sandi
                            </label>
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                :type="showPassword ? 'text' : 'password'"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all outline-none pr-12"
                                required
                            />
                        </div>


                        {{-- Submit Button --}}
                        <button
                            type="submit"
                            :disabled="isLoading"
                            class="w-full bg-gradient-to-r from-green-600 to-green-500 text-white py-3 rounded-lg font-semibold hover:from-green-700 hover:to-green-600 transition-all duration-300 transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl btn-press"
                            x-on:click="handleSubmit()"
                        >
                            <span x-show="isLoading" class="flex items-center justify-center gap-2">
                                <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Memproses...
                            </span>

                            <span x-show="!isLoading">Daftar</span>
                            
                            {{-- Simulasikan sukses jika Anda butuh state success --}}
                            {{-- @if (session('success')) 
                                <span class="flex items-center justify-center gap-2">Berhasil</span>
                            @endif --}}
                        </button>

                        <div class="text-center">
                            <a href="{{ route('login') }}" class="text-sm text-green-600 hover:text-green-700 font-medium transition-colors">
                                Udah punya akun?
                            </a>
                        </div>
                    </div>
                </form>
                {{-- END FORM --}}
            </div>

            <p class="text-center text-xs text-gray-500 mt-6">
                © 2025 RasoSehat. Semua hak dilindungi
            </p>
        </div>
    </div>
</div>
@endsection