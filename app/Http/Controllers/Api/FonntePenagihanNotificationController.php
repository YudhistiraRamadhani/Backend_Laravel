<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PesanNotifikasiPenagihan;
use App\Services\FonnteService;

class FonntePenagihanNotificationController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi: Pastikan key sesuai dengan body di Flutter
        $request->validate([
            'nama_pelanggan'             => 'required|string|max:100',
            'nama_barang'               => 'required|string|max:100',
            'nomorwa'                   => 'required|string|max:20',
            'pesan_notifikasi_penagihan' => 'nullable',
        ]);

        // 2. Format Nomor WA untuk Fonnte (Ubah 08... jadi 628...)
        $nomorwa_formatted = preg_replace('/[^0-9]/', '', $request->nomorwa);
        if (str_starts_with($nomorwa_formatted, '0')) {
            $nomorwa_formatted = '62' . substr($nomorwa_formatted, 1);
        }

        // 3. Logika Pesan: Ambil dari input atau gunakan template otomatis
        $isiPesan = $request->pesan_notifikasi_penagihan;

        if (empty($isiPesan)) {
            $isiPesan = "Halo *{$request->nama_pelanggan}*, ini pengingat dari *JS Cell*.\n\n" .
                          "Terdapat tagihan untuk barang: *{$request->nama_barang}*.\n" .
                          "Mohon segera melakukan penyelesaian pembayaran. Terima kasih.";
        }

        // 4. Kirim Pesan via Service Fonnte
        $response = FonnteService::sendMessage($nomorwa_formatted, $isiPesan);

        // 5. Cek respon dari Fonnte
        if (isset($response['status']) && $response['status'] == true) {
            // Simpan riwayat ke database
            PesanNotifikasiPenagihan::create([
                'nama_pelanggan'             => $request->nama_pelanggan,
                'nama_barang'               => $request->nama_barang,
                'nomorwa'                   => $request->nomorwa,
                'pesan_notifikasi_penagihan' => $isiPesan, // Simpan pesan final yang terkirim
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi Berhasil Terkirim & Dicatat!'
            ], 200);
        }

        // Jika Fonnte gagal (Misal: Token salah atau WA Disconnect)
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengirim melalui Fonnte',
            'detail'  => $response
        ], 400);
    }
}
