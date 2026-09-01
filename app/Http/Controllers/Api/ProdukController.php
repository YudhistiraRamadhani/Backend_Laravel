<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    public function index() {
        $produk = Produk::all();
        return response()->json([
            'success' => true,
            'data'    => $produk
        ]);
    }

    public function show($id)
    {
        $produk = Produk::find($id);

        if (!$produk) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json($produk);
    }

    public function store(Request $request)
    {
        $request->validate([


            'Nama_Barang' => 'nullable|string',
            'Harga'       => 'required|string',
            'Stok'        => 'required|string',
            'image'       => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'jenis_barang'     => 'required|string',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        $post = Produk::create([
            'kode_produk'  => $request->kode_produk,
            'Nama_Barang' => $request->Nama_Barang,
            'Harga'       => $request->Harga,
            'Stok'        => $request->Stok,
            'image'       => $imagePath,
            'jenis_barang'     => $request->jenis_barang,
        ]);

        return response()->json($post, 201);
    }

    public function update(Request $request, $id)
    {
        $post = Produk::findOrFail($id);

        $request->validate([
            'kode_produk'  => 'required|string|unique:produks,kode_produk,' . $id,
            'Nama_Barang' => 'required|string',
            'Harga'       => 'required|string',
            'Stok'        => 'required|string',
            'image'       => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'jenis_barang '     => 'nullable|string',
        ]);

        $data = [
            'kode_produk'  => $request->kode_produk,
            'Nama_Barang' => $request->Nama_Barang,
            'Harga'       => $request->Harga,
            'Stok'        => $request->Stok,
            'jenis_barang'     => $request->jenis_barang,
        ];

        if ($request->hasFile('image')) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }

            $data['image'] = $request->file('image')->store('posts', 'public');
        }

        $post->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Diupdate',
            'data'    => $post
        ], 200);
    }

    public function destroy($id)
    {
        $post = Produk::findOrFail($id);

        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return response()->json([
            'message' => 'Data Terhapus'
        ]);
    }
}
