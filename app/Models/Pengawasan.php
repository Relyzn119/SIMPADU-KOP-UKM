<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengawasan extends Model
{
    use HasFactory;

    protected $table = 'pengawasans';

    protected $fillable = [
        'koperasi_id',
        'no_surat_tugas',
        'tanggal_pemeriksaan',
        'nama_tim_pengawas',
        'skor_tata_kelola',
        'skor_profil_risiko',
        'skor_kinerja_keuangan',
        'skor_permodalan',
        'skor_total',
        'predikat_kesehatan',
        'kesimpulan_pengawasan',
        'file_berita_acara_path',
        'status_verifikasi',
        'verified_by',
        'verified_at',
        'rejected_by',
        'rejected_at',
        'alasan_penolakan',
    ];

    protected $casts = [
        'tanggal_pemeriksaan' => 'date',
        'skor_tata_kelola' => 'decimal:2',
        'skor_profil_risiko' => 'decimal:2',
        'skor_kinerja_keuangan' => 'decimal:2',
        'skor_permodalan' => 'decimal:2',
        'skor_total' => 'decimal:2',
        'verified_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function koperasi(): BelongsTo
    {
        return $this->belongsTo(Koperasi::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function temuans(): HasMany
    {
        return $this->hasMany(Temuan::class);
    }
}
