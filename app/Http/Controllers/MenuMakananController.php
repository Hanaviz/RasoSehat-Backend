<?php

namespace App\Http\Controllers;

use App\Models\MenuMakanan;
use Illuminate\Http\Request;

class MenuMakananController extends Controller
{
    public function index() {
        return response()->json(MenuMakanan::with(['restoran', 'kategori'])->get());
    }

    public function show($id) {
        return response()->json(MenuMakanan::with(['restoran', 'kategori'])->findOrFail($id));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'restoran_id' => 'required|exists:restorans,id',
            'kategori_id' => 'required|exists:kategori_makanan,id',
            'nama_menu' => 'required|string',
            'harga' => 'required|numeric',
        ]);

        $menu = MenuMakanan::create($validated);
        return response()->json($menu, 201);
    }

    public function update(Request $request, $id) {
        $menu = MenuMakanan::findOrFail($id);
        $menu->update($request->all());
        return response()->json($menu);
    }

    public function destroy($id) {
        MenuMakanan::destroy($id);
        return response()->json(['message' => 'Menu deleted successfully']);
    }
}
