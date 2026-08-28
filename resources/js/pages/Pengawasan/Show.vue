<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import {
    ShieldCheck,
    ArrowLeft,
    Printer,
    FileText,
    Award,
    AlertTriangle,
    CheckCircle2
} from 'lucide-vue-next';

const props = defineProps<{
    pengawasan: {
        id: number;
        no_surat_tugas: string;
        tanggal_pemeriksaan: string;
        nama_tim_pengawas: string;
        skor_tata_kelola: number;
        skor_profil_risiko: number;
        skor_kinerja_keuangan: number;
        skor_permodalan: number;
        skor_total: number;
        predikat_kesehatan: string;
        kesimpulan_pengawasan: string;
        file_berita_acara_path: string;
        koperasi: {
            id: number;
            nama_koperasi: string;
            no_badan_hukum: string;
            jenis_koperasi: string;
            kabupaten_kota: string;
            alamat: string;
        };
        temuans?: Array<{
            id: number;
            aspek_temuan: string;
            deskripsi_temuan: string;
            rekomendasi: string;
            batas_waktu: string;
            tingkat_risiko: string;
            status_tindak_lanjut: string;
        }>;
    };
}>();

const handlePrint = () => {
    window.print();
};

const getPredikatBadge = (predikat: string) => {
    switch (predikat) {
        case 'Sehat': return 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40';
        case 'Cukup Sehat': return 'bg-cyan-500/20 text-cyan-300 border-cyan-500/40';
        case 'Dalam Pengawasan': return 'bg-amber-500/20 text-amber-300 border-amber-500/40';
        default: return 'bg-rose-500/20 text-rose-300 border-rose-500/40';
    }
};

