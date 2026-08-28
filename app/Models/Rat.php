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
    ];

    protected $casts = [
        'tanggal_rat' => 'date',
        'kuorum' => 'boolean',
    ];

    public function koperasi(): BelongsTo
    {
        return $this->belongsTo(Koperasi::class);
    }
}
