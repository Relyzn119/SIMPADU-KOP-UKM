<script setup lang="ts">
import { ref } from 'vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import {
    AlertTriangle,
    Search,
    Clock,
    CheckCircle2,
    XCircle,
    RotateCcw,
    Edit3,
    ShieldCheck,
    X,
    Trash2,
    MessageSquare,
    AlertCircle
} from 'lucide-vue-next';

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
        { preserveState: true, replace: true }
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
        }
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
        }
    });
};

const handleDelete = (item: any) => {
    if (confirm(`Apakah Anda yakin ingin menghapus temuan audit ini?`)) {
        router.delete(`/temuan/${item.id}`);
    }
};

const getRiskBadge = (risiko: string) => {
    switch (risiko) {
        case 'Kritis': return 'bg-rose-500/20 text-rose-300 border-rose-500/40';
        case 'Tinggi': return 'bg-amber-500/20 text-amber-300 border-amber-500/40';
        case 'Sedang': return 'bg-yellow-500/20 text-yellow-300 border-yellow-500/40';
        default: return 'bg-blue-500/20 text-blue-300 border-blue-500/40';
    }
};

const getStatusBadge = (status: string) => {
    switch (status) {
        case 'Selesai': return 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40';
        case 'Dalam Proses': return 'bg-amber-500/20 text-amber-300 border-amber-500/40';
        default: return 'bg-rose-500/20 text-rose-300 border-rose-500/40';
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Matriks Temuan Audit & Tindak Lanjut" />

        <div class="space-y-6">
            <!-- PAGE TITLE BANNER -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight flex items-center gap-2.5">
                        <AlertTriangle class="w-6 h-6 text-amber-400" />
                        Matriks Temuan Audit & Action Plan Tindak Lanjut
                    </h1>
                    <p class="text-xs text-slate-400 mt-1">
                        Monitoring tingkat risiko pemeriksaan, tanggapan pengurus koperasi, dan verifikasi tim pengawas Diskop Provsu.
                    </p>
                </div>
            </div>

            <!-- KPI SUMMARY CARDS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl">
                    <div class="text-xs font-semibold text-slate-400 uppercase">Total Temuan Audit</div>
                    <div class="mt-2 text-3xl font-extrabold text-white">{{ summary.total }}</div>
                    <p class="text-[11px] text-slate-500 mt-1">Seluruh Hasil Pengawasan Lapangan</p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl">
                    <div class="text-xs font-semibold text-rose-400 uppercase">Risiko Kritis (Open)</div>
                    <div class="mt-2 text-3xl font-extrabold text-white">{{ summary.kritis_open }}</div>
                    <p class="text-[11px] text-slate-500 mt-1">Perlu Penanganan Prioritas</p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl">
                    <div class="text-xs font-semibold text-amber-400 uppercase">Dalam Proses Tindak Lanjut</div>
                    <div class="mt-2 text-3xl font-extrabold text-white">{{ summary.dalam_proses }}</div>
                    <p class="text-[11px] text-slate-500 mt-1">Respon Pengurus Dikirim</p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl">
                    <div class="text-xs font-semibold text-emerald-400 uppercase">Selesai Diverifikasi</div>
                    <div class="mt-2 text-3xl font-extrabold text-white">{{ summary.selesai }}</div>
                    <p class="text-[11px] text-slate-500 mt-1">Sesuai Rekomendasi Diskop</p>
                </div>
            </div>

            <!-- SEARCH & FILTER BAR -->
            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <!-- Search Input -->
                    <div class="relative">
                        <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" />
                        <input 
                            v-model="search"
                            @input="applyFilters"
                            type="text"
                            placeholder="Cari Temuan / Koperasi..."
                            class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs placeholder:text-slate-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition"
                        />
                    </div>

                    <!-- Filter Tingkat Risiko -->
                    <div>
                        <select 
                            v-model="tingkatRisiko"
                            @change="applyFilters"
                            class="w-full py-2.5 px-3 rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition"
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
                            class="w-full py-2.5 px-3 rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition"
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
                            class="w-full py-2.5 px-3 rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition"
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
                <div class="flex items-center justify-between pt-2 border-t border-slate-800/60 text-xs">
                    <span class="text-slate-400">Menampilkan {{ temuans.from || 0 }} - {{ temuans.to || 0 }} dari {{ temuans.total }} Temuan</span>
                    <button 
                        @click="resetFilters"
                        class="text-xs text-slate-400 hover:text-amber-400 flex items-center gap-1 transition"
                    >
                        <RotateCcw class="w-3.5 h-3.5" />
                        Reset Filter
                    </button>
                </div>
            </div>

            <!-- TABLE CONTAINER -->
            <div class="rounded-2xl bg-slate-900 border border-slate-800 shadow-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-300">
                        <thead class="bg-slate-950/60 text-slate-400 uppercase tracking-wider text-[11px] border-b border-slate-800">
                            <tr>
                                <th class="py-3.5 px-4 font-bold">Koperasi & Aspek</th>
                                <th class="py-3.5 px-4 font-bold">Deskripsi Temuan & Rekomendasi</th>
                                <th class="py-3.5 px-4 font-bold text-center">Batas Waktu</th>
                                <th class="py-3.5 px-4 font-bold text-center">Risiko</th>
                                <th class="py-3.5 px-4 font-bold text-center">Status TL</th>
                                <th class="py-3.5 px-4 font-bold text-center">Aksi Workflow</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            <tr 
                                v-for="item in temuans.data" 
                                :key="item.id"
                                class="hover:bg-slate-800/40 transition group"
                            >
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-100 group-hover:text-amber-400 transition">
                                        {{ item.koperasi?.nama_koperasi }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 flex items-center gap-2 mt-0.5">
                                        <span class="px-2 py-0.5 rounded bg-slate-800 text-amber-300 font-semibold">{{ item.aspek_temuan }}</span>
                                        <span>•</span>
                                        <span>{{ item.koperasi?.kabupaten_kota }}</span>
                                    </div>
                                </td>

                                <td class="py-3.5 px-4 max-w-sm space-y-1">
                                    <div class="text-slate-200 leading-snug font-medium">{{ item.deskripsi_temuan }}</div>
                                    <div class="text-[11px] text-emerald-400">Rekomendasi: {{ item.rekomendasi }}</div>
                                    
                                    <!-- Tanggapan Preview -->
                                    <div v-if="item.tanggapan_koperasi" class="p-2 rounded bg-slate-950/60 border border-slate-800 text-[11px] text-slate-400">
                                        <strong class="text-purple-300">Action Plan Koperasi:</strong> {{ item.tanggapan_koperasi }}
                                    </div>

                                    <!-- Verifikasi Catatan Preview -->
                                    <div v-if="item.catatan_verifikasi_pengawas" class="p-2 rounded bg-emerald-950/40 border border-emerald-800/40 text-[11px] text-emerald-300">
                                        <strong class="text-emerald-400">Catatan Pengawas:</strong> {{ item.catatan_verifikasi_pengawas }}
                                    </div>
                                </td>

                                <td class="py-3.5 px-4 text-center font-mono text-slate-200 whitespace-nowrap">
                                    {{ item.batas_waktu }}
                                </td>

                                <td class="py-3.5 px-4 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border" :class="getRiskBadge(item.tingkat_risiko)">
                                        {{ item.tingkat_risiko }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold border" :class="getStatusBadge(item.status_tindak_lanjut)">
                                        {{ item.status_tindak_lanjut }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- Update Action Plan (Role Admin Koperasi & Pengawas) -->
                                        <button 
                                            @click="openTindakLanjutModal(item)"
                                            class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-amber-500/10 text-amber-300 border border-amber-500/30 hover:bg-amber-500/20 text-[11px] font-medium transition"
                                            title="Update Action Plan"
                                        >
                                            <Edit3 class="w-3.5 h-3.5" />
                                            Action Plan
                                        </button>

                                        <!-- Verification (Role Bidang Pengawasan) -->
                                        <button 
                                            v-if="userRole === 'bidang_pengawasan'"
                                            @click="openVerifikasiModal(item)"
                                            class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-emerald-500/10 text-emerald-300 border border-emerald-500/30 hover:bg-emerald-500/20 text-[11px] font-medium transition"
                                            title="Verifikasi Pengawas"
                                        >
                                            <ShieldCheck class="w-3.5 h-3.5" />
                                            Verifikasi
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- MODAL 1: UPDATE ACTION PLAN (ROLE ADMIN KOPERASI) -->
        <div v-if="isTindakLanjutModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="relative w-full max-w-lg rounded-2xl bg-slate-900 border border-slate-800 shadow-2xl p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <Edit3 class="w-4 h-4 text-amber-400" />
                        Update Progress Action Plan Koperasi
                    </h3>
                    <button @click="isTindakLanjutModalOpen = false" class="p-1 rounded text-slate-400 hover:text-white">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <div class="text-xs text-slate-300 space-y-1 bg-slate-950 p-3 rounded-xl border border-slate-800">
                    <div><strong>Koperasi:</strong> {{ activeTemuan?.koperasi?.nama_koperasi }}</div>
                    <div><strong>Temuan:</strong> {{ activeTemuan?.deskripsi_temuan }}</div>
                </div>

                <form @submit.prevent="submitTindakLanjut" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Status Progress *</label>
                        <select 
                            v-model="tindakLanjutForm.status_tindak_lanjut"
                            class="w-full rounded-xl bg-slate-800 border border-slate-700 text-white text-xs px-3.5 py-2.5"
                        >
                            <option value="Dalam Proses">Dalam Proses (Sedang Ditindaklanjuti)</option>
                            <option value="Selesai">Selesai (Siap Diverifikasi Pengawas)</option>
                            <option value="Belum Ditindaklanjuti">Belum Ditindaklanjuti</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Tanggapan & Penjelasan Action Plan Pengurus *</label>
                        <textarea 
                            v-model="tindakLanjutForm.tanggapan_koperasi"
                            rows="4"
                            required
                            placeholder="Jelaskan langkah nyata yang telah dilakukan koperasi (misal: SOP telah disahkan pada tgl...)"
                            class="w-full rounded-xl bg-slate-800 border border-slate-700 text-white text-xs px-3.5 py-2.5"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="isTindakLanjutModalOpen = false" class="px-4 py-2 text-xs text-slate-400">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-xs font-semibold">Simpan Action Plan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL 2: VERIFIKASI PENGAWAS (ROLE BIDANG PENGAWASAN) -->
        <div v-if="isVerifikasiModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="relative w-full max-w-lg rounded-2xl bg-slate-900 border border-slate-800 shadow-2xl p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <ShieldCheck class="w-4 h-4 text-emerald-400" />
                        Verifikasi & Approval Pengawas Diskop Provsu
                    </h3>
                    <button @click="isVerifikasiModalOpen = false" class="p-1 rounded text-slate-400 hover:text-white">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <div class="text-xs text-slate-300 space-y-1 bg-slate-950 p-3 rounded-xl border border-slate-800">
                    <div><strong>Koperasi:</strong> {{ activeTemuan?.koperasi?.nama_koperasi }}</div>
                    <div><strong>Action Plan Pengurus:</strong> {{ activeTemuan?.tanggapan_koperasi || '-' }}</div>
                </div>

                <form @submit.prevent="submitVerifikasi" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Keputusan Status Verifikasi *</label>
                        <select 
                            v-model="verifikasiForm.status_tindak_lanjut"
                            class="w-full rounded-xl bg-slate-800 border border-slate-700 text-white text-xs px-3.5 py-2.5"
                        >
                            <option value="Selesai">Disetujui / Selesai (Sesuai Rekomendasi)</option>
                            <option value="Dalam Proses">Perlu Perbaikan (Kembalikan ke Koperasi)</option>
                            <option value="Belum Ditindaklanjuti">Ditolak / Belum Ditindaklanjuti</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Catatan Hasil Verifikasi Pengawas *</label>
                        <textarea 
                            v-model="verifikasiForm.catatan_verifikasi_pengawas"
                            rows="4"
                            required
                            placeholder="Catatan tim pengawas mengenai kesesuaian dokumen/bukti tindak lanjut..."
                            class="w-full rounded-xl bg-slate-800 border border-slate-700 text-white text-xs px-3.5 py-2.5"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="isVerifikasiModalOpen = false" class="px-4 py-2 text-xs text-slate-400">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold">Simpan Verifikasi</button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
