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
        case 'Sehat': return 'bg-emerald-50 text-emerald-700 border-emerald-200';
        case 'Cukup Sehat': return 'bg-cyan-50 text-cyan-700 border-cyan-200';
        case 'Dalam Pengawasan': return 'bg-amber-50 text-amber-700 border-amber-200';
        default: return 'bg-rose-50 text-rose-700 border-rose-200';
    }
};

const getRiskBadge = (risiko: string) => {
    switch (risiko) {
        case 'Kritis': return 'bg-rose-50 text-rose-700 border-rose-200';
        case 'Tinggi': return 'bg-amber-50 text-amber-700 border-amber-200';
        case 'Sedang': return 'bg-yellow-50 text-yellow-700 border-yellow-200';
        default: return 'bg-blue-50 text-blue-700 border-blue-200';
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="`Lembar Hasil Pemeriksaan - ${pengawasan.koperasi?.nama_koperasi}`" />

        <div class="space-y-6 max-w-4xl mx-auto">
            <!-- TOP BAR BACK & PRINT ACTION -->
            <div class="flex items-center justify-between print:hidden">
                <Link href="/pengawasan" class="inline-flex items-center gap-2 text-xs font-semibold text-gray-500 hover:text-emerald-600 transition">
                    <ArrowLeft class="w-4 h-4" />
                    Kembali ke Daftar Pengawasan
                </Link>

                <button 
                    @click="handlePrint"
                    class="flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-xs transition"
                >
                    <Printer class="w-4 h-4 text-emerald-400" />
                    Cetak Lembar Hasil (LHP)
                </button>
            </div>

            <!-- OFFICIAL REPORT CONTAINER (PRINT-READY) -->
            <div class="p-8 sm:p-10 rounded-3xl bg-white border border-gray-200/70 shadow-2xs space-y-6 text-gray-900 print:bg-white print:text-slate-900 print:shadow-none print:border-none print:p-0">
                <!-- KOP SURAT DINAS KOPERASI SUMUT -->
                <div class="flex items-center gap-4 sm:gap-6 border-b-4 border-double border-gray-900 pb-4">
                    <!-- LOGO EMBLEM PROVSU (LEFT) -->
                    <img src="/images/icon-provsu.svg" alt="Logo Pemprov Sumut" class="w-20 h-20 sm:w-24 sm:h-24 object-contain shrink-0" />

                    <!-- KOP TEXT (CENTER/RIGHT) -->
                    <div class="flex-1 text-center space-y-0.5 text-gray-900">
                        <h2 class="text-xs sm:text-sm font-extrabold uppercase tracking-wider text-gray-900 leading-tight">
                            PEMERINTAH PROVINSI SUMATERA UTARA
                        </h2>
                        <h1 class="text-sm sm:text-base font-black uppercase tracking-wide text-gray-900 leading-tight">
                            DINAS KOPERASI DAN USAHA KECIL MENENGAH
                        </h1>
                        <p class="text-[10px] sm:text-[11px] text-gray-700 font-medium">
                            Jalan Jenderal Gatot Subroto No. 222 Medan, Sumatera Utara 20118
                        </p>
                        <p class="text-[10px] sm:text-[11px] text-gray-600 font-medium">
                            Telepon: (061) 8452110, Faksimile: (061) 8452110 | Laman: diskop.sumutprov.go.id
                        </p>
                    </div>
                </div>

                <div class="text-center text-xs font-black text-gray-900 uppercase tracking-widest pt-2 underline underline-offset-4">
                    LEMBAR HASIL PEMERIKSAAN KESEHATAN KOPERASI (LHP)
                </div>

                <!-- SURAT TUGAS & KOPERASI DETAILS -->
                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div class="space-y-1">
                        <div class="text-gray-500 font-medium">Nomor Surat Tugas:</div>
                        <div class="font-bold text-gray-900 font-mono">{{ pengawasan.no_surat_tugas }}</div>
                        <div class="text-gray-500 font-medium mt-2">Tanggal Pemeriksaan:</div>
                        <div class="font-bold text-gray-900">{{ pengawasan.tanggal_pemeriksaan }}</div>
                    </div>

                    <div class="space-y-1 text-right">
                        <div class="text-gray-500 font-medium">Objek Koperasi:</div>
                        <div class="font-bold text-gray-900">{{ pengawasan.koperasi?.nama_koperasi }}</div>
                        <div class="text-gray-500 font-mono text-[11px]">{{ pengawasan.koperasi?.no_badan_hukum }}</div>
                        <div class="text-gray-500 text-[11px] font-medium">{{ pengawasan.koperasi?.kabupaten_kota }}</div>
                    </div>
                </div>

                <!-- 4 ASPECTS BREAKDOWN TABLE -->
                <div class="space-y-2">
                    <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider">
                        A. Rincian Penilaian 4 Aspek Kesehatan
                    </h3>
                    <table class="w-full text-xs text-left border border-gray-200 rounded-xl overflow-hidden">
                        <thead class="bg-gray-50 text-gray-600 font-bold uppercase text-[10px]">
                            <tr>
                                <th class="p-3 border-b border-gray-200">Aspek Penilaian</th>
                                <th class="p-3 border-b border-gray-200 text-center">Bobot</th>
                                <th class="p-3 border-b border-gray-200 text-center">Skor Terhitung</th>
                                <th class="p-3 border-b border-gray-200 text-center">Kontribusi Skor</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-gray-800">
                            <tr>
                                <td class="p-3 font-medium">1. Tata Kelola Koperasi</td>
                                <td class="p-3 text-center">30%</td>
                                <td class="p-3 text-center font-bold text-gray-900">{{ pengawasan.skor_tata_kelola }}</td>
                                <td class="p-3 text-center font-mono font-semibold">{{ (pengawasan.skor_tata_kelola * 0.30).toFixed(2) }}</td>
                            </tr>
                            <tr>
                                <td class="p-3 font-medium">2. Profil Risiko Koperasi</td>
                                <td class="p-3 text-center">25%</td>
                                <td class="p-3 text-center font-bold text-gray-900">{{ pengawasan.skor_profil_risiko }}</td>
                                <td class="p-3 text-center font-mono font-semibold">{{ (pengawasan.skor_profil_risiko * 0.25).toFixed(2) }}</td>
                            </tr>
                            <tr>
                                <td class="p-3 font-medium">3. Kinerja Keuangan Koperasi</td>
                                <td class="p-3 text-center">30%</td>
                                <td class="p-3 text-center font-bold text-gray-900">{{ pengawasan.skor_kinerja_keuangan }}</td>
                                <td class="p-3 text-center font-mono font-semibold">{{ (pengawasan.skor_kinerja_keuangan * 0.30).toFixed(2) }}</td>
                            </tr>
                            <tr>
                                <td class="p-3 font-medium">4. Permodalan Koperasi</td>
                                <td class="p-3 text-center">15%</td>
                                <td class="p-3 text-center font-bold text-gray-900">{{ pengawasan.skor_permodalan }}</td>
                                <td class="p-3 text-center font-mono font-semibold">{{ (pengawasan.skor_permodalan * 0.15).toFixed(2) }}</td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-gray-100 font-bold">
                            <tr>
                                <td colspan="3" class="p-3 text-right uppercase text-gray-700">SKOR TOTAL AKHIR:</td>
                                <td class="p-3 text-center font-mono text-emerald-700 font-bold text-sm">
                                    {{ pengawasan.skor_total }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- PREDIKAT KESEHATAN RESULT BOX -->
                <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200 flex items-center justify-between">
                    <div>
                        <div class="text-[11px] font-bold text-gray-500 uppercase">Kesimpulan Predikat Kesehatan:</div>
                        <div class="text-base font-extrabold text-gray-900 mt-0.5">
                            KOPERASI {{ pengawasan.predikat_kesehatan.toUpperCase() }}
                        </div>
                    </div>
                    <span class="inline-flex px-4 py-1.5 rounded-full text-xs font-bold border" :class="getPredikatBadge(pengawasan.predikat_kesehatan)">
                        {{ pengawasan.predikat_kesehatan }}
                    </span>
                </div>

                <!-- MATRIKS TEMUAN AUDIT -->
                <div v-if="pengawasan.temuans && pengawasan.temuans.length > 0" class="space-y-2">
                    <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider">
                        B. Matriks Temuan & Rekomendasi Pemeriksaan
                    </h3>
                    <table class="w-full text-xs text-left border border-gray-200 rounded-xl overflow-hidden">
                        <thead class="bg-gray-50 text-gray-600 font-bold uppercase text-[10px]">
                            <tr>
                                <th class="p-2.5 border-b border-gray-200">Aspek</th>
                                <th class="p-2.5 border-b border-gray-200">Temuan Lapangan</th>
                                <th class="p-2.5 border-b border-gray-200">Rekomendasi Tim</th>
                                <th class="p-2.5 border-b border-gray-200 text-center">Risiko</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-gray-800">
                            <tr v-for="t in pengawasan.temuans" :key="t.id">
                                <td class="p-2.5 font-bold text-gray-900">{{ t.aspek_temuan }}</td>
                                <td class="p-2.5 font-medium">{{ t.deskripsi_temuan }}</td>
                                <td class="p-2.5 text-emerald-700 font-bold">{{ t.rekomendasi }}</td>
                                <td class="p-2.5 text-center">
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold border" :class="getRiskBadge(t.tingkat_risiko)">
                                        {{ t.tingkat_risiko }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- SIGNATURE BOX FOR PRINT -->
                <div class="pt-10 grid grid-cols-2 gap-8 text-center text-xs print-signature print:flex print:justify-between print:pt-6">
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

