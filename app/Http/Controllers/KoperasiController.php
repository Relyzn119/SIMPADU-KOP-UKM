<?php

namespace App\Http\Controllers;

use App\Http\Requests\KoperasiRequest;
use App\Models\Koperasi;
use App\Models\PengurusKoperasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class KoperasiController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Koperasi::with(['pengurus', 'rats' => fn ($q) => $q->where('tahun_buku', 2024)]);

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_koperasi', 'like', "%{$search}%")
                    ->orWhere('no_badan_hukum', 'like', "%{$search}%")
                    ->orWhere('nik_koperasi', 'like', "%{$search}%");
            });
        }

        // Filter Kabupaten / Kota
        if ($request->filled('kabupaten_kota')) {
            $query->where('kabupaten_kota', $request->kabupaten_kota);
        }

        // Filter Jenis Koperasi
        if ($request->filled('jenis_koperasi')) {
            $query->where('jenis_koperasi', $request->jenis_koperasi);
        }

        // Filter Status Keaktifan
        if ($request->filled('status_keaktifan')) {
            $query->where('status_keaktifan', $request->status_keaktifan);
        }

        // Filter Predikat Kesehatan
        if ($request->filled('predikat_kesehatan')) {
            $query->where('predikat_kesehatan', $request->predikat_kesehatan);
        }

        $koperasis = $query->orderBy('nama_koperasi', 'asc')
            ->paginate(10)
            ->withQueryString();

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

        return Inertia::render('Koperasi/Index', [
            'koperasis' => $koperasis,
            'filters' => $request->only(['search', 'kabupaten_kota', 'jenis_koperasi', 'status_keaktifan', 'predikat_kesehatan']),
            'kabupatenKotaList' => $kabupatenKotaList,
        ]);
    }

    public function show(Koperasi $koperasi): Response
    {
        $koperasi->load([
            'pengurus',
            'rats' => fn ($q) => $q->orderBy('tahun_buku', 'desc'),
            'pengawasans' => fn ($q) => $q->with('temuans')->orderBy('tanggal_pemeriksaan', 'desc'),
        ]);

        return Inertia::render('Koperasi/Show', [
            'koperasi' => $koperasi,
        ]);
    }

    public function store(KoperasiRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $koperasi = Koperasi::create([
            'no_badan_hukum' => $validated['no_badan_hukum'],
            'nik_koperasi' => $validated['nik_koperasi'] ?? null,
            'nama_koperasi' => $validated['nama_koperasi'],
            'jenis_koperasi' => $validated['jenis_koperasi'],
            'kabupaten_kota' => $validated['kabupaten_kota'],
            'alamat' => $validated['alamat'],
            'tahun_berdiri' => $validated['tahun_berdiri'],
            'status_keaktifan' => $validated['status_keaktifan'],
            'aset' => $validated['aset'],
            'modal_sendiri' => $validated['modal_sendiri'],
            'volume_usaha' => $validated['volume_usaha'],
            'shu' => $validated['shu'],
        ]);

        PengurusKoperasi::create([
            'koperasi_id' => $koperasi->id,
            'ketua' => $validated['ketua'],
            'sekretaris' => $validated['sekretaris'],
            'bendahara' => $validated['bendahara'],
            'ketua_pengawas' => $validated['ketua_pengawas'],
            'manajer' => $validated['manajer'] ?? null,
            'jumlah_anggota_pria' => $validated['jumlah_anggota_pria'],
            'jumlah_anggota_wanita' => $validated['jumlah_anggota_wanita'],
            'periode_jabatan' => $validated['periode_jabatan'],
        ]);

        return redirect()->route('koperasi.index')->with('success', 'Data Koperasi berhasil ditambahkan!');
    }

    public function update(KoperasiRequest $request, Koperasi $koperasi): RedirectResponse
    {
        $validated = $request->validated();

        $koperasi->update([
            'no_badan_hukum' => $validated['no_badan_hukum'],
            'nik_koperasi' => $validated['nik_koperasi'] ?? null,
            'nama_koperasi' => $validated['nama_koperasi'],
            'jenis_koperasi' => $validated['jenis_koperasi'],
            'kabupaten_kota' => $validated['kabupaten_kota'],
            'alamat' => $validated['alamat'],
            'tahun_berdiri' => $validated['tahun_berdiri'],
            'status_keaktifan' => $validated['status_keaktifan'],
            'aset' => $validated['aset'],
            'modal_sendiri' => $validated['modal_sendiri'],
            'volume_usaha' => $validated['volume_usaha'],
            'shu' => $validated['shu'],
        ]);

        $koperasi->pengurus()->updateOrCreate(
            ['koperasi_id' => $koperasi->id],
            [
                'ketua' => $validated['ketua'],
                'sekretaris' => $validated['sekretaris'],
                'bendahara' => $validated['bendahara'],
                'ketua_pengawas' => $validated['ketua_pengawas'],
                'manajer' => $validated['manajer'] ?? null,
                'jumlah_anggota_pria' => $validated['jumlah_anggota_pria'],
                'jumlah_anggota_wanita' => $validated['jumlah_anggota_wanita'],
                'periode_jabatan' => $validated['periode_jabatan'],
            ]
        );

        return redirect()->route('koperasi.index')->with('success', 'Data Koperasi berhasil diperbarui!');
    }

    public function destroy(Koperasi $koperasi): RedirectResponse
    {
        $koperasi->delete();

        return redirect()->route('koperasi.index')->with('success', 'Data Koperasi berhasil dihapus!');
    }
}
