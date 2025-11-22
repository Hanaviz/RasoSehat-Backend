{{-- resources/views/layouts/auth.blade.php --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RasoSehat | @yield('title', 'Autentikasi')</title>
    
    {{-- Panggil CSS & JS untuk Tailwind/Alpine --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">
    
    {{-- Konten penuh dari Signin.jsx/Signup.jsx akan mengisi area ini --}}
    @yield('content') 
    
    {{-- Alpine.js dipanggil di sini juga, karena komponen Signin/Signup menggunakannya --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>