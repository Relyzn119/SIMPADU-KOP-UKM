<?php

namespace App\Http\Controllers;

use App\Models\Koperasi;
use App\Models\Pengawasan;
use App\Models\Rat;
use App\Models\Temuan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CetakController extends Controller
{
    public function index(Request $request): Response
    {
        $jenisLaporan = $request->get('jenis_laporan', 'wilayah');
        $tahunBuku = $request->get('tahun_buku', '2024');
        $kabupatenSelected = $request->get('kabupaten_kota');

        // Data 1: Rekapitulasi Data Koperasi Binaan per 33 Kabupaten/Kota
        $rekapWilayah = Koperasi::selectRaw('
                kabupaten_kota,
                count(*) as total_koperasi,
                sum(case when status_keaktifan = "Aktif" then 1 else 0 end) as koperasi_aktif,
                sum(aset) as total_aset,
                sum(modal_sendiri) as total_modal_sendiri,
                sum(volume_usaha) as total_volume_usaha,
                sum(shu) as total_shu
            ')
            ->when($kabupatenSelected, fn ($q) => $q->where('kabupaten_kota', $kabupatenSelected))
            ->groupBy('kabupaten_kota')
            ->orderBy('total_koperasi', 'desc')
            ->get();

        // Data 2: Rekapitulasi Kepatuhan RAT
        $ratQuery = Koperasi::with(['rats' => fn ($q) => $tahunBuku !== 'semua' ? $q->where('tahun_buku', $tahunBuku) : $q])
            ->when($kabupatenSelected, fn ($q) => $q->where('kabupaten_kota', $kabupatenSelected));
        $rekapRat = $ratQuery->orderBy('kabupaten_kota', 'asc')->orderBy('nama_koperasi', 'asc')->get();

        // Data 3: Hasil Pemeriksaan Kesehatan Koperasi
        $kesehatanQuery = Koperasi::whereNotNull('skor_kesehatan_terakhir')
            ->when($kabupatenSelected, fn ($q) => $q->where('kabupaten_kota', $kabupatenSelected));
        $rekapKesehatan = $kesehatanQuery->orderBy('skor_kesehatan_terakhir', 'desc')->get();

        // Data 4: Matriks Rekomendasi & Temuan Audit
        $temuanQuery = Temuan::with(['koperasi', 'pengawasan'])
            ->when($kabupatenSelected, fn ($q) => $q->whereHas('koperasi', fn ($k) => $k->where('kabupaten_kota', $kabupatenSelected)));
        $rekapTemuan = $temuanQuery->orderBy('batas_waktu', 'asc')->get();

        $kabupatenKotaList = [
            'Kota Medan', 'Kota Binjai', 'Kota Pematangsiantar', 'Kota Tebing Tinggi',
            'Kota Tanjungbalai', 'Kota Padangsidimpuan', 'Kota Gunungsitoli', 'Kota Sibolga',
            'Kabupaten Deli Serdang', 'Kabupaten Karo', 'Kabupaten Simalungun', 'Kabupaten Asahan',
            'Kabupaten Labuhanbatu', 'Kabupaten Toba', 'Kabupaten Tapanuli Utara', 'Kabupaten Nias',
            'Kabupaten Langkat', 'Kabupaten Dairi', 'Kabupaten Pakpak Bharat', 'Kabupaten Humbang Hasundutan',
            'Kabupaten Samosir', 'Kabupaten Tapanuli Selatan', 'Kabupaten Tapanuli Tengah', 'Kabupaten Mandailing Natal',
            'Kabupaten Batu Bara', 'Kabupaten Labuhanbatu Utara', 'Kabupaten Labuhanbatu Selatan', 'Kabupaten Padang Lawas',
            'Kabupaten Padang Lawas Utara', 'Kabupaten Nias Barat', 'Kabupaten Nias Utara', 'Kabupaten Nias Selatan',
            'Kabupaten Serdang Bedagai',
        ];

        return Inertia::render('Cetak/LaporanCetak', [
            'jenisLaporan' => $jenisLaporan,
            'tahunBuku' => $tahunBuku === 'semua' ? 'semua' : (is_numeric($tahunBuku) ? (int) $tahunBuku : $tahunBuku),
            'kabupatenSelected' => $kabupatenSelected,
            'rekapWilayah' => $rekapWilayah,
            'rekapRat' => $rekapRat,
            'rekapKesehatan' => $rekapKesehatan,
            'rekapTemuan' => $rekapTemuan,
            'kabupatenKotaList' => $kabupatenKotaList,
            'tanggalCetak' => now()->translatedFormat('d F Y'),
        ]);
    }
}
