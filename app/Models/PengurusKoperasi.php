<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengurusKoperasi extends Model
{
    use HasFactory;

    protected $table = 'pengurus_koperasis';

    protected $fillable = [
        'koperasi_id',
        'ketua',
        'sekretaris',
        'bendahara',
        'ketua_pengawas',
        'manajer',
        'jumlah_anggota_pria',
        'jumlah_anggota_wanita',
        'periode_jabatan',
    ];

    public function koperasi(): BelongsTo
    {
        return $this->belongsTo(Koperasi::class);
    }
}
