<?php

namespace App\Http\Controllers;

use App\Models\Ulasan;
use Illuminate\Http\Request;

class UlasanController extends Controller
{
    public function index() {
        return response()->json(Ulasan::with(['user', 'menu'])->get());
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'menu_id' => 'required|exists:menu_makanan,id',
            'rating' => 'required|integer|between:1,5',
        ]);

        $ulasan = Ulasan::create($validated);
        return response()->json($ulasan, 201);
    }

    public function show($id) {
        return response()->json(Ulasan::with(['user', 'menu'])->findOrFail($id));
    }

    public function update(Request $request, $id) {
        $ulasan = Ulasan::findOrFail($id);
        $ulasan->update($request->all());
        return response()->json($ulasan);
    }

    public function destroy($id) {
        Ulasan::destroy($id);
        return response()->json(['message' => 'Ulasan deleted successfully']);
    }
}
