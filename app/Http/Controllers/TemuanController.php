<?php

namespace App\Http\Controllers;

use App\Models\Temuan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TemuanController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Temuan::with(['koperasi', 'pengawasan', 'verifiedBy:id,name', 'rejectedBy:id,name']);

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('deskripsi_temuan', 'like', "%{$search}%")
                    ->orWhere('rekomendasi', 'like', "%{$search}%")
                    ->orWhereHas('koperasi', fn ($k) => $k->where('nama_koperasi', 'like', "%{$search}%")->orWhere('kabupaten_kota', 'like', "%{$search}%"));
            });
        }

        // Tingkat Risiko Filter
        if ($request->filled('tingkat_risiko')) {
            $query->where('tingkat_risiko', $request->tingkat_risiko);
        }

        // Status Tindak Lanjut Filter
        if ($request->filled('status_tindak_lanjut')) {
            $query->where('status_tindak_lanjut', $request->status_tindak_lanjut);
        }

        // Status Verifikasi Filter
        if ($request->filled('status_verifikasi')) {
            $query->where('status_verifikasi', $request->status_verifikasi);
        }

        // Aspek Temuan Filter
        if ($request->filled('aspek_temuan')) {
            $query->where('aspek_temuan', $request->aspek_temuan);
        }

        $temuans = $query->orderBy('batas_waktu', 'asc')
            ->paginate(12)
            ->withQueryString();

        // Summary counters
        $totalTemuan = Temuan::count();
        $kritisOpen = Temuan::where('tingkat_risiko', 'Kritis')->where('status_tindak_lanjut', '!=', 'Selesai')->count();
        $dalamProses = Temuan::where('status_tindak_lanjut', 'Dalam Proses')->count();
        $selesai = Temuan::where('status_tindak_lanjut', 'Selesai')->count();

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

        return Inertia::render('Temuan/Index', [
            'temuans' => $temuans,
            'summary' => [
                'total' => $totalTemuan,
                'kritis_open' => $kritisOpen,
                'dalam_proses' => $dalamProses,
                'selesai' => $selesai,
            ],
            'filters' => $request->only(['search', 'tingkat_risiko', 'status_tindak_lanjut', 'status_verifikasi', 'aspek_temuan']),
            'kabupatenKotaList' => $kabupatenKotaList,
        ]);
    }

    public function updateTindakLanjut(Request $request, Temuan $temuan): RedirectResponse
    {
        $validated = $request->validate([
            'tanggapan_koperasi' => 'required|string',
            'status_tindak_lanjut' => 'required|string|in:Belum Ditindaklanjuti,Dalam Proses,Selesai',
        ]);

        $temuan->update([
            'tanggapan_koperasi' => $validated['tanggapan_koperasi'],
            'status_tindak_lanjut' => $validated['status_tindak_lanjut'],
        ]);

        return redirect()->route('temuan.index')->with('success', 'Progress tindak lanjut temuan berhasil diperbarui!');
    }

    public function verifikasi(Request $request, Temuan $temuan): RedirectResponse
    {
        // Only bidang_pengawasan can verify
        if (! in_array($request->user()->role, ['bidang_pengawasan', 'admin_koperasi'])) {
            abort(403, 'Akses terbatas untuk Bidang Pengawasan.');
        }

        $validated = $request->validate([
            'catatan_verifikasi_pengawas' => 'nullable|string',
            'status_tindak_lanjut' => 'nullable|string|in:Belum Ditindaklanjuti,Dalam Proses,Selesai',
        ]);

        $updateData = [
            'status_verifikasi' => 'verified',
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
            'rejected_by' => null,
            'rejected_at' => null,
            'alasan_penolakan' => null,
        ];

        if (isset($validated['catatan_verifikasi_pengawas'])) {
            $updateData['catatan_verifikasi_pengawas'] = $validated['catatan_verifikasi_pengawas'];
        }

        if (isset($validated['status_tindak_lanjut'])) {
            $updateData['status_tindak_lanjut'] = $validated['status_tindak_lanjut'];
        }

        $temuan->update($updateData);

        return redirect()->route('temuan.index')->with('success', 'Verifikasi tindak lanjut temuan berhasil disimpan (Dokumen Sah)!');
    }

    public function tolak(Request $request, Temuan $temuan): RedirectResponse
    {
        if (! in_array($request->user()->role, ['bidang_pengawasan', 'admin_koperasi'])) {
            abort(403, 'Akses terbatas untuk Bidang Pengawasan.');
        }

        $validated = $request->validate([
            'alasan_penolakan' => 'required|string',
        ]);

        $temuan->update([
            'status_verifikasi' => 'rejected',
            'rejected_by' => $request->user()->id,
            'rejected_at' => now(),
            'alasan_penolakan' => $validated['alasan_penolakan'],
        ]);

        return redirect()->route('temuan.index')->with('success', 'Data Temuan berhasil ditolak.');
    }

    public function destroy(Request $request, Temuan $temuan): RedirectResponse
    {
        if ($request->user()->role === 'bidang_pengawasan') {
            abort(403, 'Akses ditolak. Pengawas tidak diizinkan menghapus temuan.');
        }

        $temuan->delete();

        return redirect()->route('temuan.index')->with('success', 'Data Temuan Audit berhasil dihapus!');
    }
}
