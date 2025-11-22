<!DOCTYPE html>
<html lang="en">
<head>
</head>
<body>

    {{-- Selalu pakai Navbar Auth --}}
    @php
        // Pastikan $navbarData tersedia saat layout dipanggil langsung (mis. Route::view)
        if (!isset($navbarData) || !is_array($navbarData)) {
            $navbarData = [
                'userData' => [],
                'notifications' => [],
            ];
        }
    @endphp

    <x-navbar-auth 
        :userData="isset($navbarData) ? ($navbarData['userData'] ?? []) : []" 
        :notifications="isset($navbarData) ? ($navbarData['notifications'] ?? []) : []"
    />

    <main>
        @yield('content')
    </main>

    <x-footer />

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</body>
</html>
