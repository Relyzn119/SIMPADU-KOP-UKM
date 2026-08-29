<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, DollarSign, FileSpreadsheet, FileText, MapPin, ShieldCheck, Users } from 'lucide-vue-next';

defineProps<{
    koperasi: {
        id: number;
        no_badan_hukum: string;
        nik_koperasi: string;
        nama_koperasi: string;
        jenis_koperasi: string;
        kabupaten_kota: string;
        alamat: string;
        tahun_berdiri: number;
        status_keaktifan: string;
        aset: number;
        modal_sendiri: number;
        volume_usaha: number;
        shu: number;
        skor_kesehatan_terakhir: number;
        predikat_kesehatan: string;
        pengurus?: {
            ketua: string;
            sekretaris: string;
            bendahara: string;
            ketua_pengawas: string;
            manajer: string;
            jumlah_anggota_pria: number;
            jumlah_anggota_wanita: number;
            periode_jabatan: string;
        };
        rats?: Array<{
            id: number;
            tahun_buku: number;
            tanggal_rat: string;
            tempat_pelaksanaan: string;
            jumlah_anggota_hadir: number;
            kuorum: boolean;
            status_rat: string;
            file_lpj_path: string;
            catatan: string;
        }>;
        pengawasans?: Array<{
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
            temuans?: Array<{
                id: number;
                aspek_temuan: string;
                deskripsi_temuan: string;
                rekomendasi: string;
                batas_waktu: string;
                tingkat_risiko: string;
                status_tindak_lanjut: string;
            }>;
        }>;
    };
}>();

const formatRupiah = (val: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
};

