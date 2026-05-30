<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FonnteService;
use App\Models\NotificationFonntepromo;
use Illuminate\Support\Facades\Log;

class FonnteNotificationController extends Controller
{
    public function sendNotification(Request $request)
    {
        // ✅ VALIDASI
        $request->validate([
            'nama' => 'required',
            'nomorwa' => 'required|min:10',
            'pesannotifikasi' => 'required',
            'tanggal_kirim' => 'nullable',
            'namabarang' => 'required',
        ]);

        // ✅ FORMAT NOMOR WA
        $nomorwa = preg_replace('/[^0-9]/', '', $request->nomorwa);

        if (substr($nomorwa, 0, 2) == '62') {
            // OK
        } elseif (substr($nomorwa, 0, 1) == '0') {
            $nomorwa = '62' . substr($nomorwa, 1);
        } else {
            $nomorwa = '62' . $nomorwa;
        }

        // ❗ VALIDASI FINAL NOMOR
        if (strlen($nomorwa) < 11) {
            return response()->json([
                'message' => 'Nomor tidak valid'
            ], 400);
        }

        // ❗ VALIDASI PESAN
        if (empty($request->pesannotifikasi)) {
            return response()->json([
                'message' => 'Pesan kosong'
            ], 400);
        }

        // ✅ FORMAT SCHEDULE
        $schedule = null;
        if (!empty($request->tanggal_kirim)) {
            $schedule = $request->tanggal_kirim . ' 10:00:00';
        }

        // ✅ DEBUG DATA
        Log::info('DATA SEBELUM KIRIM:', [
            'target' => $nomorwa,
            'message' => $request->pesannotifikasi,
            'schedule' => $schedule
        ]);

        // ✅ KIRIM
        $response = FonnteService::sendMessage(
            $nomorwa,
            $request->pesannotifikasi,
            $schedule
        );

        // ✅ JIKA BERHASIL
        if (isset($response['status']) && $response['status'] == true) {

            NotificationFonntepromo::create([
                'nama' => $request->nama,
                'nomorwa' => $nomorwa,
                'pesannotifikasi' => $request->pesannotifikasi,
                'namabarang' => $request->namabarang,
                'tanggal_kirim' => $request->tanggal_kirim,
            ]);

            return response()->json([
                'message' => 'Berhasil kirim WA & simpan data'
            ], 200);
        }

        // ❌ GAGAL
        return response()->json([
            'message' => 'Gagal di Fonnte',
            'detail' => $response
        ], 400);
    }
}
