<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Koperasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_badan_hukum',
        'nik_koperasi',
        'nama_koperasi',
        'jenis_koperasi',
        'kabupaten_kota',
        'alamat',
        'tahun_berdiri',
        'status_keaktifan',
        'aset',
        'modal_sendiri',
        'volume_usaha',
        'shu',
        'skor_kesehatan_terakhir',
        'predikat_kesehatan',
        'status_verifikasi',
        'verified_by',
        'verified_at',
        'rejected_by',
        'rejected_at',
        'alasan_penolakan',
    ];

    protected $casts = [
        'aset' => 'decimal:2',
        'modal_sendiri' => 'decimal:2',
        'volume_usaha' => 'decimal:2',
        'shu' => 'decimal:2',
        'skor_kesehatan_terakhir' => 'decimal:2',
        'verified_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function pengurus(): HasOne
    {
        return $this->hasOne(PengurusKoperasi::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function rats(): HasMany
    {
        return $this->hasMany(Rat::class);
    }

    public function pengawasans(): HasMany
    {
        return $this->hasMany(Pengawasan::class);
    }

    public function temuans(): HasMany
    {
        return $this->hasMany(Temuan::class);
    }
}

