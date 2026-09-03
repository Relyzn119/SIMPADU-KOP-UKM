<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { CheckCircle2, Clock, Eye, FileText, Plus, RotateCcw, Search, ShieldCheck, Trash2, Upload, User, X } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    pengawasans: {
        data: Array<any>;
        links: Array<any>;
        current_page: number;
        last_page: number;
        total: number;
        from: number;
        to: number;
    };
    filters: {
        search?: string;
        tahun?: string;
        predikat_kesehatan?: string;
        kabupaten_kota?: string;
        status_persetujuan_koperasi?: string;
    };
    kabupatenKotaList: string[];
}>();

const page = usePage();
const userRole = (page.props.auth as any)?.user?.role || 'admin_koperasi';

const search = ref(props.filters.search || '');
const tahun = ref(props.filters.tahun || '');
const predikatKesehatan = ref(props.filters.predikat_kesehatan || '');
const kabupatenKota = ref(props.filters.kabupaten_kota || '');
const statusPersetujuan = ref(props.filters.status_persetujuan_koperasi || '');

// Modal Persetujuan State (Role Admin Koperasi)
const isSetujuiModalOpen = ref(false);
const selectedPengawasan = ref<any | null>(null);

const setujuiForm = useForm({
    tanggapan_koperasi: '',
    file_bukti_tindak_lanjut: null as File | null,
});

const applyFilters = () => {
    router.get(
        '/pengawasan',
        {
            search: search.value || undefined,
            tahun: tahun.value || undefined,
            predikat_kesehatan: predikatKesehatan.value || undefined,
            kabupaten_kota: kabupatenKota.value || undefined,
            status_persetujuan_koperasi: statusPersetujuan.value || undefined,
        },
        { preserveState: true, replace: true },
    );
};

const resetFilters = () => {
    search.value = '';
    tahun.value = '';
    predikatKesehatan.value = '';
    kabupatenKota.value = '';
    statusPersetujuan.value = '';
    applyFilters();
};

const handleDelete = (item: any) => {
    if (confirm(`Apakah Anda yakin ingin menghapus data pemeriksaan "${item.no_surat_tugas}"?`)) {
        router.delete(`/pengawasan/${item.id}`);
    }
};

const openSetujuiModal = (item: any) => {
    selectedPengawasan.value = item;
    setujuiForm.tanggapan_koperasi = item.tanggapan_koperasi || '';
    setujuiForm.file_bukti_tindak_lanjut = null;
    isSetujuiModalOpen.value = true;
};

const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        setujuiForm.file_bukti_tindak_lanjut = target.files[0];
    }
};

