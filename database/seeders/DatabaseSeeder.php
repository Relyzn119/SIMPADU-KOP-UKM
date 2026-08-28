<?php

namespace Database\Seeders;

use App\Models\Koperasi;
use App\Models\Pengawasan;
use App\Models\PengurusKoperasi;
use App\Models\Rat;
use App\Models\Temuan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Accounts
        $admin = User::create([
            'name' => 'Budi Santoso, S.Sos',
            'email' => 'admin@diskop.sumutprov.go.id',
            'password' => Hash::make('password'),
            'role' => 'admin_koperasi',
            'nip' => '19850315 201001 1 008',
            'jabatan' => 'Staf Administrasi Kelembagaan Koperasi',
        ]);

        $pengawas = User::create([
            'name' => 'Drs. Iskandar Muda, M.Si',
            'email' => 'pengawas@diskop.sumutprov.go.id',
            'password' => Hash::make('bidang_pengawasan'), // Also allow 'password' by resetting below if needed, let's use 'password' as requested
            'role' => 'bidang_pengawasan',
            'nip' => '19780720 200312 1 002',
            'jabatan' => 'Fungsional Pengawas Koperasi Ahli Madya',
        ]);

        // Fix password to 'password' as explicitly required in prompt
        $pengawas->update(['password' => Hash::make('password')]);

        // List of 33 Kabupaten/Kota in North Sumatra
        $kabupatenKota = [
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

        $jenisList = ['KSP', 'KSU', 'Produsen', 'Pemasaran', 'Konsumen', 'Jasa'];

        $namaKoperasiSamples = [
            'KSP Harapan Kita', 'KSU Mandiri Bersama', 'Koperasi Produsen Kopi Mantap', 'KPRI Sepakat Diskop',
            'Koperasi Konsumen Sejahtera', 'KSP Syariah Berkah Utami', 'Koperasi Pemasaran Tani Makmur',
            'Koperasi Jasa Transportasi Sumut', 'KSU Bukit Barisan', 'KSP Mitra Usaha Mandiri',
            'Koperasi Produsen Sawit Jaya', 'KPRI Kencana Pendopo', 'Koperasi Konsumen Graha Mart',
            'KSP Bangun Tani', 'KSU Martabe Maju', 'Koperasi Produsen Karet Bersama',
            'Koperasi Pemasaran Hasil Laut', 'KSP Sahabat Mulia', 'KSU Bersama Karya', 'KPRI Handayani',
            'Koperasi Produsen Kakao Utama', 'KSP Bina Swadaya', 'KSU Pelita Harapan', 'Koperasi Jasa Pariwisata Danau Toba',
            'KSP Karya Nyata', 'KSU Sumber Gemilang', 'Koperasi Produsen Salak Sidempuan', 'KPRI Bina Sejahtera',
            'Koperasi Konsumen Karya Mandiri', 'KSP Maju Terus', 'KSU Tunas Mekar', 'Koperasi Pemasaran Beras Super',
        ];

        $namaIndoKetua = ['Ahmad Harahap', 'Siti Rahmah Hasibuan', 'Timbang Tarigan', 'Rondang Saragih', 'Parlindungan Siregar', 'Suryani Nasution', 'Bambang Utomo', 'Dewi Lestari Pasaribu', 'Luhut Panjaitan', 'Marlin Sinaga', 'Nurhayati Tambunan', 'Supriadi Batubara', 'Erwin Pohan', 'Zulkifli Lubis', 'Rusli Efendi', 'Rina Anggraini'];
        $namaIndoSekretaris = ['Fitriani Daulay', 'Rahmat Hidayat', 'Marta Ginting', 'Syaloom Marpaung', 'Irfan Lubis', 'Rini Puspita', 'Jefri Simanjuntak', 'Khairul Amri', 'Dian Sastro', 'Hendra Wijaya', 'Sri Rezeki', 'Farida Hanum'];
        $namaIndoBendahara = ['Hj. Aminah', 'Rudi Hartono', 'Tetty Nainggolan', 'Budi Santoso', 'Elvira Roza', 'Suhendra', 'Mei Ling', 'Ganda Tua', 'Yuni Shara', 'Doni Monardo', 'Kartini Harahap', 'Andi Suhendra'];
        $namaIndoPengawas = ['Dr. Arifin Said', 'Ir. Sahat Sitompul', 'Drs. Hamdan Rambe', 'Prof. Rustam Efendi', 'H. Marzuki Alie', 'Risman Manurung', 'Dra. Rosmawati', 'Haji Syahril', 'Jonny Purba', 'Hotman Paris'];

        // 2. Generate 100 Cooperatives
        for ($i = 1; $i <= 100; $i++) {
            $kab = $kabupatenKota[($i - 1) % count($kabupatenKota)];
            $jenis = $jenisList[array_rand($jenisList)];
            $baseName = $namaKoperasiSamples[($i - 1) % count($namaKoperasiSamples)];
            $namaKoperasi = $baseName.' '.str_replace(['Kabupaten ', 'Kota '], '', $kab);

            $tahunBerdiri = rand(1985, 2022);
            $statusAktif = (rand(1, 100) <= 90) ? 'Aktif' : 'Tidak Aktif';

            $aset = rand(250, 15000) * 1000000; // 250 Jt - 15 Milyar
            $modalSendiri = round($aset * (rand(30, 70) / 100), 2);
            $volumeUsaha = round($aset * (rand(50, 120) / 100), 2);
            $shu = round($volumeUsaha * (rand(3, 12) / 100), 2);

            $skorKesehatan = round(rand(5500, 9200) / 100, 2); // 55.00 - 92.00
            $predikat = match (true) {
                $skorKesehatan >= 80.00 => 'Sehat',
                $skorKesehatan >= 66.00 => 'Cukup Sehat',
                $skorKesehatan >= 51.00 => 'Dalam Pengawasan',
                default => 'Pengawasan Khusus',
            };

            $koperasi = Koperasi::create([
                'no_badan_hukum' => sprintf('AHU-%04d.AH.01.26.TAHUN.%d', $i * 17, $tahunBerdiri),
                'nik_koperasi' => sprintf('12%02d%02d%06d', ($i % 33) + 1, rand(1, 12), rand(100000, 999999)),
                'nama_koperasi' => $namaKoperasi,
                'jenis_koperasi' => $jenis,
                'kabupaten_kota' => $kab,
                'alamat' => 'Jl. Lintas Utama No. '.rand(1, 250).', '.$kab.', Sumatera Utara',
                'tahun_berdiri' => $tahunBerdiri,
                'status_keaktifan' => $statusAktif,
                'aset' => $aset,
                'modal_sendiri' => $modalSendiri,
                'volume_usaha' => $volumeUsaha,
                'shu' => $shu,
                'skor_kesehatan_terakhir' => $skorKesehatan,
                'predikat_kesehatan' => $predikat,
            ]);

            // Create Pengurus
            PengurusKoperasi::create([
                'koperasi_id' => $koperasi->id,
                'ketua' => $namaIndoKetua[array_rand($namaIndoKetua)],
                'sekretaris' => $namaIndoSekretaris[array_rand($namaIndoSekretaris)],
                'bendahara' => $namaIndoBendahara[array_rand($namaIndoBendahara)],
                'ketua_pengawas' => $namaIndoPengawas[array_rand($namaIndoPengawas)],
                'manajer' => rand(0, 1) ? $namaIndoKetua[array_rand($namaIndoKetua)] : null,
                'jumlah_anggota_pria' => rand(25, 450),
                'jumlah_anggota_wanita' => rand(20, 500),
                'periode_jabatan' => '2023 - 2026',
            ]);

            // Create RAT
            $isExactTime = rand(1, 100) <= 75;
            $isRATDone = rand(1, 100) <= 88;

            if ($isRATDone) {
                $statusRat = $isExactTime ? 'Sudah RAT Tepat Waktu' : 'Sudah RAT Terlambat';
                $tglRat = $isExactTime ? sprintf('2025-%02d-%02d', rand(1, 5), rand(1, 28)) : sprintf('2025-%02d-%02d', rand(7, 9), rand(1, 28));

                Rat::create([
                    'koperasi_id' => $koperasi->id,
                    'tahun_buku' => 2024,
                    'tanggal_rat' => $tglRat,
                    'tempat_pelaksanaan' => 'Aula '.$namaKoperasi,
                    'jumlah_anggota_hadir' => rand(35, 300),
                    'kuorum' => true,
                    'status_rat' => $statusRat,
                    'file_lpj_path' => 'lpj/sample_lpj_'.$koperasi->id.'.pdf',
                    'catatan' => 'Pelaksanaan RAT Tahun Buku 2024 berjalan lancar dan menyetujui Laporan Pertanggungjawaban Pengurus.',
                ]);
            } else {
                Rat::create([
                    'koperasi_id' => $koperasi->id,
                    'tahun_buku' => 2024,
                    'tanggal_rat' => '2025-06-30',
                    'tempat_pelaksanaan' => 'Belum Ditentukan',
                    'jumlah_anggota_hadir' => 0,
                    'kuorum' => false,
                    'status_rat' => 'Belum RAT',
                    'file_lpj_path' => null,
                    'catatan' => 'Koperasi belum melaksanakan RAT Tahun Buku 2024 hingga batas waktu yang ditentukan.',
                ]);
            }

            // Create Pengawasan & Temuan (for ~60% of coops)
            if ($i <= 65) {
                $skorTataKelola = round(rand(6000, 9500) / 100, 2);
                $skorProfilRisiko = round(rand(5500, 9000) / 100, 2);
                $skorKinerjaKeuangan = round(rand(5000, 9200) / 100, 2);
                $skorPermodalan = round(rand(5500, 9000) / 100, 2);
                $skorTotal = round(($skorTataKelola * 0.30) + ($skorProfilRisiko * 0.25) + ($skorKinerjaKeuangan * 0.30) + ($skorPermodalan * 0.15), 2);

                $predikatPengawasan = match (true) {
                    $skorTotal >= 80.00 => 'Sehat',
                    $skorTotal >= 66.00 => 'Cukup Sehat',
                    $skorTotal >= 51.00 => 'Dalam Pengawasan',
                    default => 'Pengawasan Khusus',
                };

                $pengawasan = Pengawasan::create([
                    'koperasi_id' => $koperasi->id,
                    'no_surat_tugas' => sprintf('ST-800/DISKOP-SU/IX/%d', 100 + $i),
                    'tanggal_pemeriksaan' => sprintf('2025-%02d-%02d', rand(3, 11), rand(1, 28)),
                    'nama_tim_pengawas' => 'Drs. Iskandar Muda, M.Si & Tim Pengawas Diskop Provsu',
                    'skor_tata_kelola' => $skorTataKelola,
                    'skor_profil_risiko' => $skorProfilRisiko,
                    'skor_kinerja_keuangan' => $skorKinerjaKeuangan,
                    'skor_permodalan' => $skorPermodalan,
                    'skor_total' => $skorTotal,
                    'predikat_kesehatan' => $predikatPengawasan,
                    'kesimpulan_pengawasan' => 'Secara umum koperasi berjalan dengan '.$predikatPengawasan.', namun terdapat beberapa aspek tata kelola dan SOP yang perlu disempurnakan.',
                    'file_berita_acara_path' => 'berita_acara/ba_pengawasan_'.$koperasi->id.'.pdf',
                ]);

                // Create 1-3 Temuan for each pengawasan
                $jumlahTemuan = rand(1, 3);
                $aspekList = ['Kelembagaan', 'Keuangan', 'Operasional', 'Usaha'];
                $risikoList = ['Rendah', 'Sedang', 'Tinggi', 'Kritis'];
                $statusTLList = ['Belum Ditindaklanjuti', 'Dalam Proses', 'Selesai'];

                for ($t = 1; $t <= $jumlahTemuan; $t++) {
                    $risiko = $risikoList[array_rand($risikoList)];
                    $statusTL = $statusTLList[array_rand($statusTLList)];

                    Temuan::create([
                        'pengawasan_id' => $pengawasan->id,
                        'koperasi_id' => $koperasi->id,
                        'aspek_temuan' => $aspekList[array_rand($aspekList)],
                        'deskripsi_temuan' => 'Belum terdapat SOP Tertulis mengenai Manajemen Risiko Pembayaran dan Akuntabilitas Laporan Bulanan.',
                        'rekomendasi' => 'Pengurus wajib menyusun dan mengesahkan SOP Manajemen Risiko dan menyampaikan salinannya ke Dinas Koperasi Provsu.',
                        'batas_waktu' => sprintf('2025-%02d-%02d', rand(9, 12), rand(1, 28)),
                        'tingkat_risiko' => $risiko,
                        'status_tindak_lanjut' => $statusTL,
                        'tanggapan_koperasi' => $statusTL !== 'Belum Ditindaklanjuti' ? 'Pengurus sedang menyusun draf SOP bersama tim internal.' : null,
                        'catatan_verifikasi_pengawas' => $statusTL === 'Selesai' ? 'SOP telah diverifikasi oleh Tim Pengawas Diskop Provsu dan dinyatakan sesuai.' : null,
                    ]);
                }
            }
        }
    }
}
