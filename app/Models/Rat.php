<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rat extends Model
{
    use HasFactory;

    protected $fillable = [
        'koperasi_id',
        'tahun_buku',
        'tanggal_rat',
        'tempat_pelaksanaan',
        'jumlah_anggota_hadir',
        'kuorum',
        'status_rat',
        'file_lpj_path',
        'catatan',
        'status_verifikasi',
        'verified_by',
        'verified_at',
        'rejected_by',
        'rejected_at',
        'alasan_penolakan',
    ];

    protected $casts = [
        'tanggal_rat' => 'date',
        'kuorum' => 'boolean',
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
}
