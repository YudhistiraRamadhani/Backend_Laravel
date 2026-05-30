<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\LaporanKeuangan;

class DashboardController extends Controller
{
    public function getSummary(): JsonResponse
    {
        try {
            $totalPendapatan = 0;
            $totalPengeluaran = 0;

            // 1. Ambil data dari tabel laporan_keuangan
            if (Schema::hasTable('laporan_keuangan')) {
                $sumPendapatanMurni = LaporanKeuangan::sum('pendapatan') ?? 0;
                $sumPengeluaranMurni = LaporanKeuangan::sum('pengeluaran') ?? 0;

                if ($sumPendapatanMurni > 0 || $sumPengeluaranMurni > 0) {
                    $totalPendapatan = $sumPendapatanMurni;
                    $totalPengeluaran = $sumPengeluaranMurni;
                } else {
                    // Fallback: hitung dari harga * jumlah
                    $totalPendapatan = LaporanKeuangan::where(function($query) {
                            $query->where('pendapatan', '>', 0)
                                  ->orWhereNull('pengeluaran');
                        })
                        ->select(DB::raw('SUM(CAST(COALESCE(harga, 0) AS NUMERIC) * CAST(COALESCE(jumlah, 0) AS NUMERIC)) as total'))
                        ->value('total') ?? 0;

                    $totalPengeluaran = LaporanKeuangan::where('pengeluaran', '>', 0)
                        ->select(DB::raw('SUM(CAST(COALESCE(harga, 0) AS NUMERIC) * CAST(COALESCE(jumlah, 0) AS NUMERIC)) as total'))
                        ->value('total') ?? 0;
                }
            }

            // Hitung Laba Bersih
            $labaBersih = $totalPendapatan - $totalPengeluaran;

            // 2. Hitung Data Produk
            $stokKritikal = 0;
            $totalSemuaProduk = 0;

            if (Schema::hasTable('produks')) {
                $stokKritikal = DB::table('produks')->where('Stok', '<', 3)->count();
                $totalSemuaProduk = DB::table('produks')->count();
            }

            // 3. Hitung data Piutang dari tabel piutang
            $totalPiutang = 0;
            $piutangBelumLunasCount = 0;

            if (Schema::hasTable('piutang')) {
                // Total nominal seluruh piutang
                $totalPiutang = DB::table('piutang')->sum('jumlah_hutang') ?? 0;

                // Hitung JUMLAH data piutang yang statusnya BELUM LUNAS
                $piutangBelumLunasCount = DB::table('piutang')
                    ->where(function($query) {
                        $query->where('status', '!=', 'Lunas')
                              ->where('status', '!=', 'lunas')
                              ->where('status', '!=', 'LUNAS')
                              ->orWhereNull('status');
                    })
                    ->count();
            }

            // Return JSON response lengkap
            return response()->json([
                'success' => true,
                'total_omzet' => (int)$labaBersih,
                'total_pemasukan' => (int)$totalPendapatan,
                'total_pengeluaran' => (int)$totalPengeluaran,
                'stok_kritikal' => (int)$stokKritikal,
                'total_produk' => (int)$totalSemuaProduk,
                'total_piutang' => (int)$totalPiutang,
                'piutang_belum_lunas_count' => (int)$piutangBelumLunasCount,
                'error_message' => null
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'total_omzet' => 0,
                'total_pemasukan' => 0,
                'total_pengeluaran' => 0,
                'stok_kritikal' => 0,
                'total_produk' => 0,
                'total_piutang' => 0,
                'piutang_belum_lunas_count' => 0,
                'error_message' => "Kalkulasi Gagal: " . $e->getMessage()
            ], 200);
        }
    }
}
