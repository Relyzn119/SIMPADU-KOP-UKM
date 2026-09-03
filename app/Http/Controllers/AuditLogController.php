<?php

namespace App\Http\Controllers;

use App\Models\Koperasi;
use App\Models\Pengawasan;
use App\Models\Rat;
use App\Models\Temuan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AuditLogController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->query('search', '');
        $status = $request->query('status', ''); // verified or rejected
        $tahun = $request->query('tahun', '');

        // Collect logs from all 4 entities
        $koperasiLogs = Koperasi::with(['verifiedBy:id,name', 'rejectedBy:id,name'])
            ->whereIn('status_verifikasi', ['verified', 'rejected'])
            ->get()
            ->map(fn ($item) => [
                'id' => 'kop-' . $item->id,
                'modul' => 'Master Koperasi',
                'judul' => $item->nama_koperasi,
                'no_referensi' => $item->no_badan_hukum,
                'status' => $item->status_verifikasi,
                'user' => $item->status_verifikasi === 'verified' ? $item->verifiedBy?->name : $item->rejectedBy?->name,
                'timestamp' => $item->status_verifikasi === 'verified' ? $item->verified_at : $item->rejected_at,
                'alasan' => $item->alasan_penolakan,
            ]);

        $ratLogs = Rat::with(['koperasi:id,nama_koperasi', 'verifiedBy:id,name', 'rejectedBy:id,name'])
            ->whereIn('status_verifikasi', ['verified', 'rejected'])
            ->get()
            ->map(fn ($item) => [
                'id' => 'rat-' . $item->id,
                'modul' => 'Laporan RAT',
                'judul' => ($item->koperasi?->nama_koperasi ?? 'Koperasi') . ' (Tahun Buku ' . $item->tahun_buku . ')',
                'no_referensi' => 'TB ' . $item->tahun_buku,
                'status' => $item->status_verifikasi,
                'user' => $item->status_verifikasi === 'verified' ? $item->verifiedBy?->name : $item->rejectedBy?->name,
                'timestamp' => $item->status_verifikasi === 'verified' ? $item->verified_at : $item->rejected_at,
                'alasan' => $item->alasan_penolakan,
            ]);

        $pengawasanLogs = Pengawasan::with(['koperasi:id,nama_koperasi', 'verifiedBy:id,name', 'rejectedBy:id,name'])
            ->whereIn('status_verifikasi', ['verified', 'rejected'])
            ->get()
            ->map(fn ($item) => [
                'id' => 'pw-' . $item->id,
                'modul' => 'Pemeriksaan Pengawasan',
                'judul' => $item->koperasi?->nama_koperasi ?? 'Koperasi',
                'no_referensi' => $item->no_surat_tugas,
                'status' => $item->status_verifikasi,
                'user' => $item->status_verifikasi === 'verified' ? $item->verifiedBy?->name : $item->rejectedBy?->name,
                'timestamp' => $item->status_verifikasi === 'verified' ? $item->verified_at : $item->rejected_at,
                'alasan' => $item->alasan_penolakan,
            ]);

        $temuanLogs = Temuan::with(['koperasi:id,nama_koperasi', 'verifiedBy:id,name', 'rejectedBy:id,name'])
            ->whereIn('status_verifikasi', ['verified', 'rejected'])
            ->get()
            ->map(fn ($item) => [
                'id' => 'tm-' . $item->id,
                'modul' => 'Temuan Audit',
                'judul' => ($item->koperasi?->nama_koperasi ?? 'Koperasi') . ' - ' . $item->aspek_temuan,
                'no_referensi' => $item->deskripsi_temuan,
                'status' => $item->status_verifikasi,
                'user' => $item->status_verifikasi === 'verified' ? $item->verifiedBy?->name : $item->rejectedBy?->name,
                'timestamp' => $item->status_verifikasi === 'verified' ? $item->verified_at : $item->rejected_at,
                'alasan' => $item->alasan_penolakan,
            ]);

        $allLogs = collect()
            ->concat($koperasiLogs)
            ->concat($ratLogs)
            ->concat($pengawasanLogs)
            ->concat($temuanLogs);

        if ($status) {
            $allLogs = $allLogs->where('status', $status);
        }

        if ($tahun && $tahun !== 'semua') {
            $allLogs = $allLogs->filter(function ($item) use ($tahun) {
                if (!$item['timestamp']) return false;
                return date('Y', strtotime($item['timestamp'])) == $tahun;
            });
        }

        if ($search) {
            $allLogs = $allLogs->filter(function ($item) use ($search) {
                return str_contains(strtolower($item['judul']), strtolower($search))
                    || str_contains(strtolower($item['no_referensi']), strtolower($search))
                    || str_contains(strtolower($item['modul']), strtolower($search));
            });
        }

        $allLogs = $allLogs->sortByDesc('timestamp')->values();

        return Inertia::render('AuditLog/Index', [
            'logs' => $allLogs,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'tahun' => $tahun,
            ],
        ]);
    }
}
