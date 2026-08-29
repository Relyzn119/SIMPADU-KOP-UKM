<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { AlertTriangle, Edit3, RotateCcw, Search, ShieldCheck, Trash2, X } from 'lucide-vue-next';
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
    };
    kabupatenKotaList: string[];
}>();

const page = usePage();
const userRole = (page.props.auth as any)?.user?.role || 'admin_koperasi';

const search = ref(props.filters.search || '');
const tingkatRisiko = ref(props.filters.tingkat_risiko || '');
const statusTindakLanjut = ref(props.filters.status_tindak_lanjut || '');
const aspekTemuan = ref(props.filters.aspek_temuan || '');

// Modal States
const isTindakLanjutModalOpen = ref(false);
const isVerifikasiModalOpen = ref(false);
const activeTemuan = ref<any | null>(null);

const tindakLanjutForm = useForm({
    tanggapan_koperasi: '',
    status_tindak_lanjut: 'Dalam Proses',
});

const verifikasiForm = useForm({
    catatan_verifikasi_pengawas: '',
    status_tindak_lanjut: 'Selesai',
});

const applyFilters = () => {
    router.get(
        '/temuan',
        {
            search: search.value || undefined,
            tingkat_risiko: tingkatRisiko.value || undefined,
            status_tindak_lanjut: statusTindakLanjut.value || undefined,
            aspek_temuan: aspekTemuan.value || undefined,
        },
        { preserveState: true, replace: true },
    );
};

const resetFilters = () => {
    search.value = '';
    tingkatRisiko.value = '';
    statusTindakLanjut.value = '';
    aspekTemuan.value = '';
    applyFilters();
};

const openTindakLanjutModal = (item: any) => {
    activeTemuan.value = item;
    tindakLanjutForm.tanggapan_koperasi = item.tanggapan_koperasi || '';
    tindakLanjutForm.status_tindak_lanjut = item.status_tindak_lanjut === 'Belum Ditindaklanjuti' ? 'Dalam Proses' : item.status_tindak_lanjut;
    isTindakLanjutModalOpen.value = true;
};

const submitTindakLanjut = () => {
    if (!activeTemuan.value) return;
    tindakLanjutForm.put(`/temuan/${activeTemuan.value.id}/tindak-lanjut`, {
        onSuccess: () => {
            isTindakLanjutModalOpen.value = false;
            tindakLanjutForm.reset();
        },
    });
};

const openVerifikasiModal = (item: any) => {
    activeTemuan.value = item;
    verifikasiForm.catatan_verifikasi_pengawas = item.catatan_verifikasi_pengawas || '';
    verifikasiForm.status_tindak_lanjut = item.status_tindak_lanjut || 'Selesai';
    isVerifikasiModalOpen.value = true;
};

const submitVerifikasi = () => {
    if (!activeTemuan.value) return;
    verifikasiForm.put(`/temuan/${activeTemuan.value.id}/verifikasi`, {
        onSuccess: () => {
            isVerifikasiModalOpen.value = false;
            verifikasiForm.reset();
        },
    });
};

