<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, CheckCircle2, Clock, FileText, Printer, User } from 'lucide-vue-next';

defineProps<{
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
        status_persetujuan_koperasi?: string;
        tanggapan_koperasi?: string;
        file_bukti_tindak_lanjut_path?: string;
        approved_at?: string;
        created_by?: { id: number; name: string };
        approved_by?: { id: number; name: string };
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

const formatDate = (dateStr?: string) => {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
};

const getPredikatBadge = (predikat: string) => {
    switch (predikat) {
        case 'Sehat':
            return 'bg-emerald-50 text-emerald-700 border-emerald-200';
        case 'Cukup Sehat':
            return 'bg-cyan-50 text-cyan-700 border-cyan-200';
        case 'Dalam Pengawasan':
            return 'bg-amber-50 text-amber-700 border-amber-200';
        default:
            return 'bg-rose-50 text-rose-700 border-rose-200';
    }
};

const getRiskBadge = (risiko: string) => {
    switch (risiko) {
        case 'Kritis':
            return 'bg-rose-50 text-rose-700 border-rose-200';
        case 'Tinggi':
            return 'bg-amber-50 text-amber-700 border-amber-200';
        case 'Sedang':
            return 'bg-yellow-50 text-yellow-700 border-yellow-200';
        default:
            return 'bg-blue-50 text-blue-700 border-blue-200';
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="`Lembar Hasil Pemeriksaan - ${pengawasan.koperasi?.nama_koperasi}`" />

        <div class="mx-auto max-w-4xl space-y-6">
            <!-- TOP BAR BACK & PRINT ACTION -->
            <div class="flex items-center justify-between print:hidden">
                <Link href="/pengawasan" class="inline-flex items-center gap-2 text-xs font-semibold text-gray-500 transition hover:text-emerald-600">
                    <ArrowLeft class="h-4 w-4" />
                    Kembali ke Daftar Pengawasan
                </Link>

                <button
                    @click="handlePrint"
                    class="shadow-xs flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white transition hover:bg-slate-800"
                >
                    <Printer class="h-4 w-4 text-emerald-400" />
                    Cetak Lembar Hasil (LHP)
                </button>
            </div>

            <!-- PERSETUJUAN KOPERASI STATUS ALERT BOX (NON-PRINT) -->
            <div
                v-if="pengawasan.status_persetujuan_koperasi === 'approved'"
                class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-xs text-emerald-900 print:hidden"
            >
                <CheckCircle2 class="h-5 w-5 shrink-0 text-emerald-600 mt-0.5" />
                <div class="space-y-1">
                    <strong class="font-bold">STATUS PERSETUJUAN: DISETUJUI OLEH PENGURUS KOPERASI</strong>
                    <p class="text-[11px] text-emerald-700">
                        Disetujui oleh <span class="font-bold">{{ pengawasan.approved_by?.name || 'Pengurus Koperasi' }}</span> pada
                        {{ formatDate(pengawasan.approved_at) }}.
                    </p>
                    <div v-if="pengawasan.tanggapan_koperasi" class="mt-2 rounded-xl bg-white/80 p-3 text-[11px] border border-emerald-200 text-emerald-900">
                        <strong>Tanggapan / Komitmen Pengurus:</strong> {{ pengawasan.tanggapan_koperasi }}
                    </div>
                    <div v-if="pengawasan.file_bukti_tindak_lanjut_path" class="pt-1">
                        <a
                            :href="`/file-download?path=${encodeURIComponent(pengawasan.file_bukti_tindak_lanjut_path)}`"
                            target="_blank"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-700 px-3 py-1.5 text-[11px] font-bold text-white transition hover:bg-emerald-800"
                        >
                            <FileText class="h-3.5 w-3.5" />
                            Lihat File Bukti Digital Tindak Lanjut
                        </a>
                    </div>
                </div>
            </div>

            <div
                v-else
                class="flex items-center gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-xs text-amber-900 print:hidden"
            >
                <Clock class="h-5 w-5 shrink-0 text-amber-600" />
                <div>
                    <strong class="font-bold">STATUS PERSETUJUAN: SEDANG DIPROSES (PENDING PENGURUS)</strong>
                    <p class="mt-0.5 text-[11px] text-amber-700">
                        LHP ini diterbitkan oleh Tim Pengawas Diskop Provsu ({{ pengawasan.created_by?.name || pengawasan.nama_tim_pengawas }}) dan menunggu peninjauan/persetujuan resmi dari Pengurus Koperasi.
                    </p>
                </div>
            </div>

            <!-- BERKAS BERITA ACARA PEMERIKSAAN (PDF) (NON-PRINT) -->
            <div
                v-if="pengawasan.file_berita_acara_path"
                class="flex items-center justify-between rounded-2xl border border-indigo-200 bg-indigo-50/70 p-4 text-xs text-indigo-900 print:hidden"
            >
                <div class="flex items-center gap-2.5">
                    <FileText class="h-5 w-5 shrink-0 text-indigo-600" />
                    <div>
                        <strong class="font-bold">Berkas Berita Acara Pemeriksaan (PDF)</strong>
                        <p class="text-[11px] text-indigo-700">Dokumen Berita Acara resmi hasil pengawasan kesehatan oleh Tim Pengawas.</p>
                    </div>
                </div>
                <a
                    :href="`/file-download?path=${encodeURIComponent(pengawasan.file_berita_acara_path)}`"
                    target="_blank"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-3.5 py-2 text-xs font-bold text-white shadow-xs transition hover:bg-indigo-700"
                >
                    <FileText class="h-4 w-4" />
                    Lihat Berita Acara (PDF)
                </a>
            </div>

            <!-- OFFICIAL REPORT CONTAINER (PRINT-READY) -->
            <div
                class="shadow-2xs space-y-6 rounded-3xl border border-gray-200/70 bg-white p-8 text-gray-900 sm:p-10 print:border-none print:bg-white print:p-0 print:text-slate-900 print:shadow-none"
            >
                <!-- KOP SURAT DINAS KOPERASI SUMUT -->
                <div class="flex items-center gap-4 border-b-4 border-double border-gray-900 pb-4 sm:gap-6">
                    <!-- LOGO EMBLEM PROVSU (LEFT) -->
                    <img src="/images/icon-provsu.svg" alt="Logo Pemprov Sumut" class="h-20 w-20 shrink-0 object-contain sm:h-24 sm:w-24" />

                    <!-- KOP TEXT (CENTER/RIGHT) -->
                    <div class="flex-1 space-y-0.5 text-center text-gray-900">
                        <h2 class="text-xs font-extrabold uppercase leading-tight tracking-wider text-gray-900 sm:text-sm">
                            PEMERINTAH PROVINSI SUMATERA UTARA
                        </h2>
                        <h1 class="text-sm font-black uppercase leading-tight tracking-wide text-gray-900 sm:text-base">
                            DINAS KOPERASI DAN USAHA KECIL MENENGAH
                        </h1>
                        <p class="text-[10px] font-medium text-gray-700 sm:text-[11px]">
                            Jalan Jenderal Gatot Subroto No. 222 Medan, Sumatera Utara 20118
                        </p>
                        <p class="text-[10px] font-medium text-gray-600 sm:text-[11px]">
                            Telepon: (061) 8452110, Faksimile: (061) 8452110 | Laman: diskop.sumutprov.go.id
                        </p>
                    </div>
                </div>

                <div class="pt-2 text-center text-xs font-black uppercase tracking-widest text-gray-900 underline underline-offset-4">
                    LEMBAR HASIL PEMERIKSAAN KESEHATAN KOPERASI (LHP)
                </div>

                <!-- SURAT TUGAS & KOPERASI DETAILS -->
                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div class="space-y-1">
                        <div class="font-medium text-gray-500">Nomor Surat Tugas:</div>
                        <div class="font-mono font-bold text-gray-900">{{ pengawasan.no_surat_tugas }}</div>
                        <div class="mt-2 font-medium text-gray-500">Tanggal Pemeriksaan:</div>
                        <div class="font-bold text-gray-900">{{ pengawasan.tanggal_pemeriksaan }}</div>
                        <div class="mt-2 flex items-center gap-1 font-medium text-indigo-700">
                            <User class="h-3.5 w-3.5 text-indigo-600" />
                            <span>Diterbitkan Oleh: {{ pengawasan.created_by?.name || pengawasan.nama_tim_pengawas }}</span>
                        </div>
                    </div>

                    <div class="space-y-1 text-right">
                        <div class="font-medium text-gray-500">Objek Koperasi:</div>
                        <div class="font-bold text-gray-900">{{ pengawasan.koperasi?.nama_koperasi }}</div>
                        <div class="font-mono text-[11px] text-gray-500">{{ pengawasan.koperasi?.no_badan_hukum }}</div>
                        <div class="text-[11px] font-medium text-gray-500">{{ pengawasan.koperasi?.kabupaten_kota }}</div>
                    </div>
                </div>

                <!-- 4 ASPECTS BREAKDOWN TABLE -->
                <div class="space-y-2">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-900">A. Rincian Penilaian 4 Aspek Kesehatan</h3>
                    <table class="w-full overflow-hidden rounded-xl border border-gray-200 text-left text-xs">
                        <thead class="bg-gray-50 text-[10px] font-bold uppercase text-gray-600">
                            <tr>
                                <th class="border-b border-gray-200 p-3">Aspek Penilaian</th>
                                <th class="border-b border-gray-200 p-3 text-center">Bobot</th>
                                <th class="border-b border-gray-200 p-3 text-center">Skor Terhitung</th>
                                <th class="border-b border-gray-200 p-3 text-center">Kontribusi Skor</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-gray-800">
                            <tr>
                                <td class="p-3 font-medium">1. Tata Kelola Koperasi</td>
                                <td class="p-3 text-center">30%</td>
                                <td class="p-3 text-center font-bold text-gray-900">{{ pengawasan.skor_tata_kelola }}</td>
                                <td class="p-3 text-center font-mono font-semibold">{{ (pengawasan.skor_tata_kelola * 0.3).toFixed(2) }}</td>
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
                                <td class="p-3 text-center font-mono font-semibold">{{ (pengawasan.skor_kinerja_keuangan * 0.3).toFixed(2) }}</td>
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
                                <td class="p-3 text-center font-mono text-sm font-bold text-emerald-700">
                                    {{ pengawasan.skor_total }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- PREDIKAT KESEHATAN RESULT BOX -->
                <div class="flex items-center justify-between rounded-2xl border border-gray-200 bg-gray-50 p-4">
                    <div>
                        <div class="text-[11px] font-bold uppercase text-gray-500">Kesimpulan Predikat Kesehatan:</div>
                        <div class="mt-0.5 text-base font-extrabold text-gray-900">KOPERASI {{ pengawasan.predikat_kesehatan.toUpperCase() }}</div>
                    </div>
                    <span
                        class="inline-flex rounded-full border px-4 py-1.5 text-xs font-bold"
                        :class="getPredikatBadge(pengawasan.predikat_kesehatan)"
                    >
                        {{ pengawasan.predikat_kesehatan }}
                    </span>
                </div>

                <!-- MATRIKS TEMUAN AUDIT -->
                <div v-if="pengawasan.temuans && pengawasan.temuans.length > 0" class="space-y-2">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-900">B. Matriks Temuan & Rekomendasi Pemeriksaan</h3>
                    <table class="w-full overflow-hidden rounded-xl border border-gray-200 text-left text-xs">
                        <thead class="bg-gray-50 text-[10px] font-bold uppercase text-gray-600">
                            <tr>
                                <th class="border-b border-gray-200 p-2.5">Aspek</th>
                                <th class="border-b border-gray-200 p-2.5">Temuan Lapangan</th>
                                <th class="border-b border-gray-200 p-2.5">Rekomendasi Tim</th>
                                <th class="border-b border-gray-200 p-2.5 text-center">Risiko</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-gray-800">
                            <tr v-for="t in pengawasan.temuans" :key="t.id">
                                <td class="p-2.5 font-bold text-gray-900">{{ t.aspek_temuan }}</td>
                                <td class="p-2.5 font-medium">{{ t.deskripsi_temuan }}</td>
                                <td class="p-2.5 font-bold text-emerald-700">{{ t.rekomendasi }}</td>
                                <td class="p-2.5 text-center">
                                    <span
                                        class="inline-flex rounded border px-2 py-0.5 text-[10px] font-bold"
                                        :class="getRiskBadge(t.tingkat_risiko)"
                                    >
                                        {{ t.tingkat_risiko }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- SIGNATURE BOX FOR PRINT -->
                <div class="print-signature grid grid-cols-2 gap-8 pt-10 text-center text-xs print:flex print:justify-between print:pt-6">
                    <div>
                        <div>Mengetahui & Menyetujui,</div>
                        <div class="font-bold">Pengurus Koperasi {{ pengawasan.koperasi?.nama_koperasi }}</div>
                        <div class="h-16"></div>
                        <div class="font-bold underline">({{ pengawasan.approved_by?.name || '________________________' }})</div>
                    </div>
                    <div>
                        <div>Medan, {{ pengawasan.tanggal_pemeriksaan }}</div>
                        <div class="font-bold">Tim Pengawas Diskop Provsu</div>
                        <div class="h-16"></div>
                        <div class="font-bold underline">{{ pengawasan.created_by?.name || pengawasan.nama_tim_pengawas }}</div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
