<?php

namespace App\Http\Controllers;

use App\Models\Koperasi;
use App\Models\Rat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class RatController extends Controller
{
    public function index(Request $request): Response
    {
        $tahunBukuSelected = $request->get('tahun_buku', '2024');

        $query = Rat::with(['koperasi', 'verifiedBy:id,name', 'rejectedBy:id,name']);

        if ($tahunBukuSelected && $tahunBukuSelected !== 'semua') {
            $query->where('tahun_buku', (int) $tahunBukuSelected);
        }

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('koperasi', function ($q) use ($search) {
                $q->where('nama_koperasi', 'like', "%{$search}%")
                    ->orWhere('no_badan_hukum', 'like', "%{$search}%")
                    ->orWhere('kabupaten_kota', 'like', "%{$search}%");
            });
        }

        // Status RAT Filter
        if ($request->filled('status_rat')) {
            $query->where('status_rat', $request->status_rat);
        }

        // Status Verifikasi Filter
        if ($request->filled('status_verifikasi')) {
            $query->where('status_verifikasi', $request->status_verifikasi);
        }

        // Kabupaten / Kota Filter
        if ($request->filled('kabupaten_kota')) {
            $query->whereHas('koperasi', function ($q) use ($request) {
                $q->where('kabupaten_kota', $request->kabupaten_kota);
            });
        }

        $rats = $query->orderBy('tanggal_rat', 'desc')
            ->paginate(12)
            ->withQueryString();

        // Counter Summary Stats
        $totalKoperasi = Koperasi::count();
        $ratStatQuery = Rat::query();
        if ($tahunBukuSelected && $tahunBukuSelected !== 'semua') {
            $ratStatQuery->where('tahun_buku', (int) $tahunBukuSelected);
        }
        $ratTepatWaktu = (clone $ratStatQuery)->where('status_rat', 'Sudah RAT Tepat Waktu')->count();
        $ratTerlambat = (clone $ratStatQuery)->where('status_rat', 'Sudah RAT Terlambat')->count();
        $belumRat = (clone $ratStatQuery)->where('status_rat', 'Belum RAT')->count();

        // Koperasi list without RAT for current select options
        $koperasis = Koperasi::select('id', 'nama_koperasi', 'no_badan_hukum', 'kabupaten_kota')
            ->orderBy('nama_koperasi', 'asc')
            ->get();

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

        return Inertia::render('Rat/Index', [
            'rats' => $rats,
            'koperasis' => $koperasis,
            'summary' => [
                'total_koperasi' => $totalKoperasi,
                'rat_tepat_waktu' => $ratTepatWaktu,
                'rat_terlambat' => $ratTerlambat,
                'belum_rat' => $belumRat,
                'kepatuhan_pct' => $totalKoperasi > 0 ? round(($ratTepatWaktu / $totalKoperasi) * 100, 1) : 0,
            ],
            'filters' => [
                'tahun_buku' => $tahunBukuSelected === 'semua' ? 'semua' : (is_numeric($tahunBukuSelected) ? (int) $tahunBukuSelected : $tahunBukuSelected),
                'search' => $request->search,
                'status_rat' => $request->status_rat,
                'kabupaten_kota' => $request->kabupaten_kota,
                'status_verifikasi' => $request->status_verifikasi,
            ],
            'kabupatenKotaList' => $kabupatenKotaList,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->role === 'bidang_pengawasan') {
            abort(403, 'Akses ditolak. Pengawas tidak diizinkan menambah data RAT.');
        }

        $validated = $request->validate([
            'koperasi_id' => 'required|exists:koperasis,id',
            'tahun_buku' => 'required|integer|min:2000|max:2030',
            'tanggal_rat' => 'required|date',
            'tempat_pelaksanaan' => 'required|string|max:255',
            'jumlah_anggota_hadir' => 'required|integer|min:1',
            'kuorum' => 'required|boolean',
            'catatan' => 'nullable|string',
            'file_lpj' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // 10MB limit
        ]);

        // Auto determine status RAT based on deadline 30 June of next year
        $deadlineDate = ($validated['tahun_buku'] + 1).'-06-30';
        $statusRat = ($validated['tanggal_rat'] <= $deadlineDate)
            ? 'Sudah RAT Tepat Waktu'
            : 'Sudah RAT Terlambat';

        $filePath = null;
        if ($request->hasFile('file_lpj')) {
            $file = $request->file('file_lpj');
            $fileName = 'lpj_tb'.$validated['tahun_buku'].'_kop'.$validated['koperasi_id'].'_'.time().'.'.$file->getClientOriginalExtension();
            $filePath = $file->storeAs('lpj', $fileName, 'public');
        }

        Rat::updateOrCreate(
            [
                'koperasi_id' => $validated['koperasi_id'],
                'tahun_buku' => $validated['tahun_buku'],
            ],
            [
                'tanggal_rat' => $validated['tanggal_rat'],
                'tempat_pelaksanaan' => $validated['tempat_pelaksanaan'],
                'jumlah_anggota_hadir' => $validated['jumlah_anggota_hadir'],
                'kuorum' => $validated['kuorum'],
                'status_rat' => $statusRat,
                'file_lpj_path' => $filePath ? 'storage/'.$filePath : null,
                'catatan' => $validated['catatan'] ?? null,
                'status_verifikasi' => 'pending',
            ]
        );

        return redirect()->route('rat.index')->with('success', 'Data Pelaksanaan RAT berhasil disimpan!');
    }

    public function update(Request $request, Rat $rat): RedirectResponse
    {
        if ($request->user()->role === 'bidang_pengawasan') {
            abort(403, 'Akses ditolak. Pengawas tidak diizinkan memperbarui data RAT.');
        }

        $validated = $request->validate([
            'tanggal_rat' => 'required|date',
            'tempat_pelaksanaan' => 'required|string|max:255',
            'jumlah_anggota_hadir' => 'required|integer|min:1',
            'kuorum' => 'required|boolean',
            'catatan' => 'nullable|string',
            'file_lpj' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $deadlineDate = ($rat->tahun_buku + 1).'-06-30';
        $statusRat = ($validated['tanggal_rat'] <= $deadlineDate)
            ? 'Sudah RAT Tepat Waktu'
            : 'Sudah RAT Terlambat';

        if ($request->hasFile('file_lpj')) {
            if ($rat->file_lpj_path && Storage::disk('public')->exists(str_replace('storage/', '', $rat->file_lpj_path))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $rat->file_lpj_path));
            }

            $file = $request->file('file_lpj');
            $fileName = 'lpj_tb'.$rat->tahun_buku.'_kop'.$rat->koperasi_id.'_'.time().'.'.$file->getClientOriginalExtension();
            $filePath = $file->storeAs('lpj', $fileName, 'public');
            $rat->file_lpj_path = 'storage/'.$filePath;
        }

        $rat->update([
            'tanggal_rat' => $validated['tanggal_rat'],
            'tempat_pelaksanaan' => $validated['tempat_pelaksanaan'],
            'jumlah_anggota_hadir' => $validated['jumlah_anggota_hadir'],
            'kuorum' => $validated['kuorum'],
            'status_rat' => $statusRat,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        return redirect()->route('rat.index')->with('success', 'Data RAT berhasil diperbarui!');
    }

    public function destroy(Request $request, Rat $rat): RedirectResponse
    {
        if ($request->user()->role === 'bidang_pengawasan') {
            abort(403, 'Akses ditolak. Pengawas tidak diizinkan menghapus data RAT.');
        }

        if ($rat->file_lpj_path && Storage::disk('public')->exists(str_replace('storage/', '', $rat->file_lpj_path))) {
            Storage::disk('public')->delete(str_replace('storage/', '', $rat->file_lpj_path));
        }

        $rat->delete();

        return redirect()->route('rat.index')->with('success', 'Data Pelaksanaan RAT berhasil dihapus!');
    }

    public function verifikasi(Request $request, Rat $rat): RedirectResponse
    {
        if ($request->user()->role !== 'bidang_pengawasan') {
            abort(403, 'Hanya Bidang Pengawasan yang diizinkan melakukan verifikasi.');
        }

        $rat->update([
            'status_verifikasi' => 'verified',
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
            'rejected_by' => null,
            'rejected_at' => null,
            'alasan_penolakan' => null,
        ]);

        return redirect()->back()->with('success', 'Laporan RAT berhasil diverifikasi (Dokumen Sah)!');
    }

    public function tolak(Request $request, Rat $rat): RedirectResponse
    {
        if ($request->user()->role !== 'bidang_pengawasan') {
            abort(403, 'Hanya Bidang Pengawasan yang diizinkan menolak dokumen.');
        }

        $validated = $request->validate([
            'alasan_penolakan' => 'required|string',
        ]);

        $rat->update([
            'status_verifikasi' => 'rejected',
            'rejected_by' => $request->user()->id,
            'rejected_at' => now(),
            'alasan_penolakan' => $validated['alasan_penolakan'],
        ]);

        return redirect()->back()->with('success', 'Laporan RAT berhasil ditolak.');
    }
}