const submitSetujui = () => {
    if (!selectedPengawasan.value) return;

    // Use router.post with _method: 'PUT' for multipart/form-data support in Inertia
    router.post(
        `/pengawasan/${selectedPengawasan.value.id}/setujui`,
        {
            _method: 'PUT',
            tanggapan_koperasi: setujuiForm.tanggapan_koperasi,
            file_bukti_tindak_lanjut: setujuiForm.file_bukti_tindak_lanjut,
        },
        {
            onSuccess: () => {
                isSetujuiModalOpen.value = false;
                setujuiForm.reset();
                selectedPengawasan.value = null;
            },
        },
    );
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
        case 'Pengawasan Khusus':
            return 'bg-rose-50 text-rose-700 border-rose-200';
        default:
            return 'bg-gray-100 text-gray-600 border-gray-200';
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Pemeriksaan & Penilaian Kesehatan Koperasi" />

        <div class="space-y-6">
            <!-- PAGE TITLE BANNER -->
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <h1 class="flex items-center gap-2.5 text-xl font-bold tracking-tight text-gray-900 sm:text-2xl">
                        <ShieldCheck class="h-6 w-6 text-indigo-600" />
                        Pemeriksaan & Penilaian Kesehatan Koperasi
                    </h1>
                    <p class="mt-1 text-xs text-gray-500">
                        Instrumen Pengawasan Lapangan 4 Aspek Kesehatan Koperasi oleh Tim Pengawas Diskop Provsu.
                    </p>
                </div>

                <!-- Create Link Shown ONLY for bidang_pengawasan -->
                <Link
                    v-if="userRole === 'bidang_pengawasan'"
                    href="/pengawasan/create"
                    class="shadow-xs flex items-center gap-2 self-start rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-slate-800 sm:self-auto"
                >
                    <Plus class="h-4 w-4 text-emerald-400" />
                    Input Pengawasan Baru
                </Link>
            </div>

            <!-- SEARCH & FILTER BAR CARD -->
            <div class="shadow-2xs space-y-4 rounded-3xl border border-gray-200/70 bg-white p-5">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-6">
                    <!-- Filter Tahun -->
                    <div>
                        <select
                            v-model="tahun"
                            @change="applyFilters"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-xs font-bold text-gray-900 transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                        >
                            <option value="">Semua Data Tahun</option>
                            <option value="2026">Data Tahun 2026</option>
                            <option value="2025">Data Tahun 2025</option>
                            <option value="2024">Data Tahun 2024</option>
                            <option value="2023">Data Tahun 2023</option>
                        </select>
                    </div>

                    <!-- Search Input -->
                    <div class="relative lg:col-span-2">
                        <Search class="absolute left-3.5 top-3 h-4 w-4 text-gray-400" />
                        <input
                            v-model="search"
                            @input="applyFilters"
                            type="text"
                            placeholder="Cari No. Surat Tugas / Nama Koperasi..."
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-9 pr-4 text-xs text-gray-900 transition placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                        />
                    </div>

                    <!-- Filter Predikat -->
                    <div>
                        <select
                            v-model="predikatKesehatan"
                            @change="applyFilters"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-xs text-gray-900 transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                        >
                            <option value="">Semua Predikat Kesehatan</option>
                            <option value="Sehat">Sehat (≥ 80.00)</option>
                            <option value="Cukup Sehat">Cukup Sehat (66.00 - 79.99)</option>
                            <option value="Dalam Pengawasan">Dalam Pengawasan (51.00 - 65.99)</option>
                            <option value="Pengawasan Khusus">Pengawasan Khusus (&lt; 51.00)</option>
                        </select>
                    </div>

                    <!-- Filter Kab/Kota -->
                    <div>
                        <select
                            v-model="kabupatenKota"
                            @change="applyFilters"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-xs text-gray-900 transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                        >
                            <option value="">Semua Kab/Kota</option>
                            <option v-for="kab in kabupatenKotaList" :key="kab" :value="kab">{{ kab }}</option>
                        </select>
                    </div>

                    <!-- Filter Status Persetujuan -->
                    <div>
                        <select
                            v-model="statusPersetujuan"
                            @change="applyFilters"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-xs text-gray-900 transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                        >
                            <option value="">Semua Status Persetujuan</option>
                            <option value="pending">Sedang Diproses (Pending)</option>
                            <option value="approved">Disetujui Pengurus (Sah)</option>
                        </select>
                    </div>
                </div>

                <!-- Reset Filters -->
                <div class="flex items-center justify-between border-t border-gray-100 pt-3 text-xs">
                    <span class="font-medium text-gray-500"
                        >Menampilkan <span class="font-bold text-gray-900">{{ pengawasans.from || 0 }} - {{ pengawasans.to || 0 }}</span> dari
                        <span class="font-bold text-gray-900">{{ pengawasans.total }}</span> Laporan Pemeriksaan</span
                    >
                    <button
                        @click="resetFilters"
                        class="flex items-center gap-1.5 text-xs font-semibold text-gray-500 transition hover:text-emerald-600"
                    >
                        <RotateCcw class="h-3.5 w-3.5" />
                        Reset Filter
                    </button>
                </div>
            </div>

            <!-- TABLE CONTAINER CARD -->
            <div class="shadow-2xs overflow-hidden rounded-3xl border border-gray-200/70 bg-white">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="border-b border-gray-100 bg-gray-50/80 text-[10px] font-bold uppercase tracking-wider text-gray-500">
                            <tr>
                                <th class="px-4 py-3.5 font-bold">Koperasi & Surat Tugas</th>
                                <th class="px-4 py-3.5 font-bold">Dibuat Oleh (Pengawas)</th>
                                <th class="px-4 py-3.5 text-center font-bold">Skor 4 Aspek</th>
                                <th class="px-4 py-3.5 text-center font-bold">Persetujuan Pengurus</th>
                                <th class="px-4 py-3.5 text-center font-bold">Predikat Kesehatan</th>
                                <th class="px-4 py-3.5 text-center font-bold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="item in pengawasans.data" :key="item.id" class="group transition hover:bg-gray-50/60">
                                <td class="px-4 py-3.5">
                                    <div class="font-bold text-gray-900 transition group-hover:text-indigo-600">
                                        {{ item.koperasi?.nama_koperasi }}
                                    </div>
                                    <div class="mt-0.5 flex items-center gap-2 text-[11px] font-medium text-gray-500">
                                        <span class="font-mono font-semibold text-indigo-600">{{ item.no_surat_tugas }}</span>
                                        <span>•</span>
                                        <span>{{ item.koperasi?.kabupaten_kota }}</span>
                                    </div>
                                    <div v-if="item.file_berita_acara_path" class="mt-1.5">
                                        <a
                                            :href="`/file-download?path=${encodeURIComponent(item.file_berita_acara_path)}`"
                                            target="_blank"
                                            class="inline-flex items-center gap-1 rounded-md border border-indigo-200 bg-indigo-50 px-2 py-0.5 text-[10px] font-bold text-indigo-700 transition hover:bg-indigo-100"
                                            title="Lihat Berkas Berita Acara Pemeriksaan (PDF)"
                                        >
                                            <FileText class="h-3 w-3 text-indigo-600" />
                                            Berita Acara (PDF)
                                        </a>
                                    </div>
                                </td>

                                <!-- Dibuat Oleh (Tim Pengawas) -->
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-1.5 font-bold text-gray-900">
                                        <User class="h-3.5 w-3.5 text-indigo-600" />
                                        {{ item.created_by?.name || item.nama_tim_pengawas }}
                                    </div>
                                    <div class="flex items-center gap-1 text-[10px] text-gray-400">
                                        <Clock class="h-3 w-3" />
                                        <span>Tgl Buat: {{ formatDate(item.created_at || item.tanggal_pemeriksaan) }}</span>
                                    </div>
                                </td>

                                <td class="px-4 py-3.5 text-center">
                                    <div class="space-x-1.5 font-mono text-[10px] text-gray-500">
                                        <span
                                            >TK: <strong class="text-gray-900">{{ item.skor_tata_kelola }}</strong></span
                                        >
                                        <span
                                            >PR: <strong class="text-gray-900">{{ item.skor_profil_risiko }}</strong></span
                                        >
                                        <span
                                            >KK: <strong class="text-gray-900">{{ item.skor_kinerja_keuangan }}</strong></span
                                        >
                                        <span
                                            >PM: <strong class="text-gray-900">{{ item.skor_permodalan }}</strong></span
                                        >
                                    </div>
                                </td>

                                <!-- Status Persetujuan Koperasi -->
                                <td class="px-4 py-3.5 text-center">
                                    <div v-if="item.status_persetujuan_koperasi === 'approved'" class="inline-flex flex-col items-center">
                                        <span class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[10px] font-bold text-emerald-700">
                                            <CheckCircle2 class="h-3 w-3 text-emerald-600" />
                                            Disetujui Pengurus
                                        </span>
                                        <span class="mt-0.5 text-[9px] text-gray-400">Tgl: {{ formatDate(item.approved_at) }}</span>
                                    </div>
                                    <div v-else class="inline-flex flex-col items-center">
                                        <span class="inline-flex items-center gap-1 rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-[10px] font-bold text-amber-700">
                                            <Clock class="h-3 w-3 text-amber-600" />
                                            Sedang Diproses
                                        </span>
                                        <span class="mt-0.5 text-[9px] text-amber-600">Pending Persetujuan</span>
                                    </div>

                                    <!-- File Bukti Digital (Visible for BOTH admin and pengawas) -->
                                    <div v-if="item.file_bukti_tindak_lanjut_path" class="mt-1.5">
                                        <a
                                            :href="`/file-download?path=${encodeURIComponent(item.file_bukti_tindak_lanjut_path)}`"
                                            target="_blank"
                                            class="inline-flex items-center gap-1 rounded-md border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 transition hover:bg-emerald-100"
                                            title="Lihat Dokumen Bukti Digital Tindak Lanjut"
                                        >
                                            <FileText class="h-3 w-3 text-emerald-600" />
                                            Bukti Digital
                                        </a>
                                    </div>
                                </td>

                                <td class="px-4 py-3.5 text-center">
                                    <div class="inline-flex flex-col items-center">
                                        <span
                                            class="inline-flex rounded-full border px-2.5 py-1 text-[10px] font-bold"
                                            :class="getPredikatBadge(item.predikat_kesehatan)"
                                        >
                                            {{ item.predikat_kesehatan }}
                                        </span>
                                        <span class="mt-0.5 font-mono text-[10px] text-gray-400">Skor Total: {{ item.skor_total }}</span>
                                    </div>
                                </td>

                                <td class="px-4 py-3.5 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <Link
                                            :href="`/pengawasan/${item.id}`"
                                            class="rounded-lg p-1.5 text-gray-400 transition hover:bg-indigo-50 hover:text-indigo-600"
                                            title="Lihat Detail Berita Acara"
                                        >
                                            <Eye class="h-4 w-4" />
                                        </Link>

                                        <!-- Admin Action: Setujui LHP -->
                                        <button
                                            v-if="userRole === 'admin_koperasi'"
                                            @click="openSetujuiModal(item)"
                                            class="flex items-center gap-1 rounded-xl border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-700 transition hover:bg-emerald-100"
                                            title="Tanggapi & Setujui LHP"
                                        >
                                            <CheckCircle2 class="h-3.5 w-3.5 text-emerald-600" />
                                            {{ item.status_persetujuan_koperasi === 'approved' ? 'Edit Tanggapan' : 'Setujui LHP' }}
                                        </button>

                                        <!-- Pengawas Action: Delete -->
                                        <button
                                            v-if="userRole === 'bidang_pengawasan'"
                                            @click="handleDelete(item)"
                                            class="rounded-lg p-1.5 text-gray-400 transition hover:bg-rose-50 hover:text-rose-600"
                                            title="Hapus LHP Pengawasan"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Footer -->
                <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50/50 px-6 py-4">
                    <div class="text-xs font-medium text-gray-500">
                        Halaman <span class="font-bold text-gray-900">{{ pengawasans.current_page }}</span> dari
                        <span class="font-bold text-gray-900">{{ pengawasans.last_page }}</span>
                    </div>

                    <div class="flex items-center gap-1">
                        <component
                            v-for="(link, index) in pengawasans.links"
                            :key="index"
                            :is="link.url ? Link : 'span'"
                            :href="link.url"
                            class="rounded-xl px-3 py-1.5 text-xs font-semibold transition"
                            :class="[
                                link.active ? 'shadow-2xs bg-slate-900 text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900',
                                !link.url && 'cursor-not-allowed opacity-40',
                            ]"
                        >
                            <span v-html="link.label" />
                        </component>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL SETUJUI LHP & UPLOAD BUKTI DIGITAL (ROLE ADMIN KOPERASI) -->
        <div
            v-if="isSetujuiModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/60 p-4 backdrop-blur-sm"
        >
            <div class="relative my-8 w-full max-w-lg space-y-4 rounded-3xl border border-gray-200/80 bg-white p-6 shadow-2xl">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="flex items-center gap-2 text-sm font-bold text-gray-900">
                        <CheckCircle2 class="h-5 w-5 text-emerald-600" />
                        Persetujuan & Tanggapan LHP Pengurus Koperasi
                    </h3>
                    <button
                        @click="isSetujuiModalOpen = false"
                        class="rounded-xl p-1 text-gray-400 transition hover:bg-gray-100 hover:text-gray-900"
                    >
                        <X class="h-5 w-5" />
                    </button>
                </div>

                <div class="space-y-1.5 rounded-2xl border border-emerald-200 bg-emerald-50/70 p-3.5 text-xs text-emerald-900">
                    <div><strong>No. Surat Tugas:</strong> {{ selectedPengawasan?.no_surat_tugas }}</div>
                    <div><strong>Objek Koperasi:</strong> {{ selectedPengawasan?.koperasi?.nama_koperasi }}</div>
                    <div><strong>Predikat Hasil Audit:</strong> {{ selectedPengawasan?.predikat_kesehatan }} (Skor: {{ selectedPengawasan?.skor_total }})</div>
                </div>

                <form @submit.prevent="submitSetujui" class="space-y-4">
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700"
                            >Tanggapan & Komitmen Pengurus Koperasi *</label
                        >
                        <textarea
                            v-model="setujuiForm.tanggapan_koperasi"
                            rows="4"
                            required
                            placeholder="Tuliskan tanggapan resmi dari Pengurus Koperasi atas LHP hasil pemeriksaan ini..."
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                        ></textarea>
                    </div>

                    <!-- File Upload Bukti Digital (Opsi D) -->
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">
                            Upload File Bukti Digital / Lampiran (PDF/DOCX/JPG)
                        </label>
                        <div v-if="selectedPengawasan?.file_bukti_tindak_lanjut_path" class="mb-2 flex items-center gap-2 rounded-xl bg-gray-50 border border-gray-200 p-2.5 text-xs">
                            <FileText class="h-4 w-4 text-emerald-600" />
                            <span class="text-gray-600">File Saat Ini:</span>
                            <a
                                :href="`/file-download?path=${encodeURIComponent(selectedPengawasan.file_bukti_tindak_lanjut_path)}`"
                                target="_blank"
                                class="font-bold text-emerald-600 hover:underline"
                            >
                                Lihat Dokumen Bukti Digital
                            </a>
                        </div>
                        <input
                            type="file"
                            @change="handleFileChange"
                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-700 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-900 file:px-3 file:py-1 text-xs file:font-bold file:text-white hover:file:bg-slate-800"
                        />
                        <p class="mt-1 text-[10px] text-gray-400">Maksimal file 10 MB. Melampirkan bukti fisik perbaikan memperkuat Indeks Transparansi Koperasi.</p>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-2">
                        <button
                            type="button"
                            @click="isSetujuiModalOpen = false"
                            class="rounded-xl px-4 py-2 text-xs font-bold text-gray-500 transition hover:bg-gray-100"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="setujuiForm.processing"
                            class="flex items-center gap-1.5 rounded-xl bg-emerald-600 px-5 py-2 text-xs font-bold text-white shadow-md transition hover:bg-emerald-700"
                        >
                            <CheckCircle2 class="h-4 w-4 text-emerald-200" />
                            Setujui LHP & Kirim Tanggapan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
