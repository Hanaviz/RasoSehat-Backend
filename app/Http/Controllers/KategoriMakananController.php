<?php

namespace App\Http\Controllers;

use App\Models\KategoriMakanan;
use Illuminate\Http\Request;

class KategoriMakananController extends Controller
{
    public function index() {
        return response()->json(KategoriMakanan::all());
    }

    public function store(Request $request) {
        $validated = $request->validate(['nama_kategori' => 'required|string']);
        $kategori = KategoriMakanan::create($validated);
        return response()->json($kategori, 201);
    }

    public function show($id) {
        return response()->json(KategoriMakanan::findOrFail($id));
    }

    public function update(Request $request, $id) {
        $kategori = KategoriMakanan::findOrFail($id);
        $kategori->update($request->all());
        return response()->json($kategori);
    }

    public function destroy($id) {
        KategoriMakanan::destroy($id);
        return response()->json(['message' => 'Kategori deleted successfully']);
    }
}
