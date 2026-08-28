<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KoperasiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only admin_koperasi can create/edit master cooperative data
        return $this->user() && in_array($this->user()->role, ['admin_koperasi', 'bidang_pengawasan']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $koperasiId = $this->koperasi ? $this->koperasi->id : null;

        return [
            'no_badan_hukum' => ['required', 'string', 'max:100', Rule::unique('koperasis', 'no_badan_hukum')->ignore($koperasiId)],
            'nik_koperasi' => ['nullable', 'string', 'max:50'],
            'nama_koperasi' => ['required', 'string', 'max:255'],
            'jenis_koperasi' => ['required', 'string', Rule::in(['KSP', 'KSU', 'Produsen', 'Pemasaran', 'Konsumen', 'Jasa'])],
            'kabupaten_kota' => ['required', 'string', 'max:100'],
            'alamat' => ['required', 'string'],
            'tahun_berdiri' => ['required', 'integer', 'min:1900', 'max:'.(date('Y'))],
            'status_keaktifan' => ['required', 'string', Rule::in(['Aktif', 'Tidak Aktif'])],
            'aset' => ['required', 'numeric', 'min:0'],
            'modal_sendiri' => ['required', 'numeric', 'min:0'],
            'volume_usaha' => ['required', 'numeric', 'min:0'],
            'shu' => ['required', 'numeric'],

            // Pengurus fields
            'ketua' => ['required', 'string', 'max:255'],
            'sekretaris' => ['required', 'string', 'max:255'],
            'bendahara' => ['required', 'string', 'max:255'],
            'ketua_pengawas' => ['required', 'string', 'max:255'],
            'manajer' => ['nullable', 'string', 'max:255'],
            'jumlah_anggota_pria' => ['required', 'integer', 'min:0'],
            'jumlah_anggota_wanita' => ['required', 'integer', 'min:0'],
            'periode_jabatan' => ['required', 'string', 'max:100'],
        ];
    }
}
