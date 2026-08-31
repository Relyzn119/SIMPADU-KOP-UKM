<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { AlertTriangle, CheckCircle2, Clock, Edit3, Plus, RotateCcw, Search, Trash2, User, X } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    temuans: {
        data: Array<any>;
        links: Array<any>;
        current_page: number;
        last_page: number;
        total: number;
        from: number;
        to: number;
    };
    summary: {
        total: number;
        kritis_open: number;
        dalam_proses: number;
        selesai: number;
    };
    filters: {
        search?: string;
        tingkat_risiko?: string;
        status_tindak_lanjut?: string;
        aspek_temuan?: string;
        status_persetujuan_koperasi?: string;
    };
    kabupatenKotaList: string[];
}>();

const page = usePage();
const userRole = (page.props.auth as any)?.user?.role || 'admin_koperasi';

const search = ref(props.filters.search || '');
const tingkatRisiko = ref(props.filters.tingkat_risiko || '');
const statusTindakLanjut = ref(props.filters.status_tindak_lanjut || '');
const aspekTemuan = ref(props.filters.aspek_temuan || '');
const statusPersetujuan = ref(props.filters.status_persetujuan_koperasi || '');

// Modal Create Temuan State (Pengawas Only)
const isCreateTemuanModalOpen = ref(false);
const createForm = useForm({
    koperasi_id: 1,
    aspek_temuan: 'Operasional',
    deskripsi_temuan: '',
    rekomendasi: '',
    batas_waktu: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
    tingkat_risiko: 'Sedang',
});

// Modal Setujui & Selesaikan State (Admin Koperasi)
const isSetujuiModalOpen = ref(false);
const activeTemuan = ref<any | null>(null);
const setujuiForm = useForm({
    tanggapan_koperasi: '',
    file_bukti_tindak_lanjut: null as File | null,
});

const applyFilters = () => {
    router.get(
        '/temuan',
        {
            search: search.value || undefined,
            tingkat_risiko: tingkatRisiko.value || undefined,
            status_tindak_lanjut: statusTindakLanjut.value || undefined,
            aspek_temuan: aspekTemuan.value || undefined,
            status_persetujuan_koperasi: statusPersetujuan.value || undefined,
        },
        { preserveState: true, replace: true },
    );
};

const resetFilters = () => {
    search.value = '';
    tingkatRisiko.value = '';
    statusTindakLanjut.value = '';
    aspekTemuan.value = '';
    statusPersetujuan.value = '';
    applyFilters();
};

const openCreateTemuanModal = () => {
    isCreateTemuanModalOpen.value = true;
};

const submitCreateTemuan = () => {
    createForm.post('/temuan', {
        onSuccess: () => {
            isCreateTemuanModalOpen.value = false;
            createForm.reset();
        },
    });
};

const openSetujuiModal = (item: any) => {
    activeTemuan.value = item;
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
    if (!activeTemuan.value) return;

    router.post(
        `/temuan/${activeTemuan.value.id}/setujui`,
        {
            _method: 'PUT',
            tanggapan_koperasi: setujuiForm.tanggapan_koperasi,
            file_bukti_tindak_lanjut: setujuiForm.file_bukti_tindak_lanjut,
        },
        {
            onSuccess: () => {
                isSetujuiModalOpen.value = false;
                setujuiForm.reset();
                activeTemuan.value = null;
            },
        },
    );
};

