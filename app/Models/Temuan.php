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
    ];

    protected $casts = [
        'batas_waktu' => 'date',
    ];

    public function pengawasan(): BelongsTo
    {
        return $this->belongsTo(Pengawasan::class);
    }

    public function koperasi(): BelongsTo
    {
        return $this->belongsTo(Koperasi::class);
    }
}
