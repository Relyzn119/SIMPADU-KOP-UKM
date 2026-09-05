<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Building2, CheckCircle2, Edit3, Eye, Plus, RotateCcw, Search, Trash2, XCircle } from 'lucide-vue-next';
import { ref } from 'vue';
import FormModal from './FormModal.vue';

const props = defineProps<{
    koperasis: {
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
        kabupaten_kota?: string;
        jenis_koperasi?: string;
        status_keaktifan?: string;
        predikat_kesehatan?: string;
        status_verifikasi?: string;
    };
    kabupatenKotaList: string[];
}>();

const page = usePage();
const userRole = (page.props.auth as any)?.user?.role || 'admin_koperasi';

const search = ref(props.filters.search || '');
const tahun = ref(props.filters.tahun || '');
const kabupatenKota = ref(props.filters.kabupaten_kota || '');
const jenisKoperasi = ref(props.filters.jenis_koperasi || '');
const statusKeaktifan = ref(props.filters.status_keaktifan || '');
const predikatKesehatan = ref(props.filters.predikat_kesehatan || '');
const statusVerifikasi = ref(props.filters.status_verifikasi || '');

const isModalOpen = ref(false);
const koperasiToEdit = ref<any | null>(null);

// Modal Penolakan State
const isRejectModalOpen = ref(false);
const selectedKoperasiForReject = ref<any | null>(null);
const alasanPenolakanInput = ref('');

const applyFilters = () => {
    router.get(
        '/koperasi',
        {
            search: search.value || undefined,
            tahun: tahun.value || undefined,
            kabupaten_kota: kabupatenKota.value || undefined,
            jenis_koperasi: jenisKoperasi.value || undefined,
            status_keaktifan: statusKeaktifan.value || undefined,
            predikat_kesehatan: predikatKesehatan.value || undefined,
            status_verifikasi: statusVerifikasi.value || undefined,
        },
        { preserveState: true, replace: true },
    );
};

const resetFilters = () => {
    search.value = '';
    tahun.value = '';
    kabupatenKota.value = '';
    jenisKoperasi.value = '';
    statusKeaktifan.value = '';
    predikatKesehatan.value = '';
    statusVerifikasi.value = '';
    applyFilters();
};

const openCreateModal = () => {
    koperasiToEdit.value = null;
    isModalOpen.value = true;
};

const openEditModal = (item: any) => {
    koperasiToEdit.value = item;
    isModalOpen.value = true;
};

const handleDelete = (item: any) => {
    if (confirm(`Apakah Anda yakin ingin menghapus data koperasi "${item.nama_koperasi}"?`)) {
        router.delete(`/koperasi/${item.id}`);
    }
};

const handleVerifikasi = (item: any) => {
    if (confirm(`Apakah Anda yakin ingin memverifikasi (Mengesahkan) data Koperasi "${item.nama_koperasi}"?`)) {
        router.put(`/koperasi/${item.id}/verifikasi`);
    }
};

const openRejectModal = (item: any) => {
    selectedKoperasiForReject.value = item;
    alasanPenolakanInput.value = item.alasan_penolakan || '';
    isRejectModalOpen.value = true;
};

