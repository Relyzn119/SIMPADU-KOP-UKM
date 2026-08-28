<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import {
    Building2,
    ArrowLeft,
    FileSpreadsheet,
    ShieldCheck,
    AlertTriangle,
    Users,
    DollarSign,
    MapPin,
    Calendar,
    CheckCircle2,
    Clock,
    FileText
} from 'lucide-vue-next';

const props = defineProps<{
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
        case 'Sehat': return 'bg-emerald-50 text-emerald-700 border-emerald-200';
        case 'Cukup Sehat': return 'bg-cyan-50 text-cyan-700 border-cyan-200';
        case 'Dalam Pengawasan': return 'bg-amber-50 text-amber-700 border-amber-200';
        case 'Pengawasan Khusus': return 'bg-rose-50 text-rose-700 border-rose-200';
        default: return 'bg-gray-100 text-gray-600 border-gray-200';
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
        <Head :title="`Detail Koperasi - ${koperasi.nama_koperasi}`" />

        <div class="space-y-6">
            <!-- TOP BAR BACK ACTION -->
            <div class="flex items-center justify-between">
                <Link 
                    href="/koperasi" 
                    class="inline-flex items-center gap-2 text-xs font-semibold text-gray-500 hover:text-emerald-600 transition"
                >
                    <ArrowLeft class="w-4 h-4" />
                    Kembali ke Daftar Koperasi
                </Link>
            </div>

            <!-- PROFILE HERO BANNER CARD -->
            <div class="rounded-3xl bg-slate-900 text-white p-6 sm:p-8 shadow-xl space-y-6 relative overflow-hidden">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                Koperasi {{ koperasi.jenis_koperasi }}
                            </span>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                {{ koperasi.kabupaten_kota }}
                            </span>
                            <span 
                                class="px-3 py-1 rounded-full text-xs font-bold border"
                                :class="koperasi.status_keaktifan === 'Aktif' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40' : 'bg-rose-500/20 text-rose-300 border-rose-500/40'"
                            >
                                {{ koperasi.status_keaktifan }}
                            </span>
                        </div>

                        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                            {{ koperasi.nama_koperasi }}
                        </h1>

                        <div class="text-xs text-slate-300 flex flex-wrap items-center gap-4">
                            <span class="font-mono text-slate-200">No. BH: {{ koperasi.no_badan_hukum }}</span>
                            <span>•</span>
                            <span class="font-mono text-slate-200">NIK: {{ koperasi.nik_koperasi || '-' }}</span>
                            <span>•</span>
                            <span>Berdiri Tahun {{ koperasi.tahun_berdiri }}</span>
                        </div>
                    </div>

                    <!-- Health Badge Box -->
                    <div class="p-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 flex items-center gap-4 shrink-0">
                        <div class="w-12 h-12 rounded-xl bg-emerald-500 text-slate-900 flex items-center justify-center font-extrabold text-lg shadow-sm">
                            {{ koperasi.skor_kesehatan_terakhir || '0' }}
                        </div>
                        <div>
                            <div class="text-[10px] font-bold text-slate-300 uppercase tracking-wider">Predikat Kesehatan</div>
                            <div 
                                v-if="koperasi.predikat_kesehatan" 
                                class="text-xs font-extrabold mt-0.5 px-2.5 py-0.5 rounded-md inline-block border"
                                :class="getPredikatBadge(koperasi.predikat_kesehatan)"
                            >
                                {{ koperasi.predikat_kesehatan }}
                            </div>
                            <div v-else class="text-xs text-slate-400 italic mt-0.5">Belum Diperiksa</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DETAILS GRID CARDS -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: Kelembagaan & Pengurus -->
                <div class="space-y-6">
                    <!-- Kelembagaan Card -->
                    <div class="p-6 rounded-3xl bg-white border border-gray-200/70 shadow-2xs space-y-4">
                        <h2 class="text-xs font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2 border-b border-gray-100 pb-3">
                            <MapPin class="w-4 h-4 text-emerald-600" />
                            Alamat & Domisili
                        </h2>
                        <p class="text-xs text-gray-600 leading-relaxed font-medium">{{ koperasi.alamat }}</p>
                    </div>

                    <!-- Pengurus Card -->
                    <div class="p-6 rounded-3xl bg-white border border-gray-200/70 shadow-2xs space-y-4">
                        <h2 class="text-xs font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2 border-b border-gray-100 pb-3">
                            <Users class="w-4 h-4 text-indigo-600" />
                            Susunan Pengurus & Pengawas
                        </h2>

                        <div v-if="koperasi.pengurus" class="space-y-3 text-xs">
                            <div class="flex justify-between py-1.5 border-b border-gray-100">
                                <span class="text-gray-500 font-medium">Ketua Pengurus:</span>
                                <span class="font-bold text-gray-900">{{ koperasi.pengurus.ketua }}</span>
                            </div>
                            <div class="flex justify-between py-1.5 border-b border-gray-100">
                                <span class="text-gray-500 font-medium">Sekretaris:</span>
                                <span class="font-bold text-gray-900">{{ koperasi.pengurus.sekretaris }}</span>
                            </div>
                            <div class="flex justify-between py-1.5 border-b border-gray-100">
                                <span class="text-gray-500 font-medium">Bendahara:</span>
                                <span class="font-bold text-gray-900">{{ koperasi.pengurus.bendahara }}</span>
                            </div>
                            <div class="flex justify-between py-1.5 border-b border-gray-100">
                                <span class="text-gray-500 font-medium">Ketua Pengawas:</span>
                                <span class="font-bold text-gray-900">{{ koperasi.pengurus.ketua_pengawas }}</span>
                            </div>
                            <div v-if="koperasi.pengurus.manajer" class="flex justify-between py-1.5 border-b border-gray-100">
                                <span class="text-gray-500 font-medium">Manajer:</span>
                                <span class="font-bold text-gray-900">{{ koperasi.pengurus.manajer }}</span>
                            </div>
                            <div class="flex justify-between py-1.5 border-b border-gray-100">
                                <span class="text-gray-500 font-medium">Periode Jabatan:</span>
                                <span class="font-mono font-bold text-emerald-600">{{ koperasi.pengurus.periode_jabatan }}</span>
                            </div>
                            <div class="pt-2 flex justify-between items-center text-gray-700">
                                <span class="font-medium">Jumlah Anggota:</span>
                                <span class="font-bold text-gray-900">
                                    {{ (koperasi.pengurus.jumlah_anggota_pria || 0) + (koperasi.pengurus.jumlah_anggota_wanita || 0) }} Orang
                                    <span class="text-[10px] text-gray-400">({{ koperasi.pengurus.jumlah_anggota_pria }} L / {{ koperasi.pengurus.jumlah_anggota_wanita }} P)</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Financial & Audit History -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Ringkasan Keuangan Card -->
                    <div class="p-6 rounded-3xl bg-white border border-gray-200/70 shadow-2xs space-y-4">
                        <h2 class="text-xs font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2 border-b border-gray-100 pb-3">
                            <DollarSign class="w-4 h-4 text-emerald-600" />
                            Ringkasan Keuangan Utama
                        </h2>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-1">
                            <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200/80">
                                <div class="text-[10px] font-bold text-gray-400 uppercase">Total Aset</div>
                                <div class="text-sm font-bold text-gray-900 font-mono mt-1">{{ formatRupiah(koperasi.aset) }}</div>
                            </div>
                            <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200/80">
                                <div class="text-[10px] font-bold text-gray-400 uppercase">Modal Sendiri</div>
                                <div class="text-sm font-bold text-gray-900 font-mono mt-1">{{ formatRupiah(koperasi.modal_sendiri) }}</div>
                            </div>
                            <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200/80">
                                <div class="text-[10px] font-bold text-gray-400 uppercase">Volume Usaha</div>
                                <div class="text-sm font-bold text-gray-900 font-mono mt-1">{{ formatRupiah(koperasi.volume_usaha) }}</div>
                            </div>
                            <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200/80">
                                <div class="text-[10px] font-bold text-gray-400 uppercase">SHU Tahun Lalu</div>
                                <div class="text-sm font-bold text-emerald-600 font-mono mt-1">{{ formatRupiah(koperasi.shu) }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Riwayat Pelaksanaan RAT Card -->
                    <div class="p-6 rounded-3xl bg-white border border-gray-200/70 shadow-2xs space-y-4">
                        <h2 class="text-xs font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2 border-b border-gray-100 pb-3">
                            <FileSpreadsheet class="w-4 h-4 text-teal-600" />
                            Riwayat Pelaksanaan RAT (Rapat Anggota Tahunan)
                        </h2>

                        <div v-if="koperasi.rats && koperasi.rats.length > 0" class="space-y-3">
                            <div 
                                v-for="rat in koperasi.rats" 
                                :key="rat.id"
                                class="p-4 rounded-2xl bg-gray-50 border border-gray-200/80 flex items-center justify-between gap-4"
                            >
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-gray-900 text-xs">Tahun Buku {{ rat.tahun_buku }}</span>
                                        <span 
                                            class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold border"
                                            :class="rat.status_rat === 'Sudah RAT Tepat Waktu' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200'"
                                        >
                                            {{ rat.status_rat }}
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-gray-500 font-medium mt-1">
                                        Dilaksanakan tgl {{ rat.tanggal_rat }} di {{ rat.tempat_pelaksanaan }} (Hadir: {{ rat.jumlah_anggota_hadir }} orang)
                                    </div>
                                </div>

                                <div v-if="rat.file_lpj_path" class="text-right">
                                    <a 
                                        :href="`/${rat.file_lpj_path}`" 
                                        target="_blank"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold hover:bg-emerald-100 transition"
                                    >
                                        <FileText class="w-3.5 h-3.5" />
                                        Berkas LPJ
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-xs text-gray-400 italic py-2">Belum ada riwayat data RAT terdaftar.</div>
                    </div>

                    <!-- Riwayat Pengawasan Card -->
                    <div class="p-6 rounded-3xl bg-white border border-gray-200/70 shadow-2xs space-y-4">
                        <h2 class="text-xs font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2 border-b border-gray-100 pb-3">
                            <ShieldCheck class="w-4 h-4 text-indigo-600" />
                            Riwayat Pemeriksaan Kesehatan & Temuan
                        </h2>

                        <div v-if="koperasi.pengawasans && koperasi.pengawasans.length > 0" class="space-y-4">
                            <div 
                                v-for="p in koperasi.pengawasans" 
                                :key="p.id"
                                class="p-4 rounded-2xl bg-gray-50 border border-gray-200/80 space-y-3"
                            >
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-xs font-bold text-gray-900">No. Surat Tugas: {{ p.no_surat_tugas }}</div>
                                        <div class="text-[11px] text-gray-500 font-medium">Tgl Pemeriksaan: {{ p.tanggal_pemeriksaan }} oleh {{ p.nama_tim_pengawas }}</div>
                                    </div>

                                    <span 
                                        class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-bold border"
                                        :class="getPredikatBadge(p.predikat_kesehatan)"
                                    >
                                        {{ p.predikat_kesehatan }} (Skor Total: {{ p.skor_total }})
                                    </span>
                                </div>

                                <!-- Aspect Scores -->
                                <div class="grid grid-cols-4 gap-2 text-[11px] bg-white p-3 rounded-xl border border-gray-200/80 text-gray-600">
                                    <div>Tata Kelola: <span class="font-bold text-gray-900">{{ p.skor_tata_kelola }}</span></div>
                                    <div>Profil Risiko: <span class="font-bold text-gray-900">{{ p.skor_profil_risiko }}</span></div>
                                    <div>Kinerja Keuangan: <span class="font-bold text-gray-900">{{ p.skor_kinerja_keuangan }}</span></div>
                                    <div>Permodalan: <span class="font-bold text-gray-900">{{ p.skor_permodalan }}</span></div>
                                </div>

                                <!-- Temuan list if any -->
                                <div v-if="p.temuans && p.temuans.length > 0" class="pt-2 border-t border-gray-200 space-y-2">
                                    <div class="text-[11px] font-bold text-amber-700 uppercase">Matriks Temuan Audit:</div>
                                    <div 
                                        v-for="t in p.temuans" 
                                        :key="t.id"
                                        class="p-3 rounded-xl bg-white border border-gray-200/80 text-xs space-y-1"
                                    >
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold text-gray-900">[{{ t.aspek_temuan }}] {{ t.deskripsi_temuan }}</span>
                                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold border" :class="getRiskBadge(t.tingkat_risiko)">
                                                Risiko {{ t.tingkat_risiko }}
                                            </span>
                                        </div>
                                        <div class="text-[11px] text-emerald-700 font-semibold">Rekomendasi: {{ t.rekomendasi }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-xs text-gray-400 italic py-2">Belum ada riwayat pemeriksaan kesehatan terdaftar.</div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

