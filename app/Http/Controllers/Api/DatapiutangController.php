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
     * Mengambil id, nama, no_whatsapp, dan telegram_chat_id dari tabel data_pelanggan
     */
    public function getPelanggan()
    {
        // 🔥 Tambahkan id dan telegram_chat_id untuk Flutter
        $pelanggan = DataPelanggan::select('id', 'nama_pelanggan', 'no_whatsapp', 'telegram_chat_id')
            ->orderBy('nama_pelanggan')
            ->get();
        return response()->json($pelanggan);
    }

    /**
     * Get produk data for autocomplete
     * Mengambil nama_barang, harga, dan stok dari tabel produk
     */
    public function getProduk()
    {
        // Mengambil data produk untuk autocomplete
        $produk = Produk::select('id', 'Nama_Barang', 'Harga', 'Stok', 'jenis_barang')
            ->orderBy('Nama_Barang')
            ->get();
        return response()->json($produk);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mengambil semua data piutang diurutkan dari terbaru
        $piutang = Piutang::orderBy('created_at', 'desc')->get();
        return response()->json($piutang);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 🔥 Validasi input
        $validated = $request->validate([
            'nama_pelanggan' => 'required|string',
            'jumlah_hutang' => 'required|integer',
            'nama_barang' => 'required|string',
            'harga' => 'required|integer',
            'status' => 'required|string',
            'no_whatsapp' => 'required|string',
            'pesanpenagihan' => 'nullable|string',
            'date' => 'nullable|date',
            'telegram_chat_id' => 'nullable|string',
        ]);

        // 🔥 Jika telegram_chat_id kosong, cari dari tabel pelanggan
        if (empty($validated['telegram_chat_id'])) {
            $pelanggan = DataPelanggan::where('no_whatsapp', $validated['no_whatsapp'])->first();
            if ($pelanggan && $pelanggan->telegram_chat_id) {
                $validated['telegram_chat_id'] = $pelanggan->telegram_chat_id;
            }
        }

        // 🔥 Perbaiki: Gunakan $validated, bukan $request langsung
        // 🔥 Perbaiki typo: telegran_chat_id -> telegram_chat_id
        $piutang = Piutang::create([
            'nama_pelanggan' => $validated['nama_pelanggan'],
            'jumlah_hutang' => $validated['jumlah_hutang'],
            'nama_barang' => $validated['nama_barang'],
            'harga' => $validated['harga'],
            'status' => $validated['status'],
            'no_whatsapp' => $validated['no_whatsapp'],
            'pesanpenagihan' => $validated['pesanpenagihan'] ?? null,
            'date' => $validated['date'] ?? date('Y-m-d'),
            'telegram_chat_id' => $validated['telegram_chat_id'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan',
            'data' => $piutang
        ], 201);
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
            'telegram_chat_id' => 'sometimes|nullable|string',
        ]);

        // 🔥 Jika telegram_chat_id kosong, cari dari tabel pelanggan
        if (empty($validatedData['telegram_chat_id']) && !empty($validatedData['no_whatsapp'])) {
            $pelanggan = DataPelanggan::where('no_whatsapp', $validatedData['no_whatsapp'])->first();
            if ($pelanggan && $pelanggan->telegram_chat_id) {
                $validatedData['telegram_chat_id'] = $pelanggan->telegram_chat_id;
            }
        }

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
            'success' => true,
            'message' => 'Data berhasil dihapus'
        ]);
    }

    /**
     * Update status piutang menjadi Lunas
     */
    public function updateStatusLunas($id)
    {
        $piutang = Piutang::find($id);

        if (!$piutang) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        if ($piutang->status == 'Lunas') {
            return response()->json([
                'success' => false,
                'message' => 'Data sudah lunas'
            ], 400);
        }

        $piutang->status = 'Lunas';
        $piutang->save();

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diupdate menjadi Lunas',
            'data' => $piutang
        ]);
    }

    /**
     * Sinkronisasi Telegram Chat ID dari DataPelanggan ke Piutang
     */
    public function sinkronTelegram()
    {
        try {
            // Ambil semua piutang yang memiliki no_whatsapp
            $piutangList = Piutang::whereNotNull('no_whatsapp')->get();
            $updated = 0;

            foreach ($piutangList as $piutang) {
                // Cari pelanggan berdasarkan no_whatsapp
                $pelanggan = DataPelanggan::where('no_whatsapp', $piutang->no_whatsapp)->first();

                if ($pelanggan && $pelanggan->telegram_chat_id) {
                    // Update telegram_chat_id di piutang
                    $piutang->telegram_chat_id = $pelanggan->telegram_chat_id;
                    $piutang->save();
                    $updated++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Sinkronisasi berhasil, $updated data diupdate",
                'updated' => $updated
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