const getRiskBadge = (risiko: string) => {
    switch (risiko) {
        case 'Kritis': return 'bg-rose-500/20 text-rose-300 border-rose-500/40';
        case 'Tinggi': return 'bg-amber-500/20 text-amber-300 border-amber-500/40';
        case 'Sedang': return 'bg-yellow-500/20 text-yellow-300 border-yellow-500/40';
        default: return 'bg-blue-500/20 text-blue-300 border-blue-500/40';
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="`Lembar Hasil Pemeriksaan - ${pengawasan.koperasi?.nama_koperasi}`" />

        <div class="space-y-6 max-w-4xl mx-auto">
            <!-- TOP BAR BACK & PRINT ACTION -->
            <div class="flex items-center justify-between print:hidden">
                <Link href="/pengawasan" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-purple-400 transition">
                    <ArrowLeft class="w-4 h-4" />
                    Kembali ke Daftar Pengawasan
                </Link>

                <button 
                    @click="handlePrint"
                    class="flex items-center gap-2 px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-semibold text-xs shadow-lg shadow-purple-600/30 transition"
                >
                    <Printer class="w-4 h-4" />
                    Cetak Lembar Hasil (LHP)
                </button>
            </div>

            <!-- OFFICIAL REPORT CONTAINER (PRINT-READY) -->
            <div class="p-8 sm:p-10 rounded-2xl bg-slate-900 border border-slate-800 shadow-2xl space-y-6 print:bg-white print:text-slate-900 print:shadow-none print:border-none print:p-0">
                <!-- KOP SURAT DINAS KOPERASI SUMUT -->
                <div class="border-b-2 border-slate-700 print:border-slate-900 pb-4 text-center space-y-1">
                    <h2 class="text-base font-extrabold uppercase tracking-wider text-white print:text-slate-900">
                        PEMERINTAH PROVINSI SUMATERA UTARA
                    </h2>
                    <h1 class="text-lg font-black uppercase tracking-widest text-emerald-400 print:text-slate-900">
                        DINAS KOPERASI DAN USAN KECIL MENENGAH
                    </h1>
                    <p class="text-xs text-slate-400 print:text-slate-700">
                        Jl. Gatot Subroto No. 222 Medan, Sumatera Utara | Telepon: (061) 8452110
                    </p>
                    <div class="text-[11px] font-bold text-slate-300 print:text-slate-800 uppercase tracking-widest pt-2">
                        LEMBAR HASIL PEMERIKSAAN KESEHATAN KOPERASI (LHP)
                    </div>
                </div>

                <!-- SURAT TUGAS & KOPERASI DETAILS -->
                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div class="space-y-1">
                        <div class="text-slate-400 print:text-slate-600">Nomor Surat Tugas:</div>
                        <div class="font-bold text-white print:text-slate-900 font-mono">{{ pengawasan.no_surat_tugas }}</div>
                        <div class="text-slate-400 print:text-slate-600 mt-2">Tanggal Pemeriksaan:</div>
                        <div class="font-semibold text-slate-200 print:text-slate-900">{{ pengawasan.tanggal_pemeriksaan }}</div>
                    </div>

                    <div class="space-y-1 text-right">
                        <div class="text-slate-400 print:text-slate-600">Objek Koperasi:</div>
                        <div class="font-bold text-white print:text-slate-900">{{ pengawasan.koperasi?.nama_koperasi }}</div>
                        <div class="text-slate-400 print:text-slate-600 font-mono text-[11px]">{{ pengawasan.koperasi?.no_badan_hukum }}</div>
                        <div class="text-slate-400 print:text-slate-600 text-[11px]">{{ pengawasan.koperasi?.kabupaten_kota }}</div>
                    </div>
                </div>

                <!-- 4 ASPECTS BREAKDOWN TABLE -->
                <div class="space-y-2">
                    <h3 class="text-xs font-bold text-slate-200 print:text-slate-900 uppercase tracking-wider">
                        A. Rincian Penilaian 4 Aspek Kesehatan
                    </h3>
                    <table class="w-full text-xs text-left border border-slate-800 print:border-slate-300">
                        <thead class="bg-slate-950/60 print:bg-slate-100 text-slate-400 print:text-slate-800">
                            <tr>
                                <th class="p-2.5 border-b border-slate-800 print:border-slate-300">Aspek Penilaian</th>
                                <th class="p-2.5 border-b border-slate-800 print:border-slate-300 text-center">Bobot</th>
                                <th class="p-2.5 border-b border-slate-800 print:border-slate-300 text-center">Skor Terhitung</th>
                                <th class="p-2.5 border-b border-slate-800 print:border-slate-300 text-center">Kontribusi Skor</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800 print:divide-slate-300 text-slate-200 print:text-slate-900">
                            <tr>
                                <td class="p-2.5">1. Tata Kelola Koperasi</td>
                                <td class="p-2.5 text-center">30%</td>
                                <td class="p-2.5 text-center font-bold">{{ pengawasan.skor_tata_kelola }}</td>
                                <td class="p-2.5 text-center font-mono">{{ (pengawasan.skor_tata_kelola * 0.30).toFixed(2) }}</td>
                            </tr>
                            <tr>
                                <td class="p-2.5">2. Profil Risiko Koperasi</td>
                                <td class="p-2.5 text-center">25%</td>
                                <td class="p-2.5 text-center font-bold">{{ pengawasan.skor_profil_risiko }}</td>
                                <td class="p-2.5 text-center font-mono">{{ (pengawasan.skor_profil_risiko * 0.25).toFixed(2) }}</td>
                            </tr>
                            <tr>
                                <td class="p-2.5">3. Kinerja Keuangan Koperasi</td>
                                <td class="p-2.5 text-center">30%</td>
                                <td class="p-2.5 text-center font-bold">{{ pengawasan.skor_kinerja_keuangan }}</td>
                                <td class="p-2.5 text-center font-mono">{{ (pengawasan.skor_kinerja_keuangan * 0.30).toFixed(2) }}</td>
                            </tr>
                            <tr>
                                <td class="p-2.5">4. Permodalan Koperasi</td>
                                <td class="p-2.5 text-center">15%</td>
                                <td class="p-2.5 text-center font-bold">{{ pengawasan.skor_permodalan }}</td>
                                <td class="p-2.5 text-center font-mono">{{ (pengawasan.skor_permodalan * 0.15).toFixed(2) }}</td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-slate-950/80 print:bg-slate-200 font-bold">
                            <tr>
                                <td colspan="3" class="p-2.5 text-right uppercase">SKOR TOTAL AKHIR:</td>
                                <td class="p-2.5 text-center font-mono text-emerald-400 print:text-slate-900 text-sm">
                                    {{ pengawasan.skor_total }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- PREDIKAT KESEHATAN RESULT BOX -->
                <div class="p-4 rounded-xl bg-slate-800/60 print:bg-slate-100 border border-slate-700 print:border-slate-300 flex items-center justify-between">
                    <div>
                        <div class="text-[11px] font-semibold text-slate-400 print:text-slate-600 uppercase">Kesimpulan Predikat Kesehatan:</div>
                        <div class="text-base font-extrabold text-white print:text-slate-900 mt-0.5">
                            KOPERASI {{ pengawasan.predikat_kesehatan.toUpperCase() }}
                        </div>
                    </div>
                    <span class="px-4 py-1.5 rounded-full text-xs font-bold border" :class="getPredikatBadge(pengawasan.predikat_kesehatan)">
                        {{ pengawasan.predikat_kesehatan }}
                    </span>
                </div>

                <!-- MATRIKS TEMUAN AUDIT -->
                <div v-if="pengawasan.temuans && pengawasan.temuans.length > 0" class="space-y-2">
                    <h3 class="text-xs font-bold text-slate-200 print:text-slate-900 uppercase tracking-wider">
                        B. Matriks Temuan & Rekomendasi Pemeriksaan
                    </h3>
                    <table class="w-full text-xs text-left border border-slate-800 print:border-slate-300">
                        <thead class="bg-slate-950/60 print:bg-slate-100 text-slate-400 print:text-slate-800">
                            <tr>
                                <th class="p-2 border-b border-slate-800 print:border-slate-300">Aspek</th>
                                <th class="p-2 border-b border-slate-800 print:border-slate-300">Temuan Lapangan</th>
                                <th class="p-2 border-b border-slate-800 print:border-slate-300">Rekomendasi Tim</th>
                                <th class="p-2 border-b border-slate-800 print:border-slate-300 text-center">Risiko</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800 print:divide-slate-300 text-slate-300 print:text-slate-900">
                            <tr v-for="t in pengawasan.temuans" :key="t.id">
                                <td class="p-2 font-bold">{{ t.aspek_temuan }}</td>
                                <td class="p-2">{{ t.deskripsi_temuan }}</td>
                                <td class="p-2 text-emerald-400 print:text-slate-900">{{ t.rekomendasi }}</td>
                                <td class="p-2 text-center">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold border" :class="getRiskBadge(t.tingkat_risiko)">
                                        {{ t.tingkat_risiko }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- SIGNATURE BOX FOR PRINT -->
                <div class="pt-10 grid grid-cols-2 gap-8 text-center text-xs print:block hidden">
                    <div>
                        <div>Mengetahui,</div>
                        <div class="font-bold">Pengurus Koperasi {{ pengawasan.koperasi?.nama_koperasi }}</div>
                        <div class="h-16"></div>
                        <div class="font-bold text-underline">( ________________________ )</div>
                    </div>
                    <div>
                        <div>Medan, {{ pengawasan.tanggal_pemeriksaan }}</div>
                        <div class="font-bold">Tim Pengawas Diskop Provsu</div>
                        <div class="h-16"></div>
                        <div class="font-bold font-underline">{{ pengawasan.nama_tim_pengawas }}</div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
