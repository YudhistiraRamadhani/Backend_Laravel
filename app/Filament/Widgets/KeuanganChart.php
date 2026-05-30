<?php

namespace App\Filament\Widgets;

use App\Models\LaporanKeuangan;
use Filament\Widgets\ChartWidget;

class KeuanganChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Perbandingan Keuangan';

    // Menentukan interval refresh otomatis (opsional)
    protected static ?string $pollingInterval = '15s';

    // Filter default saat halaman pertama kali dimuat
    public ?string $filter = '2026';

    protected function getData(): array
    {
        $tahunTerpilih = $this->filter;

        // 1. Ambil data Pendapatan per bulan
        $dataPendapatan = collect(range(1, 12))->map(function ($month) use ($tahunTerpilih) {
            return (int) LaporanKeuangan::query()
                ->whereYear('tanggal', $tahunTerpilih)
                ->whereMonth('tanggal', $month)
                ->sum('pendapatan');
        })->toArray();

        // 2. Ambil data Pengeluaran per bulan
        $dataPengeluaran = collect(range(1, 12))->map(function ($month) use ($tahunTerpilih) {
            return (int) LaporanKeuangan::query()
                ->whereYear('tanggal', $tahunTerpilih)
                ->whereMonth('tanggal', $month)
                ->sum('pengeluaran');
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Total Pendapatan',
                    'data' => array_values($dataPendapatan),
                    'backgroundColor' => '#36A2EB', // Biru
                    'borderColor' => '#2563eb',
                    'borderWidth' => 1,
                    'borderRadius' => 4,
                ],
                [
                    'label' => 'Total Pengeluaran',
                    'data' => array_values($dataPengeluaran),
                    'backgroundColor' => '#FF6384', // Merah Muda
                    'borderColor' => '#e11d48',
                    'borderWidth' => 1,
                    'borderRadius' => 4,
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
        ];
    }

    protected function getType(): string
    {
        // Menggunakan diagram batang
        return 'bar';
    }

    protected function getFilters(): ?array
    {
        // Daftar tahun yang bisa dipilih di dropdown
        $tahunSekarang = (int) date('Y');

        return [
            (string) $tahunSekarang => (string) $tahunSekarang,
            (string) ($tahunSekarang - 1) => (string) ($tahunSekarang - 1),
            (string) ($tahunSekarang - 2) => (string) ($tahunSekarang - 2),
        ];
    }
}