const getPredikatBadge = (predikat: string) => {
    switch (predikat) {
        case 'Sehat':
            return 'bg-emerald-50 text-emerald-700 border-emerald-200';
        case 'Cukup Sehat':
            return 'bg-cyan-50 text-cyan-700 border-cyan-200';
        case 'Dalam Pengawasan':
            return 'bg-amber-50 text-amber-700 border-amber-200';
        case 'Pengawasan Khusus':
            return 'bg-rose-50 text-rose-700 border-rose-200';
        default:
            return 'bg-gray-100 text-gray-600 border-gray-200';
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
        <Head :title="`Detail Koperasi - ${koperasi.nama_koperasi}`" />

        <div class="space-y-6">
            <!-- TOP BAR BACK ACTION -->
            <div class="flex items-center justify-between">
                <Link href="/koperasi" class="inline-flex items-center gap-2 text-xs font-semibold text-gray-500 transition hover:text-emerald-600">
                    <ArrowLeft class="h-4 w-4" />
                    Kembali ke Daftar Koperasi
                </Link>
            </div>

            <!-- PROFILE HERO BANNER CARD -->
            <div class="relative space-y-6 overflow-hidden rounded-3xl bg-slate-900 p-6 text-white shadow-xl sm:p-8">
                <div class="relative z-10 flex flex-col justify-between gap-6 md:flex-row md:items-center">
                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full border border-emerald-500/30 bg-emerald-500/20 px-3 py-1 text-xs font-bold text-emerald-300">
                                Koperasi {{ koperasi.jenis_koperasi }}
                            </span>
                            <span class="rounded-full border border-blue-500/30 bg-blue-500/20 px-3 py-1 text-xs font-bold text-blue-300">
                                {{ koperasi.kabupaten_kota }}
                            </span>
                            <span
                                class="rounded-full border px-3 py-1 text-xs font-bold"
                                :class="
                                    koperasi.status_keaktifan === 'Aktif'
                                        ? 'border-emerald-500/40 bg-emerald-500/20 text-emerald-300'
                                        : 'border-rose-500/40 bg-rose-500/20 text-rose-300'
                                "
                            >
                                {{ koperasi.status_keaktifan }}
                            </span>
                        </div>

                        <h1 class="text-2xl font-extrabold tracking-tight text-white sm:text-3xl">
                            {{ koperasi.nama_koperasi }}
                        </h1>

                        <div class="flex flex-wrap items-center gap-4 text-xs text-slate-300">
                            <span class="font-mono text-slate-200">No. BH: {{ koperasi.no_badan_hukum }}</span>
                            <span>•</span>
                            <span class="font-mono text-slate-200">NIK: {{ koperasi.nik_koperasi || '-' }}</span>
                            <span>•</span>
                            <span>Berdiri Tahun {{ koperasi.tahun_berdiri }}</span>
                        </div>
                    </div>

                    <!-- Health Badge Box -->
                    <div class="flex shrink-0 items-center gap-4 rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur-md">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-500 text-lg font-extrabold text-slate-900 shadow-sm"
                        >
                            {{ koperasi.skor_kesehatan_terakhir || '0' }}
                        </div>
                        <div>
                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-300">Predikat Kesehatan</div>
                            <div
                                v-if="koperasi.predikat_kesehatan"
                                class="mt-0.5 inline-block rounded-md border px-2.5 py-0.5 text-xs font-extrabold"
                                :class="getPredikatBadge(koperasi.predikat_kesehatan)"
                            >
                                {{ koperasi.predikat_kesehatan }}
                            </div>
                            <div v-else class="mt-0.5 text-xs italic text-slate-400">Belum Diperiksa</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DETAILS GRID CARDS -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Left Column: Kelembagaan & Pengurus -->
                <div class="space-y-6">
                    <!-- Kelembagaan Card -->
                    <div class="shadow-2xs space-y-4 rounded-3xl border border-gray-200/70 bg-white p-6">
                        <h2 class="flex items-center gap-2 border-b border-gray-100 pb-3 text-xs font-bold uppercase tracking-wider text-gray-900">
                            <MapPin class="h-4 w-4 text-emerald-600" />
                            Alamat & Domisili
                        </h2>
                        <p class="text-xs font-medium leading-relaxed text-gray-600">{{ koperasi.alamat }}</p>
                    </div>

                    <!-- Pengurus Card -->
                    <div class="shadow-2xs space-y-4 rounded-3xl border border-gray-200/70 bg-white p-6">
                        <h2 class="flex items-center gap-2 border-b border-gray-100 pb-3 text-xs font-bold uppercase tracking-wider text-gray-900">
                            <Users class="h-4 w-4 text-indigo-600" />
                            Susunan Pengurus & Pengawas
                        </h2>

                        <div v-if="koperasi.pengurus" class="space-y-3 text-xs">
                            <div class="flex justify-between border-b border-gray-100 py-1.5">
                                <span class="font-medium text-gray-500">Ketua Pengurus:</span>
                                <span class="font-bold text-gray-900">{{ koperasi.pengurus.ketua }}</span>
                            </div>
                            <div class="flex justify-between border-b border-gray-100 py-1.5">
                                <span class="font-medium text-gray-500">Sekretaris:</span>
                                <span class="font-bold text-gray-900">{{ koperasi.pengurus.sekretaris }}</span>
                            </div>
                            <div class="flex justify-between border-b border-gray-100 py-1.5">
                                <span class="font-medium text-gray-500">Bendahara:</span>
                                <span class="font-bold text-gray-900">{{ koperasi.pengurus.bendahara }}</span>
                            </div>
                            <div class="flex justify-between border-b border-gray-100 py-1.5">
                                <span class="font-medium text-gray-500">Ketua Pengawas:</span>
                                <span class="font-bold text-gray-900">{{ koperasi.pengurus.ketua_pengawas }}</span>
                            </div>
                            <div v-if="koperasi.pengurus.manajer" class="flex justify-between border-b border-gray-100 py-1.5">
                                <span class="font-medium text-gray-500">Manajer:</span>
                                <span class="font-bold text-gray-900">{{ koperasi.pengurus.manajer }}</span>
                            </div>
                            <div class="flex justify-between border-b border-gray-100 py-1.5">
                                <span class="font-medium text-gray-500">Periode Jabatan:</span>
                                <span class="font-mono font-bold text-emerald-600">{{ koperasi.pengurus.periode_jabatan }}</span>
                            </div>
                            <div class="flex items-center justify-between pt-2 text-gray-700">
                                <span class="font-medium">Jumlah Anggota:</span>
                                <span class="font-bold text-gray-900">
                                    {{ (koperasi.pengurus.jumlah_anggota_pria || 0) + (koperasi.pengurus.jumlah_anggota_wanita || 0) }} Orang
                                    <span class="text-[10px] text-gray-400"
                                        >({{ koperasi.pengurus.jumlah_anggota_pria }} L / {{ koperasi.pengurus.jumlah_anggota_wanita }} P)</span
                                    >
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Financial & Audit History -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Ringkasan Keuangan Card -->
                    <div class="shadow-2xs space-y-4 rounded-3xl border border-gray-200/70 bg-white p-6">
                        <h2 class="flex items-center gap-2 border-b border-gray-100 pb-3 text-xs font-bold uppercase tracking-wider text-gray-900">
                            <DollarSign class="h-4 w-4 text-emerald-600" />
                            Ringkasan Keuangan Utama
                        </h2>

                        <div class="grid grid-cols-2 gap-4 pt-1 sm:grid-cols-4">
                            <div class="rounded-2xl border border-gray-200/80 bg-gray-50 p-4">
                                <div class="text-[10px] font-bold uppercase text-gray-400">Total Aset</div>
                                <div class="mt-1 font-mono text-sm font-bold text-gray-900">{{ formatRupiah(koperasi.aset) }}</div>
                            </div>
                            <div class="rounded-2xl border border-gray-200/80 bg-gray-50 p-4">
                                <div class="text-[10px] font-bold uppercase text-gray-400">Modal Sendiri</div>
                                <div class="mt-1 font-mono text-sm font-bold text-gray-900">{{ formatRupiah(koperasi.modal_sendiri) }}</div>
                            </div>
                            <div class="rounded-2xl border border-gray-200/80 bg-gray-50 p-4">
                                <div class="text-[10px] font-bold uppercase text-gray-400">Volume Usaha</div>
                                <div class="mt-1 font-mono text-sm font-bold text-gray-900">{{ formatRupiah(koperasi.volume_usaha) }}</div>
                            </div>
                            <div class="rounded-2xl border border-gray-200/80 bg-gray-50 p-4">
                                <div class="text-[10px] font-bold uppercase text-gray-400">SHU Tahun Lalu</div>
                                <div class="mt-1 font-mono text-sm font-bold text-emerald-600">{{ formatRupiah(koperasi.shu) }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Riwayat Pelaksanaan RAT Card -->
                    <div class="shadow-2xs space-y-4 rounded-3xl border border-gray-200/70 bg-white p-6">
                        <h2 class="flex items-center gap-2 border-b border-gray-100 pb-3 text-xs font-bold uppercase tracking-wider text-gray-900">
                            <FileSpreadsheet class="h-4 w-4 text-teal-600" />
                            Riwayat Pelaksanaan RAT (Rapat Anggota Tahunan)
                        </h2>

                        <div v-if="koperasi.rats && koperasi.rats.length > 0" class="space-y-3">
                            <div
                                v-for="rat in koperasi.rats"
                                :key="rat.id"
                                class="flex items-center justify-between gap-4 rounded-2xl border border-gray-200/80 bg-gray-50 p-4"
                            >
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-gray-900">Tahun Buku {{ rat.tahun_buku }}</span>
                                        <span
                                            class="inline-flex rounded border px-2 py-0.5 text-[10px] font-bold"
                                            :class="
                                                rat.status_rat === 'Sudah RAT Tepat Waktu'
                                                    ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                                                    : 'border-amber-200 bg-amber-50 text-amber-700'
                                            "
                                        >
                                            {{ rat.status_rat }}
                                        </span>
                                    </div>
                                    <div class="mt-1 text-[11px] font-medium text-gray-500">
                                        Dilaksanakan tgl {{ rat.tanggal_rat }} di {{ rat.tempat_pelaksanaan }} (Hadir:
                                        {{ rat.jumlah_anggota_hadir }} orang)
                                    </div>
                                </div>

                                <div v-if="rat.file_lpj_path" class="text-right">
                                    <a
                                        :href="`/${rat.file_lpj_path}`"
                                        target="_blank"
                                        class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700 transition hover:bg-emerald-100"
                                    >
                                        <FileText class="h-3.5 w-3.5" />
                                        Berkas LPJ
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div v-else class="py-2 text-xs italic text-gray-400">Belum ada riwayat data RAT terdaftar.</div>
                    </div>

                    <!-- Riwayat Pengawasan Card -->
                    <div class="shadow-2xs space-y-4 rounded-3xl border border-gray-200/70 bg-white p-6">
                        <h2 class="flex items-center gap-2 border-b border-gray-100 pb-3 text-xs font-bold uppercase tracking-wider text-gray-900">
                            <ShieldCheck class="h-4 w-4 text-indigo-600" />
                            Riwayat Pemeriksaan Kesehatan & Temuan
                        </h2>

                        <div v-if="koperasi.pengawasans && koperasi.pengawasans.length > 0" class="space-y-4">
                            <div v-for="p in koperasi.pengawasans" :key="p.id" class="space-y-3 rounded-2xl border border-gray-200/80 bg-gray-50 p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-xs font-bold text-gray-900">No. Surat Tugas: {{ p.no_surat_tugas }}</div>
                                        <div class="text-[11px] font-medium text-gray-500">
                                            Tgl Pemeriksaan: {{ p.tanggal_pemeriksaan }} oleh {{ p.nama_tim_pengawas }}
                                        </div>
                                    </div>

                                    <span
                                        class="inline-flex rounded-full border px-2.5 py-1 text-[10px] font-bold"
                                        :class="getPredikatBadge(p.predikat_kesehatan)"
                                    >
                                        {{ p.predikat_kesehatan }} (Skor Total: {{ p.skor_total }})
                                    </span>
                                </div>

                                <!-- Aspect Scores -->
                                <div class="grid grid-cols-4 gap-2 rounded-xl border border-gray-200/80 bg-white p-3 text-[11px] text-gray-600">
                                    <div>
                                        Tata Kelola: <span class="font-bold text-gray-900">{{ p.skor_tata_kelola }}</span>
                                    </div>
                                    <div>
                                        Profil Risiko: <span class="font-bold text-gray-900">{{ p.skor_profil_risiko }}</span>
                                    </div>
                                    <div>
                                        Kinerja Keuangan: <span class="font-bold text-gray-900">{{ p.skor_kinerja_keuangan }}</span>
                                    </div>
                                    <div>
                                        Permodalan: <span class="font-bold text-gray-900">{{ p.skor_permodalan }}</span>
                                    </div>
                                </div>

                                <!-- Temuan list if any -->
                                <div v-if="p.temuans && p.temuans.length > 0" class="space-y-2 border-t border-gray-200 pt-2">
                                    <div class="text-[11px] font-bold uppercase text-amber-700">Matriks Temuan Audit:</div>
                                    <div
                                        v-for="t in p.temuans"
                                        :key="t.id"
                                        class="space-y-1 rounded-xl border border-gray-200/80 bg-white p-3 text-xs"
                                    >
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold text-gray-900">[{{ t.aspek_temuan }}] {{ t.deskripsi_temuan }}</span>
                                            <span
                                                class="inline-flex rounded border px-2 py-0.5 text-[10px] font-bold"
                                                :class="getRiskBadge(t.tingkat_risiko)"
                                            >
                                                Risiko {{ t.tingkat_risiko }}
                                            </span>
                                        </div>
                                        <div class="text-[11px] font-semibold text-emerald-700">Rekomendasi: {{ t.rekomendasi }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="py-2 text-xs italic text-gray-400">Belum ada riwayat pemeriksaan kesehatan terdaftar.</div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
