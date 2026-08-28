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

        // If user belongs to a specific cooperative, filter stats if needed or show overall summary with highlights
        $totalKoperasi = Koperasi::count();
        $koperasiAktif = Koperasi::where('status_keaktifan', 'Aktif')->count();

        // Kepatuhan RAT (Tahun Buku 2024 / Terakhir)
        $totalRatDoneOnTime = Rat::where('tahun_buku', 2024)
            ->where('status_rat', 'Sudah RAT Tepat Waktu')
            ->count();

        $kepatuhanRatPercentage = $totalKoperasi > 0
            ? round(($totalRatDoneOnTime / $totalKoperasi) * 100, 1)
            : 0;

        // Total Koperasi Diawasi
        $totalDiawasi = Koperasi::whereNotNull('skor_kesehatan_terakhir')->count();

        // Total Temuan Audit belum selesai
        $totalTemuanOpen = Temuan::where('status_tindak_lanjut', '!=', 'Selesai')->count();
        $temuanKritisOpen = Temuan::where('tingkat_risiko', 'Kritis')
            ->where('status_tindak_lanjut', '!=', 'Selesai')
            ->count();

        // Donut Chart Predikat Kesehatan
        $predikatDistribution = [
            'Sehat' => Koperasi::where('predikat_kesehatan', 'Sehat')->count(),
            'Cukup Sehat' => Koperasi::where('predikat_kesehatan', 'Cukup Sehat')->count(),
            'Dalam Pengawasan' => Koperasi::where('predikat_kesehatan', 'Dalam Pengawasan')->count(),
            'Pengawasan Khusus' => Koperasi::where('predikat_kesehatan', 'Pengawasan Khusus')->count(),
        ];

        // Bar Chart Kepatuhan per 33 Kabupaten/Kota
        $kepatuhanPerWilayah = Koperasi::selectRaw('kabupaten_kota, count(*) as total, sum(case when predikat_kesehatan = "Sehat" or predikat_kesehatan = "Cukup Sehat" then 1 else 0 end) as sehat_cukup')
            ->groupBy('kabupaten_kota')
            ->orderBy('total', 'desc')
            ->take(12) // Show top 12 regencies on summary bar chart
            ->get();

        // Alert Items: Koperasi Belum RAT / Terlambat RAT
        $koperasiBelumRat = Koperasi::with(['rats' => fn ($q) => $q->where('tahun_buku', 2024)])
            ->whereHas('rats', function ($q) {
                $q->where('tahun_buku', 2024)->where('status_rat', 'Belum RAT');
            })
            ->select('id', 'nama_koperasi', 'no_badan_hukum', 'kabupaten_kota', 'jenis_koperasi')
            ->take(5)
            ->get();

        // Alert Items: Temuan Audit Kritis / Tinggi yang Belum Selesai
        $temuanEmergency = Temuan::with(['koperasi:id,nama_koperasi,kabupaten_kota'])
            ->whereIn('tingkat_risiko', ['Kritis', 'Tinggi'])
            ->where('status_tindak_lanjut', '!=', 'Selesai')
            ->orderBy('batas_waktu', 'asc')
            ->take(5)
            ->get();

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
        ]);
    }
}
