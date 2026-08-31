<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Temuan extends Model
{
    use HasFactory;

    protected $fillable = [
        'pengawasan_id',
        'koperasi_id',
        'aspek_temuan',
        'deskripsi_temuan',
        'rekomendasi',
        'batas_waktu',
        'tingkat_risiko',
        'status_tindak_lanjut',
        'tanggapan_koperasi',
        'catatan_verifikasi_pengawas',
        'status_verifikasi',
        'verified_by',
        'verified_at',
        'rejected_by',
        'rejected_at',
        'alasan_penolakan',
    ];

    protected $casts = [
        'batas_waktu' => 'date',
        'verified_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function pengawasan(): BelongsTo
    {
        return $this->belongsTo(Pengawasan::class);
    }

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
}
