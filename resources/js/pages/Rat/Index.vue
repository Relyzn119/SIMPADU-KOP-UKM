<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { CheckCircle2, Clock, Edit3, FileSpreadsheet, FileText, Plus, Search, Trash2, X, XCircle } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    rats: {
        data: Array<any>;
        links: Array<any>;
        current_page: number;
        last_page: number;
        total: number;
        from: number;
        to: number;
    };
    koperasis: Array<{
        id: number;
        nama_koperasi: string;
        no_badan_hukum: string;
        kabupaten_kota: string;
    }>;
    summary: {
        total_koperasi: number;
        rat_tepat_waktu: number;
        rat_terlambat: number;
        belum_rat: number;
        kepatuhan_pct: number;
    };
    filters: {
        tahun_buku: number;
        search?: string;
        status_rat?: string;
        kabupaten_kota?: string;
        status_verifikasi?: string;
    };
    kabupatenKotaList: string[];
}>();

const page = usePage();
const userRole = (page.props.auth as any)?.user?.role || 'admin_koperasi';

const tahunBuku = ref(props.filters.tahun_buku || 2024);
const search = ref(props.filters.search || '');
const statusRat = ref(props.filters.status_rat || '');
const kabupatenKota = ref(props.filters.kabupaten_kota || '');
const statusVerifikasi = ref(props.filters.status_verifikasi || '');

const isModalOpen = ref(false);
const ratToEdit = ref<any | null>(null);

// Modal Penolakan State
const isRejectModalOpen = ref(false);
const selectedRatForReject = ref<any | null>(null);
const alasanPenolakanInput = ref('');

const form = useForm({
    koperasi_id: props.koperasis[0]?.id || '',
    tahun_buku: props.filters.tahun_buku || 2024,
    tanggal_rat: `${props.filters.tahun_buku + 1}-05-15`,
    tempat_pelaksanaan: 'Aula Koperasi',
    jumlah_anggota_hadir: 50,
    kuorum: true,
    catatan: '',
    file_lpj: null as File | null,
});

const applyFilters = () => {
    router.get(
        '/rat',
        {
            tahun_buku: tahunBuku.value,
            search: search.value || undefined,
            status_rat: statusRat.value || undefined,
            kabupaten_kota: kabupatenKota.value || undefined,
            status_verifikasi: statusVerifikasi.value || undefined,
        },
        { preserveState: true, replace: true },
    );
};

const openCreateModal = () => {
    ratToEdit.value = null;
    form.reset();
    form.tahun_buku = tahunBuku.value;
    form.tanggal_rat = `${tahunBuku.value + 1}-05-15`;
    isModalOpen.value = true;
};

const openEditModal = (item: any) => {
    ratToEdit.value = item;
    form.koperasi_id = item.koperasi_id;
    form.tahun_buku = item.tahun_buku;
    form.tanggal_rat = item.tanggal_rat;
    form.tempat_pelaksanaan = item.tempat_pelaksanaan;
    form.jumlah_anggota_hadir = item.jumlah_anggota_hadir;
    form.kuorum = Boolean(item.kuorum);
    form.catatan = item.catatan || '';
    form.file_lpj = null;
    isModalOpen.value = true;
};

const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.file_lpj = target.files[0];
    }
};

