<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LaporanKeuangan;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\TransaksiPengeluaranVoucher;
class LaporankeuanganController extends Controller
{

   public function index()
    {
 return response()->json(LaporanKeuangan::orderBy('id', 'asc')->get(), 200);



    }
public function store(Request $request)
{
    try {
        $harga = (int) $request->harga;
        $jumlah = (int) $request->jumlah;
        $total = $harga * $jumlah;

        $pendapatan = 0;
        $pengeluaran = 0;

        // Logic pemisahan kolom berdasarkan jenis_transaksi yang dikirim Flutter
        if (strtolower($request->jenis_transaksi) == 'pemasukan') {
            $pendapatan = $total;
        } else {
            $pengeluaran = $total;
        }

        $laporan = LaporanKeuangan::create([
            'pendapatan'    => $pendapatan,
            'pengeluaran'   => $pengeluaran,
            'nama_barang'   => $request->nama_barang,
            'harga'         => $harga,
            'jumlah'        => $jumlah,
            'tanggal'       => $request->tanggal,
            'jenis_barang'  => $request->jenis_barang, // Ini akan berisi 'Voucher' atau 'Kartu Provider'
            'nama_supplier' => $request->nama_supplier,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil disimpan ke Laporan Keuangan',
            'data'   => $laporan
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}
   public function update(Request $request, $id)
{
    $laporan = LaporanKeuangan::find($id);

    if (!$laporan) {
        return response()->json(['message' => 'Laporan keuangan tidak ditemukan'], 404);
    }

    // Gunakan 'numeric' alih-alih 'integer' agar string angka dari Flutter diterima
    $request->validate([
        'nama_barang'     => 'sometimes|string',
        'harga'           => 'sometimes|numeric',
        'jumlah'          => 'sometimes|numeric',
        'tanggal'         => 'sometimes|date',
    ]);

    // Sinkronisasi input (mengatasi perbedaan huruf kapital antara Request dan Database)
    $harga = $request->harga ?? $request->Harga ?? $laporan->harga;
    $jumlah = $request->jumlah ?? $request->Jumlah ?? $laporan->jumlah;
    $jenis = $request->jenis_transaksi ?? $laporan->jenis_transaksi;

    // Hitung total (Harga * Jumlah) sesuai logika store Anda
    $total = (int)$harga * (int)$jumlah;

    $pendapatan = (strtolower($jenis) == 'pemasukan') ? $total : 0;
    $pengeluaran = (strtolower($jenis) == 'pengeluaran') ? $total : 0;

    $laporan->update([
        'nama_barang'     => $request->nama_barang ?? $request->Nama_Barang ?? $laporan->nama_barang,
        'nama_supplier'   => $request->nama_supplier ?? $laporan->nama_supplier,
        'jenis_barang'    => $request->jenis_barang ?? $laporan->jenis_barang,
        'harga'           => $harga,
        'jumlah'          => $jumlah,
        'tanggal'         => $request->tanggal ?? $request->Tanggal ?? $laporan->tanggal,
        'pendapatan'      => $pendapatan,
        'pengeluaran'     => $pengeluaran,
        'jenis_transaksi' => $jenis,
    ]);

    return response()->json([
        'status' => 'success',
        'data' => $laporan
    ]);
}
public function destroy($id)
    {
        try {
            $laporan = LaporanKeuangan::find($id);

            if (!$laporan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            $laporan->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data laporan keuangan berhasil dihapus'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }
}