const handleDelete = (item: any) => {
    if (confirm(`Apakah Anda yakin ingin menghapus temuan audit ini?`)) {
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
        <Head title="Matriks Temuan Audit & Tindak Lanjut" />

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
                            Monitoring tingkat risiko pemeriksaan, tanggapan pengurus koperasi, dan verifikasi tim pengawas Diskop Provsu.
                        </p>
                    </div>
                </div>
            </div>

            <!-- KPI SUMMARY CARDS -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="shadow-2xs rounded-3xl border border-gray-200/70 bg-white p-5">
                    <div class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Temuan Audit</div>
                    <div class="mt-2 font-mono text-3xl font-extrabold text-gray-900">{{ summary.total }}</div>
                    <p class="mt-1 text-[11px] font-medium text-gray-400">Hasil Pengawasan Lapangan</p>
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
                    <div class="text-xs font-bold uppercase tracking-wider text-emerald-700">Selesai Diverifikasi</div>
                    <div class="mt-2 font-mono text-3xl font-extrabold text-gray-900">{{ summary.selesai }}</div>
                    <p class="mt-1 text-[11px] font-medium text-gray-400">Sesuai Rekomendasi Diskop</p>
                </div>
            </div>

            <!-- SEARCH & FILTER BAR CARD -->
            <div class="shadow-2xs space-y-4 rounded-3xl border border-gray-200/70 bg-white p-5">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
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
                            <option value="Selesai">Selesai Diverifikasi</option>
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
                                <th class="px-4 py-3.5 text-center font-bold">Risiko</th>
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
                                        <span>{{ item.koperasi?.kabupaten_kota }}</span>
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

                                    <!-- Verifikasi Catatan Preview -->
                                    <div
                                        v-if="item.catatan_verifikasi_pengawas"
                                        class="rounded-xl border border-emerald-200 bg-emerald-50/60 p-2 text-[11px] text-emerald-800"
                                    >
                                        <strong class="font-bold text-emerald-700">Catatan Pengawas:</strong> {{ item.catatan_verifikasi_pengawas }}
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-4 py-3.5 text-center font-mono font-medium text-gray-700">
                                    {{ item.batas_waktu }}
                                </td>

                                <td class="px-4 py-3.5 text-center">
                                    <span
                                        class="inline-flex rounded-full border px-2.5 py-1 text-[10px] font-bold"
                                        :class="getRiskBadge(item.tingkat_risiko)"
                                    >
                                        {{ item.tingkat_risiko }}
                                    </span>
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
                                        <!-- Update Action Plan (Role Admin Koperasi & Pengawas) -->
                                        <button
                                            @click="openTindakLanjutModal(item)"
                                            class="flex items-center gap-1 rounded-xl border border-amber-200 bg-amber-50 px-2.5 py-1.5 text-[11px] font-bold text-amber-700 transition hover:bg-amber-100"
                                            title="Update Action Plan"
                                        >
                                            <Edit3 class="h-3.5 w-3.5" />
                                            Action Plan
                                        </button>

                                        <!-- Verification (Role Bidang Pengawasan) -->
                                        <button
                                            v-if="userRole === 'bidang_pengawasan'"
                                            @click="openVerifikasiModal(item)"
                                            class="flex items-center gap-1 rounded-xl border border-emerald-200 bg-emerald-50 px-2.5 py-1.5 text-[11px] font-bold text-emerald-700 transition hover:bg-emerald-100"
                                            title="Verifikasi Pengawas"
                                        >
                                            <ShieldCheck class="h-3.5 w-3.5" />
                                            Verifikasi
                                        </button>

                                        <!-- Hapus Temuan (Role Bidang Pengawasan) -->
                                        <button
                                            v-if="userRole === 'bidang_pengawasan'"
                                            @click="handleDelete(item)"
                                            class="rounded-lg p-1.5 text-gray-400 transition hover:bg-rose-50 hover:text-rose-600"
                                            title="Hapus Temuan"
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

        <!-- MODAL 1: UPDATE ACTION PLAN (ROLE ADMIN KOPERASI) -->
        <div
            v-if="isTindakLanjutModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/60 p-4 backdrop-blur-sm"
        >
            <div class="relative my-8 w-full max-w-lg space-y-4 rounded-3xl border border-gray-200/80 bg-white p-6 shadow-2xl">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="flex items-center gap-2 text-sm font-bold text-gray-900">
                        <Edit3 class="h-4 w-4 text-amber-600" />
                        Update Progress Action Plan Koperasi
                    </h3>
                    <button
                        @click="isTindakLanjutModalOpen = false"
                        class="rounded-xl p-1 text-gray-400 transition hover:bg-gray-100 hover:text-gray-900"
                    >
                        <X class="h-5 w-5" />
                    </button>
                </div>

                <div class="space-y-1 rounded-2xl border border-gray-200 bg-gray-50 p-3 text-xs text-gray-700">
                    <div><strong>Koperasi:</strong> {{ activeTemuan?.koperasi?.nama_koperasi }}</div>
                    <div><strong>Temuan:</strong> {{ activeTemuan?.deskripsi_temuan }}</div>
                </div>

                <form @submit.prevent="submitTindakLanjut" class="space-y-4">
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">Status Progress *</label>
                        <select
                            v-model="tindakLanjutForm.status_tindak_lanjut"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                        >
                            <option value="Dalam Proses">Dalam Proses (Sedang Ditindaklanjuti)</option>
                            <option value="Selesai">Selesai (Siap Diverifikasi Pengawas)</option>
                            <option value="Belum Ditindaklanjuti">Belum Ditindaklanjuti</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700"
                            >Tanggapan & Penjelasan Action Plan Pengurus *</label
                        >
                        <textarea
                            v-model="tindakLanjutForm.tanggapan_koperasi"
                            rows="4"
                            required
                            placeholder="Jelaskan langkah nyata yang telah dilakukan koperasi (misal: SOP telah disahkan pada tgl...)"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-2">
                        <button
                            type="button"
                            @click="isTindakLanjutModalOpen = false"
                            class="rounded-xl px-4 py-2 text-xs font-bold text-gray-500 transition hover:bg-gray-100"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="rounded-xl bg-slate-900 px-5 py-2 text-xs font-bold text-white shadow-md transition hover:bg-slate-800"
                        >
                            Simpan Action Plan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL 2: VERIFIKASI PENGAWAS (ROLE BIDANG PENGAWASAN) -->
        <div
            v-if="isVerifikasiModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/60 p-4 backdrop-blur-sm"
        >
            <div class="relative my-8 w-full max-w-lg space-y-4 rounded-3xl border border-gray-200/80 bg-white p-6 shadow-2xl">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="flex items-center gap-2 text-sm font-bold text-gray-900">
                        <ShieldCheck class="h-4 w-4 text-emerald-600" />
                        Verifikasi & Approval Pengawas Diskop Provsu
                    </h3>
                    <button
                        @click="isVerifikasiModalOpen = false"
                        class="rounded-xl p-1 text-gray-400 transition hover:bg-gray-100 hover:text-gray-900"
                    >
                        <X class="h-5 w-5" />
                    </button>
                </div>

                <div class="space-y-1 rounded-2xl border border-gray-200 bg-gray-50 p-3 text-xs text-gray-700">
                    <div><strong>Koperasi:</strong> {{ activeTemuan?.koperasi?.nama_koperasi }}</div>
                    <div><strong>Action Plan Pengurus:</strong> {{ activeTemuan?.tanggapan_koperasi || '-' }}</div>
                </div>

                <form @submit.prevent="submitVerifikasi" class="space-y-4">
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">Keputusan Status Verifikasi *</label>
                        <select
                            v-model="verifikasiForm.status_tindak_lanjut"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                        >
                            <option value="Selesai">Disetujui / Selesai (Sesuai Rekomendasi)</option>
                            <option value="Dalam Proses">Perlu Perbaikan (Kembalikan ke Koperasi)</option>
                            <option value="Belum Ditindaklanjuti">Ditolak / Belum Ditindaklanjuti</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700"
                            >Catatan Hasil Verifikasi Pengawas *</label
                        >
                        <textarea
                            v-model="verifikasiForm.catatan_verifikasi_pengawas"
                            rows="4"
                            required
                            placeholder="Catatan tim pengawas mengenai kesesuaian dokumen/bukti tindak lanjut..."
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-2">
                        <button
                            type="button"
                            @click="isVerifikasiModalOpen = false"
                            class="rounded-xl px-4 py-2 text-xs font-bold text-gray-500 transition hover:bg-gray-100"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="rounded-xl bg-slate-900 px-5 py-2 text-xs font-bold text-white shadow-md transition hover:bg-slate-800"
                        >
                            Simpan Verifikasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