const handleDelete = (item: any) => {
    if (confirm(`Apakah Anda yakin ingin menghapus data temuan audit ini?`)) {
        router.delete(`/temuan/${item.id}`);
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

const getStatusBadge = (status: string) => {
    switch (status) {
        case 'Selesai':
            return 'bg-emerald-50 text-emerald-700 border-emerald-200';
        case 'Dalam Proses':
            return 'bg-amber-50 text-amber-700 border-amber-200';
        default:
            return 'bg-rose-50 text-rose-700 border-rose-200';
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Matriks Temuan Audit & Action Plan Tindak Lanjut" />

        <div class="space-y-6">
            <!-- PAGE TITLE BANNER CARD -->
            <div class="shadow-2xs rounded-3xl border border-gray-200/70 bg-white p-6">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div>
                        <h1 class="flex items-center gap-2.5 text-xl font-bold tracking-tight text-gray-900 sm:text-2xl">
                            <AlertTriangle class="h-6 w-6 text-amber-600" />
                            Matriks Temuan Audit & Action Plan Tindak Lanjut
                        </h1>
                        <p class="mt-1 text-xs text-gray-500">
                            Monitoring temuan audit lapangan yang diterbitkan oleh Tim Pengawas Diskop Provsu.
                        </p>
                    </div>

                    <!-- Create Link Shown ONLY for bidang_pengawasan -->
                    <button
                        v-if="userRole === 'bidang_pengawasan'"
                        @click="openCreateTemuanModal"
                        class="shadow-xs flex items-center gap-2 self-start rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-slate-800 sm:self-auto"
                    >
                        <Plus class="h-4 w-4 text-emerald-400" />
                        Tambah Temuan Audit
                    </button>
                </div>
            </div>

            <!-- KPI SUMMARY CARDS -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="shadow-2xs rounded-3xl border border-gray-200/70 bg-white p-5">
                    <div class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Temuan Audit</div>
                    <div class="mt-2 font-mono text-3xl font-extrabold text-gray-900">{{ summary.total }}</div>
                    <p class="mt-1 text-[11px] font-medium text-gray-400">Hasil Pemeriksaan Pengawas</p>
                </div>

                <div class="shadow-2xs rounded-3xl border border-gray-200/70 bg-white p-5">
                    <div class="text-xs font-bold uppercase tracking-wider text-rose-700">Risiko Kritis (Open)</div>
                    <div class="mt-2 font-mono text-3xl font-extrabold text-gray-900">{{ summary.kritis_open }}</div>
                    <p class="mt-1 text-[11px] font-medium text-gray-400">Penanganan Prioritas</p>
                </div>

                <div class="shadow-2xs rounded-3xl border border-gray-200/70 bg-white p-5">
                    <div class="text-xs font-bold uppercase tracking-wider text-amber-700">Dalam Proses Tindak Lanjut</div>
                    <div class="mt-2 font-mono text-3xl font-extrabold text-gray-900">{{ summary.dalam_proses }}</div>
                    <p class="mt-1 text-[11px] font-medium text-gray-400">Respon Pengurus Dikirim</p>
                </div>

                <div class="shadow-2xs rounded-3xl border border-gray-200/70 bg-white p-5">
                    <div class="text-xs font-bold uppercase tracking-wider text-emerald-700">Disetujui & Selesai</div>
                    <div class="mt-2 font-mono text-3xl font-extrabold text-gray-900">{{ summary.selesai }}</div>
                    <p class="mt-1 text-[11px] font-medium text-gray-400">Diselesaikan Pengurus Koperasi</p>
                </div>
            </div>

            <!-- SEARCH & FILTER BAR CARD -->
            <div class="shadow-2xs space-y-4 rounded-3xl border border-gray-200/70 bg-white p-5">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5">
                    <!-- Search Input -->
                    <div class="relative">
                        <Search class="absolute left-3.5 top-3 h-4 w-4 text-gray-400" />
                        <input
                            v-model="search"
                            @input="applyFilters"
                            type="text"
                            placeholder="Cari Temuan / Koperasi..."
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-9 pr-4 text-xs text-gray-900 transition placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                        />
                    </div>

                    <!-- Filter Tingkat Risiko -->
                    <div>
                        <select
                            v-model="tingkatRisiko"
                            @change="applyFilters"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-xs text-gray-900 transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                        >
                            <option value="">Semua Tingkat Risiko</option>
                            <option value="Kritis">Kritis (Merah)</option>
                            <option value="Tinggi">Tinggi (Oranye)</option>
                            <option value="Sedang">Sedang (Kuning)</option>
                            <option value="Rendah">Rendah (Biru)</option>
                        </select>
                    </div>

                    <!-- Filter Status Tindak Lanjut -->
                    <div>
                        <select
                            v-model="statusTindakLanjut"
                            @change="applyFilters"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-xs text-gray-900 transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                        >
                            <option value="">Semua Status TL</option>
                            <option value="Belum Ditindaklanjuti">Belum Ditindaklanjuti</option>
                            <option value="Dalam Proses">Dalam Proses</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </div>

                    <!-- Filter Aspek -->
                    <div>
                        <select
                            v-model="aspekTemuan"
                            @change="applyFilters"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-xs text-gray-900 transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                        >
                            <option value="">Semua Aspek Temuan</option>
                            <option value="Kelembagaan">Kelembagaan</option>
                            <option value="Keuangan">Keuangan</option>
                            <option value="Operasional">Operasional</option>
                            <option value="Usaha">Usaha</option>
                        </select>
                    </div>

                    <!-- Filter Status Persetujuan -->
                    <div>
                        <select
                            v-model="statusPersetujuan"
                            @change="applyFilters"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-xs text-gray-900 transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                        >
                            <option value="">Semua Persetujuan</option>
                            <option value="pending">Sedang Diproses (Pending)</option>
                            <option value="approved">Disetujui Pengurus (Sah)</option>
                        </select>
                    </div>
                </div>

                <!-- Reset Filters -->
                <div class="flex items-center justify-between border-t border-gray-100 pt-2 text-xs">
                    <span class="font-medium text-gray-500"
                        >Menampilkan {{ temuans.from || 0 }} - {{ temuans.to || 0 }} dari {{ temuans.total }} Temuan</span
                    >
                    <button @click="resetFilters" class="flex items-center gap-1 text-xs font-bold text-gray-500 transition hover:text-emerald-700">
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
                                <th class="px-4 py-3.5 font-bold">Koperasi & Aspek</th>
                                <th class="px-4 py-3.5 font-bold">Deskripsi Temuan & Rekomendasi</th>
                                <th class="px-4 py-3.5 text-center font-bold">Batas Waktu</th>
                                <th class="px-4 py-3.5 text-center font-bold">Persetujuan Koperasi</th>
                                <th class="px-4 py-3.5 text-center font-bold">Status TL</th>
                                <th class="px-4 py-3.5 text-center font-bold">Aksi Workflow</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="item in temuans.data" :key="item.id" class="group transition hover:bg-gray-50/60">
                                <td class="px-4 py-3.5">
                                    <div class="font-bold text-gray-900 transition group-hover:text-emerald-600">
                                        {{ item.koperasi?.nama_koperasi }}
                                    </div>
                                    <div class="mt-0.5 flex items-center gap-2 text-[11px] font-medium text-gray-500">
                                        <span class="rounded bg-gray-100 px-2 py-0.5 font-bold text-gray-700">{{ item.aspek_temuan }}</span>
                                        <span>•</span>
                                        <span>Dibuat: {{ item.created_by?.name || 'Pengawas' }}</span>
                                    </div>
                                </td>

                                <td class="max-w-sm space-y-1 px-4 py-3.5">
                                    <div class="font-medium leading-snug text-gray-800">{{ item.deskripsi_temuan }}</div>
                                    <div class="text-[11px] font-bold text-emerald-700">Rekomendasi: {{ item.rekomendasi }}</div>

                                    <!-- Tanggapan Preview -->
                                    <div
                                        v-if="item.tanggapan_koperasi"
                                        class="rounded-xl border border-gray-200 bg-gray-50 p-2 text-[11px] text-gray-600"
                                    >
                                        <strong class="font-bold text-indigo-700">Action Plan Koperasi:</strong> {{ item.tanggapan_koperasi }}
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-4 py-3.5 text-center font-mono font-medium text-gray-700">
                                    {{ item.batas_waktu }}
                                </td>

                                <!-- Persetujuan Koperasi -->
                                <td class="px-4 py-3.5 text-center">
                                    <div v-if="item.status_persetujuan_koperasi === 'approved'" class="inline-flex flex-col items-center">
                                        <span class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[10px] font-bold text-emerald-700">
                                            <CheckCircle2 class="h-3 w-3 text-emerald-600" />
                                            Disetujui Pengurus
                                        </span>
                                    </div>
                                    <div v-else class="inline-flex flex-col items-center">
                                        <span class="inline-flex items-center gap-1 rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-[10px] font-bold text-amber-700">
                                            <Clock class="h-3 w-3 text-amber-600" />
                                            Sedang Diproses
                                        </span>
                                    </div>
                                </td>

                                <td class="px-4 py-3.5 text-center">
                                    <span
                                        class="inline-flex rounded-full border px-2.5 py-1 text-[10px] font-bold"
                                        :class="getStatusBadge(item.status_tindak_lanjut)"
                                    >
                                        {{ item.status_tindak_lanjut }}
                                    </span>
                                </td>

                                <td class="px-4 py-3.5 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- Setujui & Selesaikan (Role Admin Koperasi) -->
                                        <button
                                            v-if="userRole === 'admin_koperasi'"
                                            @click="openSetujuiModal(item)"
                                            class="flex items-center gap-1 rounded-xl border border-emerald-200 bg-emerald-50 px-2.5 py-1.5 text-[11px] font-bold text-emerald-700 transition hover:bg-emerald-100"
                                            title="Setujui & Selesaikan Temuan"
                                        >
                                            <CheckCircle2 class="h-3.5 w-3.5 text-emerald-600" />
                                            {{ item.status_persetujuan_koperasi === 'approved' ? 'Edit Tanggapan' : 'Setujui & Tanggapi' }}
                                        </button>

                                        <!-- Hapus Temuan (Role Bidang Pengawasan ONLY) -->
                                        <button
                                            v-if="userRole === 'bidang_pengawasan'"
                                            @click="handleDelete(item)"
                                            class="rounded-lg p-1.5 text-gray-400 transition hover:bg-rose-50 hover:text-rose-600"
                                            title="Hapus Temuan Audit"
                                        >
                                            <Trash2 class="h-3.5 w-3.5" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Footer -->
                <div
                    v-if="temuans.links && temuans.links.length > 3"
                    class="flex items-center justify-between border-t border-gray-100 bg-gray-50/50 px-6 py-4"
                >
                    <div class="text-xs font-medium text-gray-500">
                        Halaman <span class="font-bold text-gray-900">{{ temuans.current_page }}</span> dari
                        <span class="font-bold text-gray-900">{{ temuans.last_page }}</span>
                    </div>

                    <div class="flex items-center gap-1">
                        <component
                            v-for="(link, index) in temuans.links"
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

        <!-- MODAL 1: TAMBAH TEMUAN AUDIT (ROLE BIDANG PENGAWASAN) -->
        <div
            v-if="isCreateTemuanModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/60 p-4 backdrop-blur-sm"
        >
            <div class="relative my-8 w-full max-w-lg space-y-4 rounded-3xl border border-gray-200/80 bg-white p-6 shadow-2xl">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="flex items-center gap-2 text-sm font-bold text-gray-900">
                        <Plus class="h-4 w-4 text-emerald-600" />
                        Tambah Temuan Audit Lapangan Baru
                    </h3>
                    <button
                        @click="isCreateTemuanModalOpen = false"
                        class="rounded-xl p-1 text-gray-400 transition hover:bg-gray-100 hover:text-gray-900"
                    >
                        <X class="h-5 w-5" />
                    </button>
                </div>

                <form @submit.prevent="submitCreateTemuan" class="space-y-4">
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">Aspek Temuan *</label>
                        <select
                            v-model="createForm.aspek_temuan"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                        >
                            <option value="Kelembagaan">Kelembagaan</option>
                            <option value="Keuangan">Keuangan</option>
                            <option value="Operasional">Operasional</option>
                            <option value="Usaha">Usaha</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">Tingkat Risiko *</label>
                        <select
                            v-model="createForm.tingkat_risiko"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                        >
                            <option value="Rendah">Rendah (Biru)</option>
                            <option value="Sedang">Sedang (Kuning)</option>
                            <option value="Tinggi">Tinggi (Oranye)</option>
                            <option value="Kritis">Kritis (Merah)</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">Batas Waktu Jatuh Tempo *</label>
                        <input
                            v-model="createForm.batas_waktu"
                            type="date"
                            required
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                        />
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">Deskripsi Temuan Audit *</label>
                        <textarea
                            v-model="createForm.deskripsi_temuan"
                            rows="3"
                            required
                            placeholder="Catatan hasil temuan lapangan pengawas..."
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                        ></textarea>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">Rekomendasi Tim Pengawas *</label>
                        <textarea
                            v-model="createForm.rekomendasi"
                            rows="3"
                            required
                            placeholder="Instruksi & perbaikan yang harus dilakukan pengurus..."
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-2">
                        <button
                            type="button"
                            @click="isCreateTemuanModalOpen = false"
                            class="rounded-xl px-4 py-2 text-xs font-bold text-gray-500 transition hover:bg-gray-100"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="rounded-xl bg-slate-900 px-5 py-2 text-xs font-bold text-white shadow-md transition hover:bg-slate-800"
                        >
                            Simpan Temuan Audit
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL 2: SETUJUI & TANGGAPI TEMUAN AUDIT (ROLE ADMIN KOPERASI) -->
        <div
            v-if="isSetujuiModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/60 p-4 backdrop-blur-sm"
        >
            <div class="relative my-8 w-full max-w-lg space-y-4 rounded-3xl border border-gray-200/80 bg-white p-6 shadow-2xl">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="flex items-center gap-2 text-sm font-bold text-gray-900">
                        <CheckCircle2 class="h-4 w-4 text-emerald-600" />
                        Setujui & Selesaikan Temuan Audit Koperasi
                    </h3>
                    <button
                        @click="isSetujuiModalOpen = false"
                        class="rounded-xl p-1 text-gray-400 transition hover:bg-gray-100 hover:text-gray-900"
                    >
                        <X class="h-5 w-5" />
                    </button>
                </div>

                <div class="space-y-1 rounded-2xl border border-gray-200 bg-gray-50 p-3 text-xs text-gray-700">
                    <div><strong>Koperasi:</strong> {{ activeTemuan?.koperasi?.nama_koperasi }}</div>
                    <div><strong>Deskripsi Temuan:</strong> {{ activeTemuan?.deskripsi_temuan }}</div>
                    <div><strong>Rekomendasi:</strong> {{ activeTemuan?.rekomendasi }}</div>
                </div>

                <form @submit.prevent="submitSetujui" class="space-y-4">
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700"
                            >Action Plan & Tanggapan Pengurus Koperasi *</label
                        >
                        <textarea
                            v-model="setujuiForm.tanggapan_koperasi"
                            rows="4"
                            required
                            placeholder="Jelaskan perbaikan nyata yang telah diselesaikan oleh pengurus koperasi..."
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                        ></textarea>
                    </div>

                    <!-- Upload File Bukti Digital (Opsi D) -->
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700"
                            >Upload File Bukti Perbaikan (PDF/DOCX/JPG)</label
                        >
                        <input
                            type="file"
                            @change="handleFileChange"
                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-700 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-900 file:px-3 file:py-1 text-xs file:font-bold file:text-white hover:file:bg-slate-800"
                        />
                        <p class="mt-1 text-[10px] text-gray-400">Unggah bukti pendukung perbaikan untuk meningkatkan Indeks Transparansi Koperasi.</p>
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
                            class="rounded-xl bg-emerald-600 px-5 py-2 text-xs font-bold text-white shadow-md transition hover:bg-emerald-700"
                        >
                            Setujui & Selesaikan Temuan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
