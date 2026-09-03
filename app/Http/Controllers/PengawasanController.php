<?php

namespace App\Http\Controllers;

use App\Models\Koperasi;
use App\Models\Pengawasan;
use App\Models\Temuan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class PengawasanController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Pengawasan::with(['koperasi', 'createdBy:id,name', 'approvedBy:id,name', 'verifiedBy:id,name', 'rejectedBy:id,name', 'temuans']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_surat_tugas', 'like', "%{$search}%")
                    ->orWhere('nama_tim_pengawas', 'like', "%{$search}%")
                    ->orWhereHas('koperasi', fn ($k) => $k->where('nama_koperasi', 'like', "%{$search}%")->orWhere('kabupaten_kota', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('tahun') && $request->tahun !== 'semua') {
            $query->whereYear('tanggal_pemeriksaan', $request->tahun);
        }

        if ($request->filled('predikat_kesehatan')) {
            $query->where('predikat_kesehatan', $request->predikat_kesehatan);
        }

        if ($request->filled('status_verifikasi')) {
            $query->where('status_verifikasi', $request->status_verifikasi);
        }

        if ($request->filled('status_persetujuan_koperasi')) {
            $query->where('status_persetujuan_koperasi', $request->status_persetujuan_koperasi);
        }

        if ($request->filled('kabupaten_kota')) {
            $query->whereHas('koperasi', fn ($k) => $k->where('kabupaten_kota', $request->kabupaten_kota));
        }

        $pengawasans = $query->orderBy('tanggal_pemeriksaan', 'desc')
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

        return Inertia::render('Pengawasan/Index', [
            'pengawasans' => $pengawasans,
            'filters' => $request->only(['search', 'tahun', 'predikat_kesehatan', 'status_verifikasi', 'status_persetujuan_koperasi', 'kabupaten_kota']),
            'kabupatenKotaList' => $kabupatenKotaList,
        ]);
    }

    public function create(Request $request): Response
    {
        if ($request->user()->role === 'admin_koperasi') {
            abort(403, 'Akses ditolak. Penginputan LHP Pengawasan hanya diizinkan untuk Tim Pengawas Diskop.');
        }

        $koperasis = Koperasi::select('id', 'nama_koperasi', 'no_badan_hukum', 'kabupaten_kota')
            ->orderBy('nama_koperasi', 'asc')
            ->get();

        return Inertia::render('Pengawasan/Create', [
            'koperasis' => $koperasis,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->role === 'admin_koperasi') {
            abort(403, 'Akses ditolak. Penginputan LHP Pengawasan hanya diizinkan untuk Tim Pengawas Diskop.');
        }

        $validated = $request->validate([
            'koperasi_id' => 'required|exists:koperasis,id',
            'no_surat_tugas' => 'required|string|max:100',
            'tanggal_pemeriksaan' => 'required|date',
            'nama_tim_pengawas' => 'required|string|max:255',
            'skor_tata_kelola' => 'required|numeric|min:0|max:100',
            'skor_profil_risiko' => 'required|numeric|min:0|max:100',
            'skor_kinerja_keuangan' => 'required|numeric|min:0|max:100',
            'skor_permodalan' => 'required|numeric|min:0|max:100',
            'kesimpulan_pengawasan' => 'nullable|string',
            'file_berita_acara' => 'nullable|file|mimes:pdf,doc,docx|max:10240',

            // Temuan list (array)
            'temuans' => 'nullable|array',
            'temuans.*.aspek_temuan' => 'required|string|in:Kelembagaan,Keuangan,Operasional,Usaha',
            'temuans.*.deskripsi_temuan' => 'required|string',
            'temuans.*.rekomendasi' => 'required|string',
            'temuans.*.batas_waktu' => 'required|date',
            'temuans.*.tingkat_risiko' => 'required|string|in:Rendah,Sedang,Tinggi,Kritis',
        ]);

        // Calculate Total Score (4 Aspects Weightage per Permenkop)
        $skorTotal = round(
            ($validated['skor_tata_kelola'] * 0.30) +
            ($validated['skor_profil_risiko'] * 0.25) +
            ($validated['skor_kinerja_keuangan'] * 0.30) +
            ($validated['skor_permodalan'] * 0.15),
            2
        );

        $predikat = match (true) {
            $skorTotal >= 80.00 => 'Sehat',
            $skorTotal >= 66.00 => 'Cukup Sehat',
            $skorTotal >= 51.00 => 'Dalam Pengawasan',
            default => 'Pengawasan Khusus',
        };

        $filePath = null;
        if ($request->hasFile('file_berita_acara')) {
            $file = $request->file('file_berita_acara');
            $fileName = 'ba_pengawasan_kop'.$validated['koperasi_id'].'_'.time().'.'.$file->getClientOriginalExtension();
            $filePath = $file->storeAs('berita_acara', $fileName, 'public');
        }

        $pengawasan = Pengawasan::create([
            'koperasi_id' => $validated['koperasi_id'],
            'created_by' => $request->user()->id,
            'no_surat_tugas' => $validated['no_surat_tugas'],
            'tanggal_pemeriksaan' => $validated['tanggal_pemeriksaan'],
            'nama_tim_pengawas' => $validated['nama_tim_pengawas'],
            'skor_tata_kelola' => $validated['skor_tata_kelola'],
            'skor_profil_risiko' => $validated['skor_profil_risiko'],
            'skor_kinerja_keuangan' => $validated['skor_kinerja_keuangan'],
            'skor_permodalan' => $validated['skor_permodalan'],
            'skor_total' => $skorTotal,
            'predikat_kesehatan' => $predikat,
            'kesimpulan_pengawasan' => $validated['kesimpulan_pengawasan'] ?? null,
            'file_berita_acara_path' => $filePath ? 'storage/'.$filePath : null,
            'status_verifikasi' => 'verified', // Pengawas yang buat -> otomatis terverifikasi oleh pengawas
            'status_persetujuan_koperasi' => 'pending', // Menunggu persetujuan pengurus koperasi
        ]);

        // Update latest health status in Koperasi master record
        Koperasi::where('id', $validated['koperasi_id'])->update([
            'skor_kesehatan_terakhir' => $skorTotal,
            'predikat_kesehatan' => $predikat,
        ]);

        // Save attached temuans
        if (! empty($validated['temuans'])) {
            foreach ($validated['temuans'] as $t) {
                Temuan::create([
                    'pengawasan_id' => $pengawasan->id,
                    'koperasi_id' => $validated['koperasi_id'],
                    'created_by' => $request->user()->id,
                    'aspek_temuan' => $t['aspek_temuan'],
                    'deskripsi_temuan' => $t['deskripsi_temuan'],
                    'rekomendasi' => $t['rekomendasi'],
                    'batas_waktu' => $t['batas_waktu'],
                    'tingkat_risiko' => $t['tingkat_risiko'],
                    'status_tindak_lanjut' => 'Belum Ditindaklanjuti',
                    'status_verifikasi' => 'verified',
                    'status_persetujuan_koperasi' => 'pending',
                ]);
            }
        }

        return redirect()->route('pengawasan.index')->with('success', 'Berita Acara & Penilaian Kesehatan Koperasi berhasil diterbitkan oleh Pengawas!');
    }

    public function show(Pengawasan $pengawasan): Response
    {
        $pengawasan->load(['koperasi', 'createdBy:id,name', 'approvedBy:id,name', 'verifiedBy:id,name', 'rejectedBy:id,name', 'temuans']);

        return Inertia::render('Pengawasan/Show', [
            'pengawasan' => $pengawasan,
        ]);
    }

    public function destroy(Request $request, Pengawasan $pengawasan): RedirectResponse
    {
        if ($request->user()->role === 'admin_koperasi') {
            abort(403, 'Akses ditolak. Pengurus Koperasi tidak diizinkan menghapus LHP Pemeriksaan.');
        }

        if ($pengawasan->file_berita_acara_path && Storage::disk('public')->exists(str_replace('storage/', '', $pengawasan->file_berita_acara_path))) {
            Storage::disk('public')->delete(str_replace('storage/', '', $pengawasan->file_berita_acara_path));
        }

        $pengawasan->delete();

        return redirect()->route('pengawasan.index')->with('success', 'Data Pemeriksaan Pengawasan berhasil dihapus oleh Pengawas!');
    }

    // Persetujuan & Tanggapan oleh Admin / Pengurus Koperasi
    public function setujui(Request $request, Pengawasan $pengawasan): RedirectResponse
    {
        $validated = $request->validate([
            'tanggapan_koperasi' => 'required|string',
            'file_bukti_tindak_lanjut' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:10240',
        ]);

        $filePath = $pengawasan->file_bukti_tindak_lanjut_path;
        if ($request->hasFile('file_bukti_tindak_lanjut')) {
            $file = $request->file('file_bukti_tindak_lanjut');
            $fileName = 'bukti_tl_pengawasan_'.$pengawasan->id.'_'.time().'.'.$file->getClientOriginalExtension();
            $filePath = 'storage/' . $file->storeAs('bukti_tindak_lanjut', $fileName, 'public');
        }

        $pengawasan->update([
            'status_persetujuan_koperasi' => 'approved',
            'tanggapan_koperasi' => $validated['tanggapan_koperasi'],
            'file_bukti_tindak_lanjut_path' => $filePath,
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'skor_transparansi' => 100, // Nilai transparansi sempurna jika disetujui pengurus
        ]);

        return redirect()->back()->with('success', 'LHP Pemeriksaan Pengawasan berhasil disetujui dan ditanggapi oleh Pengurus Koperasi!');
    }
}
