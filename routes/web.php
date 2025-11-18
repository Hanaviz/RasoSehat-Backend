<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Rute-rute ini dimuat oleh ServiceProvider dan mengandung middleware 'web'.
| Fokus utama adalah mengembalikan view HTML untuk Single Page Application (SPA).
|
*/

// Route Default (Homepage)
Route::get('/', function () {
    // Mengembalikan view utama, tempat ReactJS di-mount
    return view('welcome');
});

// Catch-all route untuk React Router. 
// Rute ini memastikan setiap URL (kecuali yang diawali dengan /api) mengembalikan view utama.
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '^(?!api).*$'); // PENTING: Mengecualikan semua rute yang diawali dengan /api