const submitReject = () => {
    if (!alasanPenolakanInput.value.trim()) {
        alert('Mohon isi alasan penolakan.');
        return;
    }

    router.put(
        `/koperasi/${selectedKoperasiForReject.value.id}/tolak`,
        {
            alasan_penolakan: alasanPenolakanInput.value,
        },
        {
            onSuccess: () => {
                isRejectModalOpen.value = false;
                selectedKoperasiForReject.value = null;
                alasanPenolakanInput.value = '';
            },
        },
    );
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

const formatRupiah = (val: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
};

const formatDate = (dateStr: string) => {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Master Data Koperasi Binaan" />

        <div class="space-y-6">
            <!-- PAGE TITLE & TOP ACTION -->
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <h1 class="flex items-center gap-2.5 text-xl font-bold tracking-tight text-gray-900 sm:text-2xl">
                        <Building2 class="h-6 w-6 text-emerald-600" />
                        Data Master Koperasi Binaan
                    </h1>
                    <p class="mt-1 text-xs text-gray-500">
                        <template v-if="userRole === 'bidang_pengawasan'">
                            Modul Pengawasan: Verifikasi keabsahan data kelembagaan Koperasi se-Sumatera Utara.
                        </template>
                        <template v-else>
                            Kelola data kelembagaan, pengurus, serta ringkasan kesehatan Koperasi se-Sumatera Utara.
                        </template>
                    </p>
                </div>

                <!-- Create button shown ONLY for admin_koperasi -->
                <button
                    v-if="userRole === 'admin_koperasi'"
                    @click="openCreateModal"
                    class="shadow-xs flex items-center gap-2 self-start rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-slate-800 sm:self-auto"
                >
                    <Plus class="h-4 w-4 text-emerald-400" />
                    Tambah Koperasi Baru
                </button>
            </div>

            <!-- SEARCH & FILTER BAR CARD -->
            <div class="shadow-2xs space-y-4 rounded-3xl border border-gray-200/70 bg-white p-5">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-7">
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
                            placeholder="Cari Nama Koperasi / No. Badan Hukum..."
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-9 pr-4 text-xs text-gray-900 transition placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                        />
                    </div>

                    <!-- Filter Kabupaten/Kota -->
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

                    <!-- Filter Jenis -->
                    <div>
                        <select
                            v-model="jenisKoperasi"
                            @change="applyFilters"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-xs text-gray-900 transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                        >
                            <option value="">Semua Jenis</option>
                            <option value="KSP">KSP (Simpan Pinjam)</option>
                            <option value="KSU">KSU (Serba Usaha)</option>
                            <option value="Produsen">Produsen</option>
                            <option value="Pemasaran">Pemasaran</option>
                            <option value="Konsumen">Konsumen</option>
                            <option value="Jasa">Jasa</option>
                        </select>
                    </div>

                    <!-- Filter Predikat Kesehatan -->
                    <div>
                        <select
                            v-model="predikatKesehatan"
                            @change="applyFilters"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-xs text-gray-900 transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                        >
                            <option value="">Semua Predikat</option>
                            <option value="Sehat">Sehat</option>
                            <option value="Cukup Sehat">Cukup Sehat</option>
                            <option value="Dalam Pengawasan">Dalam Pengawasan</option>
                            <option value="Pengawasan Khusus">Pengawasan Khusus</option>
                        </select>
                    </div>

                    <!-- Filter Status Verifikasi -->
                    <div>
                        <select
                            v-model="statusVerifikasi"
                            @change="applyFilters"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-xs text-gray-900 transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                        >
                            <option value="">Semua Status Verifikasi</option>
                            <option value="pending">Draft / Pending</option>
                            <option value="verified">Verified (Sah)</option>
                            <option value="rejected">Ditolak</option>
                        </select>
                    </div>
                </div>

                <!-- Reset Filters Button -->
                <div class="flex items-center justify-between border-t border-gray-100 pt-3 text-xs">
                    <span class="font-medium text-gray-500"
                        >Menampilkan <span class="font-bold text-gray-900">{{ koperasis.from || 0 }} - {{ koperasis.to || 0 }}</span> dari
                        <span class="font-bold text-gray-900">{{ koperasis.total }}</span> Koperasi</span
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
                                <th class="px-4 py-3.5 font-bold">Koperasi & No. BH</th>
                                <th class="px-4 py-3.5 font-bold">Jenis & Wilayah</th>
                                <th class="px-4 py-3.5 text-right font-bold">Total Aset</th>
                                <th class="px-4 py-3.5 text-center font-bold">Status Keabsahan</th>
                                <th class="px-4 py-3.5 text-center font-bold">Predikat Kesehatan</th>
                                <th class="px-4 py-3.5 text-center font-bold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="item in koperasis.data" :key="item.id" class="group transition hover:bg-gray-50/60">
                                <!-- Name & Avatar -->
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-900 text-xs font-bold text-white"
                                        >
                                            {{ item.nama_koperasi.charAt(0) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 transition group-hover:text-emerald-600">
                                                {{ item.nama_koperasi }}
                                            </div>
                                            <div class="mt-0.5 font-mono text-[10px] text-gray-400">
                                                {{ item.no_badan_hukum }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Jenis & Kab/Kota -->
                                <td class="px-4 py-3.5">
                                    <div class="font-semibold text-gray-800">{{ item.jenis_koperasi }}</div>
                                    <div class="text-[11px] font-medium text-gray-500">{{ item.kabupaten_kota }}</div>
                                </td>

                                <!-- Aset -->
                                <td class="px-4 py-3.5 text-right font-mono font-bold text-gray-900">
                                    {{ formatRupiah(item.aset) }}
                                </td>

                                <!-- Status Verifikasi / Keabsahan -->
                                <td class="px-4 py-3.5 text-center">
                                    <div v-if="item.status_verifikasi === 'verified'" class="inline-flex flex-col items-center">
                                        <span class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[10px] font-bold text-emerald-700">
                                            <CheckCircle2 class="h-3 w-3 text-emerald-600" />
                                            Dokumen Sah
                                        </span>
                                        <span class="mt-0.5 text-[9px] text-gray-400">Oleh: {{ item.verified_by?.name || 'Pengawas' }}</span>
                                    </div>
                                    <div v-else-if="item.status_verifikasi === 'rejected'" class="inline-flex flex-col items-center">
                                        <span class="inline-flex items-center gap-1 rounded-full border border-rose-200 bg-rose-50 px-2.5 py-1 text-[10px] font-bold text-rose-700" :title="item.alasan_penolakan">
                                            <XCircle class="h-3 w-3 text-rose-600" />
                                            Ditolak
                                        </span>
                                        <span class="mt-0.5 text-[9px] text-rose-500 truncate max-w-[120px]" :title="item.alasan_penolakan">
                                            {{ item.alasan_penolakan }}
                                        </span>
                                    </div>
                                    <div v-else class="inline-flex flex-col items-center">
                                        <span class="inline-flex rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-[10px] font-bold text-amber-700">
                                            Draft / Belum Verifikasi
                                        </span>
                                    </div>
                                </td>

                                <!-- Predikat Kesehatan -->
                                <td class="px-4 py-3.5 text-center">
                                    <div v-if="item.predikat_kesehatan" class="inline-flex flex-col items-center">
                                        <span
                                            class="inline-flex rounded-full border px-2.5 py-1 text-[10px] font-bold"
                                            :class="getPredikatBadge(item.predikat_kesehatan)"
                                        >
                                            {{ item.predikat_kesehatan }}
                                        </span>
                                        <span class="mt-0.5 font-mono text-[10px] text-gray-400">Skor: {{ item.skor_kesehatan_terakhir }}</span>
                                    </div>
                                    <span v-else class="text-[11px] italic text-gray-400">Belum Diperiksa</span>
                                </td>

                                <!-- Action Buttons -->
                                <td class="px-4 py-3.5 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <!-- Preview Link (All Roles) -->
                                        <Link
                                            :href="`/koperasi/${item.id}`"
                                            title="Lihat Detail Profile"
                                            class="rounded-lg p-1.5 text-gray-400 transition hover:bg-emerald-50 hover:text-emerald-600"
                                        >
                                            <Eye class="h-4 w-4" />
                                        </Link>

                                        <!-- Admin Actions: Edit & Delete -->
                                        <template v-if="userRole === 'admin_koperasi'">
                                            <button
                                                @click="openEditModal(item)"
                                                title="Edit Data"
                                                class="rounded-lg p-1.5 text-gray-400 transition hover:bg-cyan-50 hover:text-cyan-600"
                                            >
                                                <Edit3 class="h-4 w-4" />
                                            </button>

                                            <button
                                                @click="handleDelete(item)"
                                                title="Hapus Data"
                                                class="rounded-lg p-1.5 text-gray-400 transition hover:bg-rose-50 hover:text-rose-600"
                                            >
                                                <Trash2 class="h-4 w-4" />
                                            </button>
                                        </template>


                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Footer -->
                <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50/50 px-6 py-4">
                    <div class="text-xs font-medium text-gray-500">
                        Halaman <span class="font-bold text-gray-900">{{ koperasis.current_page }}</span> dari
                        <span class="font-bold text-gray-900">{{ koperasis.last_page }}</span>
                    </div>

                    <div class="flex items-center gap-1">
                        <component
                            v-for="(link, index) in koperasis.links"
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

        <!-- Create & Edit Form Modal (Admin Only) -->
        <FormModal :is-open="isModalOpen" :koperasi-to-edit="koperasiToEdit" :kabupaten-kota-list="kabupatenKotaList" @close="isModalOpen = false" />

        <!-- Modal Tolak Dokumen (Pengawas Only) -->
        <div v-if="isRejectModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-xs">
            <div class="w-full max-w-md space-y-4 rounded-3xl bg-white p-6 shadow-xl">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="flex items-center gap-2 text-sm font-bold text-rose-700">
                        <XCircle class="h-5 w-5 text-rose-600" />
                        Tolak Dokumen Koperasi
                    </h3>
                    <button @click="isRejectModalOpen = false" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>

                <div class="space-y-3">
                    <p class="text-xs text-gray-600">
                        Anda akan menolak keabsahan data untuk Koperasi
                        <strong class="text-gray-900">{{ selectedKoperasiForReject?.nama_koperasi }}</strong>.
                        Mohon berikan alasan penolakan:
                    </p>

                    <div>
                        <label class="mb-1 block text-xs font-bold text-gray-700">Alasan Penolakan <span class="text-rose-500">*</span></label>
                        <textarea
                            v-model="alasanPenolakanInput"
                            rows="4"
                            placeholder="Contoh: Dokumen NIK/Badan Hukum tidak sesuai dengan database Kementerian Koperasi..."
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 p-3 text-xs text-gray-900 transition focus:border-rose-500 focus:bg-white focus:ring-2 focus:ring-rose-500/20"
                        ></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 border-t border-gray-100 pt-3">
                    <button
                        @click="isRejectModalOpen = false"
                        class="rounded-xl px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100"
                    >
                        Batal
                    </button>
                    <button
                        @click="submitReject"
                        class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white shadow-xs transition hover:bg-rose-700"
                    >
                        Simpan Penolakan
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