const handleSubmit = () => {
    if (ratToEdit.value) {
        form.post(`/rat/${ratToEdit.value.id}`, {
            headers: { 'X-HTTP-Method-Override': 'PUT' },
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    } else {
        form.post('/rat', {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    }
};

const handleDelete = (item: any) => {
    if (confirm(`Apakah Anda yakin ingin menghapus laporan RAT Koperasi "${item.koperasi?.nama_koperasi}"?`)) {
        router.delete(`/rat/${item.id}`);
    }
};

const handleVerifikasi = (item: any) => {
    if (confirm(`Apakah Anda yakin ingin memverifikasi (Mengesahkan) Laporan RAT Koperasi "${item.koperasi?.nama_koperasi}"?`)) {
        router.put(`/rat/${item.id}/verifikasi`);
    }
};

const openRejectModal = (item: any) => {
    selectedRatForReject.value = item;
    alasanPenolakanInput.value = item.alasan_penolakan || '';
    isRejectModalOpen.value = true;
};

const submitReject = () => {
    if (!alasanPenolakanInput.value.trim()) {
        alert('Mohon isi alasan penolakan.');
        return;
    }

    router.put(
        `/rat/${selectedRatForReject.value.id}/tolak`,
        {
            alasan_penolakan: alasanPenolakanInput.value,
        },
        {
            onSuccess: () => {
                isRejectModalOpen.value = false;
                selectedRatForReject.value = null;
                alasanPenolakanInput.value = '';
            },
        },
    );
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Monitoring & Pelaporan RAT Koperasi" />

        <div class="space-y-6">
            <!-- DEADLINE BANNER CARD (UU NO. 25/1992) -->
            <div class="shadow-2xs rounded-3xl border border-gray-200/70 bg-white p-6">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div>
                        <div
                            class="mb-2 inline-flex items-center gap-2 rounded-full border border-teal-200 bg-teal-50 px-3 py-1 text-xs font-bold text-teal-700"
                        >
                            <Clock class="h-3.5 w-3.5" />
                            Amanat Undang-Undang No. 25 Tahun 1992
                        </div>
                        <h1 class="text-xl font-bold tracking-tight text-gray-900 sm:text-2xl">
                            Monitoring & Pelaporan RAT (Tahun Buku {{ tahunBuku }})
                        </h1>
                        <p class="mt-1 max-w-2xl text-xs text-gray-500">
                            Pelaksanaan RAT paling lambat diselenggarakan 6 (enam) bulan setelah tutup tahun buku (Batas Waktu:
                            <span class="font-bold text-teal-700">30 Juni {{ tahunBuku + 1 }}</span
                            >).
                        </p>
                    </div>

                    <!-- Show create button ONLY for admin_koperasi -->
                    <button
                        v-if="userRole === 'admin_koperasi'"
                        @click="openCreateModal"
                        class="shadow-xs flex items-center gap-2 self-start rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-slate-800 sm:self-auto"
                    >
                        <Plus class="h-4 w-4 text-emerald-400" />
                        Input RAT Koperasi
                    </button>
                </div>
            </div>

            <!-- KPI SUMMARY COUNTER CARDS -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="shadow-2xs rounded-3xl border border-gray-200/70 bg-white p-5">
                    <div class="text-xs font-bold uppercase tracking-wider text-gray-400">Kepatuhan RAT</div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="font-mono text-3xl font-extrabold text-gray-900">{{ summary.kepatuhan_pct }}%</span>
                        <span class="text-xs font-bold text-teal-700">Target Diskop</span>
                    </div>
                    <div class="mt-3 h-1.5 w-full overflow-hidden rounded-full bg-gray-100">
                        <div class="h-full rounded-full bg-teal-600" :style="{ width: `${summary.kepatuhan_pct}%` }"></div>
                    </div>
                </div>

                <div class="shadow-2xs rounded-3xl border border-gray-200/70 bg-white p-5">
                    <div class="text-xs font-bold uppercase tracking-wider text-emerald-700">RAT Tepat Waktu</div>
                    <div class="mt-2 font-mono text-3xl font-extrabold text-gray-900">{{ summary.rat_tepat_waktu }}</div>
                    <p class="mt-1 text-[11px] font-medium text-gray-400">Sebelum 30 Juni {{ tahunBuku + 1 }}</p>
                </div>

                <div class="shadow-2xs rounded-3xl border border-gray-200/70 bg-white p-5">
                    <div class="text-xs font-bold uppercase tracking-wider text-amber-700">RAT Terlambat</div>
                    <div class="mt-2 font-mono text-3xl font-extrabold text-gray-900">{{ summary.rat_terlambat }}</div>
                    <p class="mt-1 text-[11px] font-medium text-gray-400">Setelah 30 Juni {{ tahunBuku + 1 }}</p>
                </div>

                <div class="shadow-2xs rounded-3xl border border-gray-200/70 bg-white p-5">
                    <div class="text-xs font-bold uppercase tracking-wider text-rose-700">Belum Melaporkan</div>
                    <div class="mt-2 font-mono text-3xl font-extrabold text-gray-900">{{ summary.belum_rat }}</div>
                    <p class="mt-1 text-[11px] font-medium text-gray-400">Perlu Surat Teguran</p>
                </div>
            </div>

            <!-- SEARCH & FILTER BAR CARD -->
            <div class="shadow-2xs space-y-4 rounded-3xl border border-gray-200/70 bg-white p-5">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-6">
                    <!-- Select Tahun Buku -->
                    <div>
                        <select
                            v-model="tahunBuku"
                            @change="applyFilters"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-xs font-bold text-gray-900 transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                        >
                            <option value="semua">Semua Data Tahun</option>
                            <option :value="2026">Data Tahun 2026</option>
                            <option :value="2025">Data Tahun 2025</option>
                            <option :value="2024">Data Tahun 2024</option>
                            <option :value="2023">Data Tahun 2023</option>
                        </select>
                    </div>

                    <!-- Search Input -->
                    <div class="relative lg:col-span-2">
                        <Search class="absolute left-3.5 top-3 h-4 w-4 text-gray-400" />
                        <input
                            v-model="search"
                            @input="applyFilters"
                            type="text"
                            placeholder="Cari Koperasi / No. BH..."
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-9 pr-4 text-xs text-gray-900 transition placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                        />
                    </div>

                    <!-- Status RAT -->
                    <div>
                        <select
                            v-model="statusRat"
                            @change="applyFilters"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-xs text-gray-900 transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                        >
                            <option value="">Semua Status RAT</option>
                            <option value="Sudah RAT Tepat Waktu">RAT Tepat Waktu</option>
                            <option value="Sudah RAT Terlambat">RAT Terlambat</option>
                            <option value="Belum RAT">Belum RAT</option>
                        </select>
                    </div>

                    <!-- Kab/Kota -->
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

                    <!-- Status Verifikasi -->
                    <div>
                        <select
                            v-model="statusVerifikasi"
                            @change="applyFilters"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-xs text-gray-900 transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                        >
                            <option value="">Semua Verifikasi</option>
                            <option value="pending">Draft / Pending</option>
                            <option value="verified">Verified (Sah)</option>
                            <option value="rejected">Ditolak</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- TABLE CONTAINER CARD -->
            <div class="shadow-2xs overflow-hidden rounded-3xl border border-gray-200/70 bg-white">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="border-b border-gray-100 bg-gray-50/80 text-[10px] font-bold uppercase tracking-wider text-gray-500">
                            <tr>
                                <th class="px-4 py-3.5 font-bold">Koperasi & BH</th>
                                <th class="px-4 py-3.5 text-center font-bold">Tgl RAT</th>
                                <th class="px-4 py-3.5 font-bold">Tempat Pelaksanaan</th>
                                <th class="px-4 py-3.5 text-center font-bold">Hadir & Kuorum</th>
                                <th class="px-4 py-3.5 text-center font-bold">Status Keabsahan</th>
                                <th class="px-4 py-3.5 text-center font-bold">Berkas LPJ</th>
                                <th class="px-4 py-3.5 text-center font-bold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="item in rats.data" :key="item.id" class="group transition hover:bg-gray-50/60">
                                <td class="px-4 py-3.5">
                                    <div class="font-bold text-gray-900 transition group-hover:text-teal-600">
                                        {{ item.koperasi?.nama_koperasi }}
                                    </div>
                                    <div class="mt-0.5 flex items-center gap-2 text-[11px] font-medium text-gray-500">
                                        <span>{{ item.koperasi?.kabupaten_kota }}</span>
                                        <span>•</span>
                                        <span class="font-mono text-gray-400">{{ item.koperasi?.no_badan_hukum }}</span>
                                    </div>
                                </td>

                                <td class="px-4 py-3.5 text-center font-mono font-medium text-gray-700">
                                    {{ item.tanggal_rat }}
                                </td>

                                <td class="px-4 py-3.5 font-medium text-gray-700">
                                    {{ item.tempat_pelaksanaan }}
                                </td>

                                <td class="px-4 py-3.5 text-center">
                                    <div class="font-bold text-gray-900">{{ item.jumlah_anggota_hadir }} Orang</div>
                                    <span class="text-[10px] font-bold" :class="item.kuorum ? 'text-emerald-600' : 'text-rose-600'">
                                        {{ item.kuorum ? 'Kuorum Memenuhi' : 'Tidak Kuorum' }}
                                    </span>
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

                                <td class="px-4 py-3.5 text-center">
                                    <a
                                        v-if="item.file_lpj_path"
                                        :href="`/file-download?path=${encodeURIComponent(item.file_lpj_path)}`"
                                        target="_blank"
                                        class="inline-flex items-center gap-1.5 rounded-xl border border-teal-200 bg-teal-50 px-3 py-1.5 text-xs font-bold text-teal-700 transition hover:bg-teal-100"
                                    >
                                        <FileText class="h-3.5 w-3.5" />
                                        Unduh LPJ
                                    </a>
                                    <span v-else class="text-[11px] italic text-gray-400">Belum Ada LPJ</span>
                                </td>

                                <td class="px-4 py-3.5 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <!-- Admin Actions: Edit & Delete -->
                                        <template v-if="userRole === 'admin_koperasi'">
                                            <button
                                                @click="openEditModal(item)"
                                                class="rounded-lg p-1.5 text-gray-400 transition hover:bg-cyan-50 hover:text-cyan-600"
                                                title="Edit Data RAT"
                                            >
                                                <Edit3 class="h-4 w-4" />
                                            </button>

                                            <button
                                                @click="handleDelete(item)"
                                                class="rounded-lg p-1.5 text-gray-400 transition hover:bg-rose-50 hover:text-rose-600"
                                                title="Hapus Data RAT"
                                            >
                                                <Trash2 class="h-4 w-4" />
                                            </button>
                                        </template>

                                        <!-- Pengawas Actions: Verifikasi (Sah) & Tolak -->
                                        <template v-if="userRole === 'bidang_pengawasan'">
                                            <button
                                                v-if="item.status_verifikasi !== 'verified'"
                                                @click="handleVerifikasi(item)"
                                                title="Verifikasi Laporan RAT"
                                                class="flex items-center gap-1 rounded-lg bg-emerald-50 px-2 py-1 text-[11px] font-bold text-emerald-700 transition hover:bg-emerald-100"
                                            >
                                                <CheckCircle2 class="h-3.5 w-3.5 text-emerald-600" />
                                                Verifikasi
                                            </button>

                                            <button
                                                v-if="item.status_verifikasi !== 'rejected'"
                                                @click="openRejectModal(item)"
                                                title="Tolak Laporan RAT"
                                                class="flex items-center gap-1 rounded-lg bg-rose-50 px-2 py-1 text-[11px] font-bold text-rose-700 transition hover:bg-rose-100"
                                            >
                                                <XCircle class="h-3.5 w-3.5 text-rose-600" />
                                                Tolak
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
                        Halaman <span class="font-bold text-gray-900">{{ rats.current_page }}</span> dari
                        <span class="font-bold text-gray-900">{{ rats.last_page }}</span>
                    </div>

                    <div class="flex items-center gap-1">
                        <component
                            v-for="(link, index) in rats.links"
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

        <!-- FORM MODAL INPUT RAT DIALOG (Admin Only) -->
        <div
            v-if="isModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/60 p-4 backdrop-blur-sm duration-200 animate-in fade-in sm:p-6"
        >
            <div class="relative my-8 w-full max-w-xl overflow-hidden rounded-3xl border border-gray-200/80 bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-gray-100 bg-white px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-teal-50 text-teal-600">
                            <FileSpreadsheet class="h-5 w-5" />
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">
                                {{ ratToEdit ? 'Edit Pelaksanaan RAT' : 'Input Pelaksanaan RAT Baru' }}
                            </h3>
                            <p class="text-xs text-gray-500">Verifikasi Batas Waktu 30 Juni</p>
                        </div>
                    </div>

                    <button @click="isModalOpen = false" class="rounded-xl p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-900">
                        <X class="h-5 w-5" />
                    </button>
                </div>

                <form @submit.prevent="handleSubmit" class="space-y-4 p-6">
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">Pilih Koperasi *</label>
                        <select
                            v-model="form.koperasi_id"
                            :disabled="!!ratToEdit"
                            required
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition duration-150 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 disabled:opacity-60 sm:text-sm"
                        >
                            <option v-for="k in koperasis" :key="k.id" :value="k.id">{{ k.nama_koperasi }} ({{ k.kabupaten_kota }})</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">Tahun Buku *</label>
                            <input
                                v-model="form.tahun_buku"
                                type="number"
                                required
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition duration-150 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                            />
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">Tanggal Pelaksanaan RAT *</label>
                            <input
                                v-model="form.tanggal_rat"
                                type="date"
                                required
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition duration-150 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">Tempat Pelaksanaan *</label>
                        <input
                            v-model="form.tempat_pelaksanaan"
                            type="text"
                            required
                            placeholder="Aula Kantor Koperasi / Gedung Pertemuan..."
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition duration-150 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">Jumlah Anggota Hadir *</label>
                            <input
                                v-model="form.jumlah_anggota_hadir"
                                type="number"
                                required
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition duration-150 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                            />
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">Status Kuorum *</label>
                            <select
                                v-model="form.kuorum"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition duration-150 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                            >
                                <option :value="true">Memenuhi Kuorum</option>
                                <option :value="false">Tidak Kuorum</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700"
                            >Upload Berkas LPJ / Berita Acara (PDF/DOC)</label
                        >
                        <input
                            @change="handleFileChange"
                            type="file"
                            accept=".pdf,.doc,.docx"
                            class="w-full text-xs text-gray-500 transition file:mr-4 file:rounded-xl file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-xs file:font-bold file:text-white hover:file:bg-slate-800"
                        />
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">Catatan Pelaksanaan (Opsional)</label>
                        <textarea
                            v-model="form.catatan"
                            rows="2"
                            placeholder="Catatan persetujuan pertanggungjawaban pengurus..."
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition duration-150 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-4">
                        <button
                            type="button"
                            @click="isModalOpen = false"
                            class="rounded-xl px-4 py-2.5 text-xs font-bold text-gray-600 transition hover:bg-gray-100"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-xl bg-slate-900 px-5 py-2.5 text-xs font-bold text-white shadow-md shadow-slate-900/10 transition hover:bg-slate-800 disabled:opacity-50"
                        >
                            Simpan Data RAT
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Tolak Dokumen (Pengawas Only) -->
        <div v-if="isRejectModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-xs">
            <div class="w-full max-w-md space-y-4 rounded-3xl bg-white p-6 shadow-xl">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="flex items-center gap-2 text-sm font-bold text-rose-700">
                        <XCircle class="h-5 w-5 text-rose-600" />
                        Tolak Laporan RAT Koperasi
                    </h3>
                    <button @click="isRejectModalOpen = false" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>

                <div class="space-y-3">
                    <p class="text-xs text-gray-600">
                        Anda akan menolak Laporan RAT Koperasi
                        <strong class="text-gray-900">{{ selectedRatForReject?.koperasi?.nama_koperasi }}</strong>.
                        Mohon berikan alasan penolakan:
                    </p>

                    <div>
                        <label class="mb-1 block text-xs font-bold text-gray-700">Alasan Penolakan <span class="text-rose-500">*</span></label>
                        <textarea
                            v-model="alasanPenolakanInput"
                            rows="4"
                            placeholder="Contoh: Berkas LPJ tidak melampirkan neraca keuangan yang telah diaudit..."
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
