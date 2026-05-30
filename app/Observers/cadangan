<?php

namespace App\Observers;

use App\Models\Transaksi;
use App\Models\LaporanKeuangan;
use App\Models\Produk;

class TransaksiObserver
{
    // Berjalan saat data BARU dibuat (Insert)
    public function created(Transaksi $transaksi)
    {
        $this->syncKeLaporan($transaksi);
    }

    // Berjalan saat data LAMA diubah (Update)
    public function updated(Transaksi $transaksi)
    {
        // Cari data di laporan keuangan yang berhubungan, lalu update atau buat baru jika belum ada
        $this->syncKeLaporan($transaksi);
    }

    // Fungsi bantuan agar logika tidak ditulis dua kali
    private function syncKeLaporan(Transaksi $transaksi)
    {
        $jenis = strtolower(trim($transaksi->jenis_transaksi));
        $total = (int)$transaksi->Harga * (int)$transaksi->Jumlah;

        // Gunakan updateOrCreate agar jika data di-update, baris yang sama di laporan keuangan ikut berubah
        // Kita gunakan Nama_Barang dan Tanggal sebagai kunci pencarian (atau bisa field unik lainnya)
        LaporanKeuangan::updateOrCreate(
            [
                // Kunci pencarian agar tidak menduplikat baris saat update
                'tanggal'     => $transaksi->getOriginal('Tanggal') ?? $transaksi->Tanggal,
                'nama_barang' => $transaksi->getOriginal('Nama_Barang') ?? $transaksi->Nama_Barang,
            ],
            [
                'tanggal'       => $transaksi->Tanggal,
                'nama_barang'   => $transaksi->Nama_Barang,
                'nama_supplier' => $transaksi->nama_supplier ?? '-',
                'jenis_barang'  => $transaksi->jenis_barang,
                'pendapatan'    => ($jenis === 'pemasukan') ? $total : 0,
                'pengeluaran'   => ($jenis === 'pengeluaran') ? $total : 0,
                'jumlah'        => $transaksi->Jumlah,
                'harga'         => $transaksi->Harga,
            ]
        );
    }
}
