<?php

namespace App\Http\Controllers;

use App\Models\Koperasi;
use App\Models\Pengawasan;
use App\Models\Rat;
use App\Models\Temuan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $tahunBuku = $request->query('tahun_buku', '2024');
        $kabupatenKota = $request->query('kabupaten_kota', '');

        // Base Koperasi Query Filtered by Wilayah
        $koperasiQuery = Koperasi::query();
        if ($kabupatenKota && $kabupatenKota !== 'semua') {
            $koperasiQuery->where('kabupaten_kota', $kabupatenKota);
        }

        $totalKoperasi = (clone $koperasiQuery)->count();
        $koperasiAktif = (clone $koperasiQuery)->where('status_keaktifan', 'Aktif')->count();

        // Kepatuhan RAT (Filtered by tahun_buku & kabupaten_kota)
        $ratQuery = Rat::query();
        if ($tahunBuku && $tahunBuku !== 'semua') {
            $ratQuery->where('tahun_buku', (int)$tahunBuku);
        }
        if ($kabupatenKota && $kabupatenKota !== 'semua') {
            $ratQuery->whereHas('koperasi', fn ($q) => $q->where('kabupaten_kota', $kabupatenKota));
        }

        $totalRatDoneOnTime = (clone $ratQuery)
            ->where('status_rat', 'Sudah RAT Tepat Waktu')
            ->count();

        $kepatuhanRatPercentage = $totalKoperasi > 0
            ? round(($totalRatDoneOnTime / $totalKoperasi) * 100, 1)
            : 0;

        // Total Koperasi Diawasi
        $totalDiawasi = (clone $koperasiQuery)->whereNotNull('skor_kesehatan_terakhir')->count();

        // Total Temuan Audit belum selesai
        $temuanQuery = Temuan::query();
        if ($kabupatenKota && $kabupatenKota !== 'semua') {
            $temuanQuery->whereHas('koperasi', fn ($q) => $q->where('kabupaten_kota', $kabupatenKota));
        }

        $totalTemuanOpen = (clone $temuanQuery)->where('status_tindak_lanjut', '!=', 'Selesai')->count();
        $temuanKritisOpen = (clone $temuanQuery)->where('tingkat_risiko', 'Kritis')
            ->where('status_tindak_lanjut', '!=', 'Selesai')
            ->count();

        // Donut Chart Predikat Kesehatan
        $predikatDistribution = [
            'Sehat' => (clone $koperasiQuery)->where('predikat_kesehatan', 'Sehat')->count(),
            'Cukup Sehat' => (clone $koperasiQuery)->where('predikat_kesehatan', 'Cukup Sehat')->count(),
            'Dalam Pengawasan' => (clone $koperasiQuery)->where('predikat_kesehatan', 'Dalam Pengawasan')->count(),
            'Pengawasan Khusus' => (clone $koperasiQuery)->where('predikat_kesehatan', 'Pengawasan Khusus')->count(),
        ];

        // Bar Chart Kepatuhan per 33 Kabupaten/Kota
        $kepatuhanPerWilayahQuery = Koperasi::selectRaw('kabupaten_kota, count(*) as total, sum(case when predikat_kesehatan = "Sehat" or predikat_kesehatan = "Cukup Sehat" then 1 else 0 end) as sehat_cukup');
        if ($kabupatenKota && $kabupatenKota !== 'semua') {
            $kepatuhanPerWilayahQuery->where('kabupaten_kota', $kabupatenKota);
        }

        $kepatuhanPerWilayah = $kepatuhanPerWilayahQuery
            ->groupBy('kabupaten_kota')
            ->orderBy('total', 'desc')
            ->take(12)
            ->get();

        // Alert Items: Koperasi Belum RAT
        $targetTahun = ($tahunBuku && $tahunBuku !== 'semua') ? (int)$tahunBuku : 2024;
        $koperasiBelumRat = Koperasi::with(['rats' => fn ($q) => $q->where('tahun_buku', $targetTahun)])
            ->when($kabupatenKota && $kabupatenKota !== 'semua', fn ($q) => $q->where('kabupaten_kota', $kabupatenKota))
            ->whereHas('rats', function ($q) use ($targetTahun) {
                $q->where('tahun_buku', $targetTahun)->where('status_rat', 'Belum RAT');
            })
            ->select('id', 'nama_koperasi', 'no_badan_hukum', 'kabupaten_kota', 'jenis_koperasi')
            ->take(5)
            ->get();

        if ($koperasiBelumRat->isEmpty()) {
            $koperasiBelumRat = Koperasi::when($kabupatenKota && $kabupatenKota !== 'semua', fn ($q) => $q->where('kabupaten_kota', $kabupatenKota))
                ->select('id', 'nama_koperasi', 'no_badan_hukum', 'kabupaten_kota', 'jenis_koperasi')
                ->take(5)
                ->get();
        }

        // Alert Items: Temuan Audit Kritis / Tinggi yang Belum Selesai
        $temuanEmergency = Temuan::with(['koperasi:id,nama_koperasi,kabupaten_kota'])
            ->when($kabupatenKota && $kabupatenKota !== 'semua', fn ($q) => $q->whereHas('koperasi', fn ($k) => $k->where('kabupaten_kota', $kabupatenKota)))
            ->whereIn('tingkat_risiko', ['Kritis', 'Tinggi'])
            ->where('status_tindak_lanjut', '!=', 'Selesai')
            ->orderBy('batas_waktu', 'asc')
            ->take(5)
            ->get();

        // List of all 33 Regencies in North Sumatra
        $kabupatenKotaList = Koperasi::distinct()->pluck('kabupaten_kota')->filter()->values();

        return Inertia::render('Dashboard', [
            'kpi' => [
                'total_koperasi' => $totalKoperasi,
                'koperasi_aktif' => $koperasiAktif,
                'kepatuhan_rat_pct' => $kepatuhanRatPercentage,
                'total_diawasi' => $totalDiawasi,
                'total_temuan_open' => $totalTemuanOpen,
                'temuan_kritis_open' => $temuanKritisOpen,
            ],
            'charts' => [
                'predikat' => $predikatDistribution,
                'per_wilayah' => $kepatuhanPerWilayah,
            ],
            'alerts' => [
                'belum_rat' => $koperasiBelumRat,
                'temuan_emergency' => $temuanEmergency,
            ],
            'filters' => [
                'tahun_buku' => $tahunBuku,
                'kabupaten_kota' => $kabupatenKota,
            ],
            'kabupatenKotaList' => $kabupatenKotaList,
        ]);
    }
}
