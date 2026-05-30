<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Datapiutang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Piutang;
use App\Models\DataPelanggan;
use App\Models\Produk;
use App\Http\Controllers\DataPelangganController;

class DatapiutangController extends Controller {

    /**
     * Get pelanggan data for autocomplete
     * Mengambil nama dan no_whatsapp dari tabel data_pelanggan
     */
    public function getPelanggan()
    {
        // Mengambil nama dan no_whatsapp dari model DataPelanggan
        $pelanggan = DataPelanggan::select('nama_pelanggan', 'no_whatsapp')->get();
        return response()->json($pelanggan);
    }

    /**
     * Get produk data for autocomplete
     * Mengambil nama_barang, harga, dan stok dari tabel produk
     */
    public function getProduk()
    {
        // Mengambil data produk untuk autocomplete
        $produk = Produk::select('id', 'Nama_Barang', 'Harga', 'Stok', 'jenis_barang')->get();
        return response()->json($produk);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mengambil semua data piutang
        return response()->json(Piutang::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string',
            'jumlah_hutang' => 'required|integer',
            'nama_barang' => 'required|string',
            'harga' => 'required|integer',
            'status' => 'required|string',
            'no_whatsapp' => 'required|string',
            'pesanpenagihan' => 'nullable|string',
            'date' => 'nullable|date',
        ]);

        $piutang = Piutang::create([
            'nama_pelanggan' => $request->nama_pelanggan,
            'jumlah_hutang' => $request->jumlah_hutang,
            'nama_barang' => $request->nama_barang,
            'harga' => $request->harga,
            'status' => $request->status,
            'no_whatsapp' => $request->no_whatsapp,
            'pesanpenagihan' => $request->pesanpenagihan,
            'date' => $request->date,
        ]);

        return response()->json($piutang, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Cari data piutang berdasarkan ID
        $piutang = Piutang::find($id);

        // Jika data tidak ditemukan, kirim error 404
        if (!$piutang) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        // Validasi data
        $validatedData = $request->validate([
            'nama_pelanggan' => 'sometimes|required|string',
            'jumlah_hutang'  => 'sometimes|required|integer',
            'nama_barang'    => 'sometimes|required|string',
            'harga'          => 'sometimes|required|integer',
            'status'         => 'sometimes|required|string',
            'no_whatsapp'    => 'sometimes|required|string',
            'pesanpenagihan' => 'sometimes|nullable|string',
            'date' => 'sometimes|nullable|date',
        ]);

        // Update data
        $piutang->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diperbarui',
            'data'    => $piutang
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $piutang = Piutang::find($id);

        if (!$piutang) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $piutang->delete();

        return response()->json([
            'message' => 'Data berhasil dihapus'
        ]);
    }
}
