<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { router } from '@inertiajs/vue3';
import { AlertTriangle, Building2, FileSpreadsheet, Printer, ShieldCheck } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    jenisLaporan: string;
    tahunBuku: number;
    kabupatenSelected?: string;
    rekapWilayah: Array<any>;
    rekapRat: Array<any>;
    rekapKesehatan: Array<any>;
    rekapTemuan: Array<any>;
    kabupatenKotaList: string[];
    tanggalCetak: string;
}>();

const selectedJenis = ref(props.jenisLaporan);
const selectedTahun = ref(props.tahunBuku);
const selectedKab = ref(props.kabupatenSelected || '');

const applyFilters = () => {
    router.get(
        '/cetak',
        {
            jenis_laporan: selectedJenis.value,
            tahun_buku: selectedTahun.value,
            kabupaten_kota: selectedKab.value || undefined,
        },
        { preserveState: true },
    );
};

const handlePrint = () => {
    window.print();
};

const formatRupiah = (val: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);
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
        <div class="mx-auto max-w-5xl space-y-6">
            <!-- SELECTOR & PRINT ACTIONS (HIDDEN WHEN PRINTING) -->
            <div class="shadow-2xs space-y-4 rounded-3xl border border-gray-200/70 bg-white p-6 print:hidden">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div>
                        <h1 class="flex items-center gap-2 text-xl font-bold tracking-tight text-gray-900">
                            <Printer class="h-5 w-5 text-emerald-600" />
                            Modul Cetak Laporan Resmi Diskop Provsu
                        </h1>
                        <p class="mt-0.5 text-xs text-gray-500">
                            Pilih jenis format laporan, sesuaikan filter, dan cetak dokumen resmi ber-kop Dinas.
                        </p>
                    </div>

                    <button
                        @click="handlePrint"
                        class="shadow-xs flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-xs font-bold text-white transition hover:bg-slate-800"
                    >
                        <Printer class="h-4 w-4 text-emerald-400" />
                        Cetak Laporan (Ctrl + P)
                    </button>
                </div>

                <!-- Report Format Tabs / Selectors -->
                <div class="grid grid-cols-1 gap-3 pt-2 sm:grid-cols-4">
                    <button
                        @click="
                            selectedJenis = 'wilayah';
                            applyFilters();
                        "
                        class="flex items-center justify-center gap-2 rounded-xl border p-3 text-xs font-bold transition"
                        :class="
                            selectedJenis === 'wilayah'
                                ? 'shadow-xs border-slate-900 bg-slate-900 text-white'
                                : 'border-gray-200 bg-gray-50 text-gray-700 hover:bg-gray-100'
                        "
                    >
                        <Building2 class="h-4 w-4" />
                        1. Rekap Wilayah 33 Kab/Kota
                    </button>

                    <button
                        @click="
                            selectedJenis = 'rat';
                            applyFilters();
                        "
                        class="flex items-center justify-center gap-2 rounded-xl border p-3 text-xs font-bold transition"
                        :class="
                            selectedJenis === 'rat'
                                ? 'shadow-xs border-slate-900 bg-slate-900 text-white'
                                : 'border-gray-200 bg-gray-50 text-gray-700 hover:bg-gray-100'
                        "
                    >
                        <FileSpreadsheet class="h-4 w-4" />
                        2. Kepatuhan RAT Koperasi
                    </button>

                    <button
                        @click="
                            selectedJenis = 'kesehatan';
                            applyFilters();
                        "
                        class="flex items-center justify-center gap-2 rounded-xl border p-3 text-xs font-bold transition"
                        :class="
                            selectedJenis === 'kesehatan'
                                ? 'shadow-xs border-slate-900 bg-slate-900 text-white'
                                : 'border-gray-200 bg-gray-50 text-gray-700 hover:bg-gray-100'
                        "
                    >
                        <ShieldCheck class="h-4 w-4" />
                        3. Skor Kesehatan Koperasi
                    </button>

                    <button
                        @click="
                            selectedJenis = 'temuan';
                            applyFilters();
                        "
                        class="flex items-center justify-center gap-2 rounded-xl border p-3 text-xs font-bold transition"
                        :class="
                            selectedJenis === 'temuan'
                                ? 'shadow-xs border-slate-900 bg-slate-900 text-white'
                                : 'border-gray-200 bg-gray-50 text-gray-700 hover:bg-gray-100'
                        "
                    >
                        <AlertTriangle class="h-4 w-4" />
                        4. Matriks Temuan Audit
                    </button>
                </div>

                <!-- Secondary Filter Bar -->
                <div class="grid grid-cols-1 gap-3 border-t border-gray-100 pt-2 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-gray-700">Filter Tahun Buku RAT</label>
                        <select
                            v-model="selectedTahun"
                            @change="applyFilters"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-xs font-bold text-gray-900 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                        >
                            <option :value="2025">Tahun Buku 2025</option>
                            <option :value="2024">Tahun Buku 2024</option>
                            <option :value="2023">Tahun Buku 2023</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-gray-700">Filter Wilayah Kabupaten / Kota</label>
                        <select
                            v-model="selectedKab"
                            @change="applyFilters"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-xs font-bold text-gray-900 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                        >
                            <option value="">Seluruh 33 Kabupaten / Kota Sumut</option>
                            <option v-for="kab in kabupatenKotaList" :key="kab" :value="kab">{{ kab }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- OFFICIAL PRINTABLE REPORT CONTAINER (KOP SURAT PEMPROV SUMUT) -->
            <div
                class="shadow-2xs space-y-6 rounded-3xl border border-gray-200/70 bg-white p-8 text-gray-900 sm:p-10 print:border-none print:bg-white print:p-0 print:text-slate-900 print:shadow-none"
            >
                <!-- KOP SURAT RESMI -->
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
                        <p class="text-[10px] font-medium text-gray-600 sm:text-[11px]">Telepon: (061) 8452110, Faksimile: (061) 8452110</p>
                        <p class="text-[10px] font-medium text-gray-600 sm:text-[11px]">
                            Laman: diskop.sumutprov.go.id | Email: diskopukm@sumutprov.go.id
                        </p>
                    </div>
                </div>

                <!-- DOCUMENT TITLE -->
                <div class="pt-2 text-center text-xs font-black uppercase tracking-widest text-gray-900 underline decoration-2 sm:text-sm">
                    <span v-if="selectedJenis === 'wilayah'">LAPORAN REKAPITULASI DATA KOPERASI BINAAN PER KABUPATEN/KOTA</span>
                    <span v-else-if="selectedJenis === 'rat'">LAPORAN REKAPITULASI KEPATUHAN RAT (TAHUN BUKU {{ selectedTahun }})</span>
                    <span v-else-if="selectedJenis === 'kesehatan'">LAPORAN HASIL PENILAIAN SKOR KESEHATAN KOPERASI (PERMENKOP 9/2020)</span>
                    <span v-else-if="selectedJenis === 'temuan'">MATRIKS REKOMENDASI DAN TINDAK LANJUT TEMUAN PENGAWASAN</span>
                </div>

                <!-- REPORT FORMAT 1: REKAP WILAYAH -->
                <div v-if="selectedJenis === 'wilayah'" class="space-y-4">
                    <table class="w-full overflow-hidden rounded-xl border border-gray-200 text-left text-xs print:border-slate-400">
                        <thead class="border-b border-gray-200 bg-gray-50 text-[10px] font-bold uppercase text-gray-700">
                            <tr>
                                <th class="p-2.5 text-center font-bold">No</th>
                                <th class="p-2.5 font-bold">Kabupaten / Kota</th>
                                <th class="p-2.5 text-center font-bold">Total Koperasi</th>
                                <th class="p-2.5 text-center font-bold">Koperasi Aktif</th>
                                <th class="p-2.5 text-right font-bold">Total Aset (Rp)</th>
                                <th class="p-2.5 text-right font-bold">Volume Usaha (Rp)</th>
                                <th class="p-2.5 text-right font-bold">Total SHU (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-gray-800">
                            <tr v-for="(item, idx) in rekapWilayah" :key="item.kabupaten_kota">
                                <td class="p-2.5 text-center font-mono">{{ idx + 1 }}</td>
                                <td class="p-2.5 font-bold text-gray-900">{{ item.kabupaten_kota }}</td>
                                <td class="p-2.5 text-center font-bold text-gray-900">{{ item.total_koperasi }}</td>
                                <td class="p-2.5 text-center font-bold text-emerald-700">{{ item.koperasi_aktif }}</td>
                                <td class="p-2.5 text-right font-mono font-semibold">{{ formatRupiah(item.total_aset) }}</td>
                                <td class="p-2.5 text-right font-mono font-semibold">{{ formatRupiah(item.total_volume_usaha) }}</td>
                                <td class="p-2.5 text-right font-mono font-bold text-emerald-700">{{ formatRupiah(item.total_shu) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- REPORT FORMAT 2: KEPATUHAN RAT -->
                <div v-else-if="selectedJenis === 'rat'" class="space-y-4">
                    <table class="w-full overflow-hidden rounded-xl border border-gray-200 text-left text-xs print:border-slate-400">
                        <thead class="border-b border-gray-200 bg-gray-50 text-[10px] font-bold uppercase text-gray-700">
                            <tr>
                                <th class="p-2.5 text-center font-bold">No</th>
                                <th class="p-2.5 font-bold">Nama Koperasi & BH</th>
                                <th class="p-2.5 font-bold">Kabupaten / Kota</th>
                                <th class="p-2.5 text-center font-bold">Tanggal RAT</th>
                                <th class="p-2.5 text-center font-bold">Status Kuorum</th>
                                <th class="p-2.5 text-center font-bold">Status Kepatuhan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-gray-800">
                            <tr v-for="(item, idx) in rekapRat" :key="item.id">
                                <td class="p-2.5 text-center font-mono">{{ idx + 1 }}</td>
                                <td class="p-2.5">
                                    <div class="font-bold text-gray-900">{{ item.nama_koperasi }}</div>
                                    <div class="font-mono text-[11px] text-gray-500">{{ item.no_badan_hukum }}</div>
                                </td>
                                <td class="p-2.5 font-medium">{{ item.kabupaten_kota }}</td>
                                <td class="p-2.5 text-center font-mono font-medium">
                                    {{ item.rats && item.rats.length > 0 ? item.rats[0].tanggal_rat : '-' }}
                                </td>
                                <td class="p-2.5 text-center font-semibold">
                                    {{ item.rats && item.rats.length > 0 && item.rats[0].kuorum ? 'Kuorum' : 'Tidak Kuorum / N/A' }}
                                </td>
                                <td class="p-2.5 text-center">
                                    <span
                                        v-if="item.rats && item.rats.length > 0"
                                        class="inline-flex rounded-full border px-2.5 py-1 text-[10px] font-bold"
                                        :class="
                                            item.rats[0].status_rat === 'Sudah RAT Tepat Waktu'
                                                ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                                                : 'border-amber-200 bg-amber-50 text-amber-700'
                                        "
                                    >
                                        {{ item.rats[0].status_rat }}
                                    </span>
                                    <span v-else class="font-bold text-rose-700">Belum RAT</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- REPORT FORMAT 3: SKOR KESEHATAN KOPERASI -->
                <div v-else-if="selectedJenis === 'kesehatan'" class="space-y-4">
                    <table class="w-full overflow-hidden rounded-xl border border-gray-200 text-left text-xs print:border-slate-400">
                        <thead class="border-b border-gray-200 bg-gray-50 text-[10px] font-bold uppercase text-gray-700">
                            <tr>
                                <th class="p-2.5 text-center font-bold">No</th>
                                <th class="p-2.5 font-bold">Nama Koperasi & Wilayah</th>
                                <th class="p-2.5 text-center font-bold">Jenis</th>
                                <th class="p-2.5 text-center font-bold">Skor Total</th>
                                <th class="p-2.5 text-center font-bold">Predikat Kesehatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-gray-800">
                            <tr v-for="(item, idx) in rekapKesehatan" :key="item.id">
                                <td class="p-2.5 text-center font-mono">{{ idx + 1 }}</td>
                                <td class="p-2.5">
                                    <div class="font-bold text-gray-900">{{ item.nama_koperasi }}</div>
                                    <div class="text-[11px] text-gray-500">{{ item.kabupaten_kota }} • BH: {{ item.no_badan_hukum }}</div>
                                </td>
                                <td class="p-2.5 text-center font-semibold">{{ item.jenis_koperasi }}</td>
                                <td class="p-2.5 text-center font-mono text-sm font-bold text-emerald-700">
                                    {{ item.skor_kesehatan_terakhir }}
                                </td>
                                <td class="p-2.5 text-center">
                                    <span
                                        class="inline-flex rounded-full border px-2.5 py-1 text-[10px] font-bold"
                                        :class="getPredikatBadge(item.predikat_kesehatan)"
                                    >
                                        {{ item.predikat_kesehatan }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- REPORT FORMAT 4: MATRIKS TEMUAN AUDIT -->
                <div v-else-if="selectedJenis === 'temuan'" class="space-y-4">
                    <table class="w-full overflow-hidden rounded-xl border border-gray-200 text-left text-xs print:border-slate-400">
                        <thead class="border-b border-gray-200 bg-gray-50 text-[10px] font-bold uppercase text-gray-700">
                            <tr>
                                <th class="p-2.5 text-center font-bold">No</th>
                                <th class="p-2.5 font-bold">Koperasi & Wilayah</th>
                                <th class="p-2.5 font-bold">Deskripsi Temuan</th>
                                <th class="p-2.5 font-bold">Rekomendasi</th>
                                <th class="p-2.5 text-center font-bold">Risiko</th>
                                <th class="p-2.5 text-center font-bold">Status TL</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-gray-800">
                            <tr v-for="(item, idx) in rekapTemuan" :key="item.id">
                                <td class="p-2.5 text-center font-mono">{{ idx + 1 }}</td>
                                <td class="p-2.5">
                                    <div class="font-bold text-gray-900">{{ item.koperasi?.nama_koperasi }}</div>
                                    <div class="text-[11px] text-gray-500">{{ item.koperasi?.kabupaten_kota }}</div>
                                </td>
                                <td class="max-w-xs p-2.5 font-medium">{{ item.deskripsi_temuan }}</td>
                                <td class="max-w-xs p-2.5 font-bold text-emerald-700">{{ item.rekomendasi }}</td>
                                <td class="p-2.5 text-center">
                                    <span
                                        class="inline-flex rounded-full border px-2.5 py-1 text-[10px] font-bold"
                                        :class="getRiskBadge(item.tingkat_risiko)"
                                    >
                                        {{ item.tingkat_risiko }}
                                    </span>
                                </td>
                                <td class="p-2.5 text-center font-semibold">{{ item.status_tindak_lanjut }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- OFFICIAL SIGNATURE BLOCK FOR PRINT -->
                <div class="print-signature grid grid-cols-2 gap-8 pt-12 text-center text-xs print:pt-6">
                    <div>
                        <div class="font-medium text-gray-600">Mengetahui,</div>
                        <div class="font-bold text-gray-900">Kepala Bidang Pengawasan Koperasi</div>
                        <div class="font-bold text-gray-900">Dinas Koperasi dan UKM Provsu</div>
                        <div class="h-20"></div>
                        <div class="font-bold text-gray-900 underline">Drs. Iskandar Muda, M.Si</div>
                        <div class="text-[11px] text-gray-600">NIP. 19780720 200312 1 002</div>
                    </div>

                    <div>
                        <div class="font-medium text-gray-600">Medan, {{ tanggalCetak }}</div>
                        <div class="font-bold text-gray-900">Petugas Administrasi Kelembagaan</div>
                        <div class="font-bold text-gray-900">SIMPADU KOP-UKM PROVSU</div>
                        <div class="h-20"></div>
                        <div class="font-bold text-gray-900 underline">Budi Santoso, S.Sos</div>
                        <div class="text-[11px] text-gray-600">NIP. 19850315 201001 1 008</div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
