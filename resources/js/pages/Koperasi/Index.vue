<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import FormModal from './FormModal.vue';
import {
    Building2,
    Search,
    Plus,
    Filter,
    Eye,
    Edit3,
    Trash2,
    ChevronLeft,
    ChevronRight,
    ShieldCheck,
    CheckCircle2,
    XCircle,
    RotateCcw
} from 'lucide-vue-next';

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
        kabupaten_kota?: string;
        jenis_koperasi?: string;
        status_keaktifan?: string;
        predikat_kesehatan?: string;
    };
    kabupatenKotaList: string[];
}>();

const page = usePage();
const userRole = (page.props.auth as any)?.user?.role || 'admin_koperasi';

const search = ref(props.filters.search || '');
const kabupatenKota = ref(props.filters.kabupaten_kota || '');
const jenisKoperasi = ref(props.filters.jenis_koperasi || '');
const statusKeaktifan = ref(props.filters.status_keaktifan || '');
const predikatKesehatan = ref(props.filters.predikat_kesehatan || '');

const isModalOpen = ref(false);
const koperasiToEdit = ref<any | null>(null);

const applyFilters = () => {
    router.get(
        '/koperasi',
        {
            search: search.value || undefined,
            kabupaten_kota: kabupatenKota.value || undefined,
            jenis_koperasi: jenisKoperasi.value || undefined,
            status_keaktifan: statusKeaktifan.value || undefined,
            predikat_kesehatan: predikatKesehatan.value || undefined,
        },
        { preserveState: true, replace: true }
    );
};

const resetFilters = () => {
    search.value = '';
    kabupatenKota.value = '';
    jenisKoperasi.value = '';
    statusKeaktifan.value = '';
    predikatKesehatan.value = '';
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

const getPredikatBadge = (predikat: string) => {
    switch (predikat) {
        case 'Sehat': return 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40';
        case 'Cukup Sehat': return 'bg-cyan-500/20 text-cyan-300 border-cyan-500/40';
        case 'Dalam Pengawasan': return 'bg-amber-500/20 text-amber-300 border-amber-500/40';
        case 'Pengawasan Khusus': return 'bg-rose-500/20 text-rose-300 border-rose-500/40';
        default: return 'bg-slate-700 text-slate-400 border-slate-600';
    }
};

const formatRupiah = (val: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Master Data Koperasi Binaan" />

        <div class="space-y-6">
            <!-- PAGE TITLE & TOP ACTION -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight flex items-center gap-2.5">
                        <Building2 class="w-6 h-6 text-emerald-400" />
                        Data Master Koperasi Binaan
                    </h1>
                    <p class="text-xs text-slate-400 mt-1">
                        Kelola data kelembagaan, pengurus, serta ringkasan kesehatan 100 Koperasi se-Sumatera Utara.
                    </p>
                </div>

                <button 
                    v-if="userRole === 'admin_koperasi' || userRole === 'bidang_pengawasan'"
                    @click="openCreateModal"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-lg shadow-emerald-600/30 transition self-start sm:self-auto"
                >
                    <Plus class="w-4 h-4" />
                    Tambah Koperasi Baru
                </button>
            </div>

            <!-- SEARCH & FILTER BAR -->
            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    <!-- Search Input -->
                    <div class="relative lg:col-span-2">
                        <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" />
                        <input 
                            v-model="search"
                            @input="applyFilters"
                            type="text"
                            placeholder="Cari Nama Koperasi / No. Badan Hukum..."
                            class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs placeholder:text-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                        />
                    </div>

                    <!-- Filter Kabupaten/Kota -->
                    <div>
                        <select 
                            v-model="kabupatenKota"
                            @change="applyFilters"
                            class="w-full py-2.5 px-3 rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
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
                            class="w-full py-2.5 px-3 rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
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
                            class="w-full py-2.5 px-3 rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                        >
                            <option value="">Semua Predikat</option>
                            <option value="Sehat">Sehat</option>
                            <option value="Cukup Sehat">Cukup Sehat</option>
                            <option value="Dalam Pengawasan">Dalam Pengawasan</option>
                            <option value="Pengawasan Khusus">Pengawasan Khusus</option>
                        </select>
                    </div>
                </div>

                <!-- Reset Filters Button -->
                <div class="flex items-center justify-between pt-2 border-t border-slate-800/60 text-xs">
                    <span class="text-slate-400">Menampilkan {{ koperasis.from || 0 }} - {{ koperasis.to || 0 }} dari {{ koperasis.total }} Koperasi</span>
                    <button 
                        @click="resetFilters"
                        class="text-xs text-slate-400 hover:text-emerald-400 flex items-center gap-1 transition"
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
                                <th class="py-3.5 px-4 font-bold">Koperasi & No. BH</th>
                                <th class="py-3.5 px-4 font-bold">Jenis & Wilayah</th>
                                <th class="py-3.5 px-4 font-bold text-right">Total Aset</th>
                                <th class="py-3.5 px-4 font-bold text-center">Status RAT</th>
                                <th class="py-3.5 px-4 font-bold text-center">Predikat Kesehatan</th>
                                <th class="py-3.5 px-4 font-bold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            <tr 
                                v-for="item in koperasis.data" 
                                :key="item.id"
                                class="hover:bg-slate-800/40 transition group"
                            >
                                <!-- Name & Badan Hukum -->
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-100 group-hover:text-emerald-400 transition">
                                        {{ item.nama_koperasi }}
                                    </div>
                                    <div class="text-[11px] font-mono text-slate-500 mt-0.5">
                                        {{ item.no_badan_hukum }}
                                    </div>
                                </td>

                                <!-- Jenis & Kab/Kota -->
                                <td class="py-3.5 px-4">
                                    <div class="font-semibold text-slate-200">{{ item.jenis_koperasi }}</div>
                                    <div class="text-[11px] text-slate-400">{{ item.kabupaten_kota }}</div>
                                </td>

                                <!-- Aset -->
                                <td class="py-3.5 px-4 text-right font-mono font-semibold text-slate-200">
                                    {{ formatRupiah(item.aset) }}
                                </td>

                                <!-- Status RAT -->
                                <td class="py-3.5 px-4 text-center">
                                    <span 
                                        v-if="item.rats && item.rats.length > 0"
                                        class="px-2.5 py-1 rounded-full text-[10px] font-semibold border"
                                        :class="item.rats[0].status_rat === 'Sudah RAT Tepat Waktu' 
                                            ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' 
                                            : 'bg-amber-500/10 text-amber-400 border-amber-500/30'"
                                    >
                                        {{ item.rats[0].status_rat }}
                                    </span>
                                    <span 
                                        v-else 
                                        class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/30"
                                    >
                                        Belum RAT
                                    </span>
                                </td>

                                <!-- Predikat Kesehatan -->
                                <td class="py-3.5 px-4 text-center">
                                    <div 
                                        v-if="item.predikat_kesehatan"
                                        class="inline-flex flex-col items-center"
                                    >
                                        <span 
                                            class="px-2.5 py-1 rounded-full text-[10px] font-bold border"
                                            :class="getPredikatBadge(item.predikat_kesehatan)"
                                        >
                                            {{ item.predikat_kesehatan }}
                                        </span>
                                        <span class="text-[10px] text-slate-400 font-mono mt-0.5">Skor: {{ item.skor_kesehatan_terakhir }}</span>
                                    </div>
                                    <span v-else class="text-[11px] text-slate-500 italic">Belum Diperiksa</span>
                                </td>

                                <!-- Action Buttons -->
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <Link 
                                            :href="`/koperasi/${item.id}`"
                                            title="Lihat Detail Profile"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition"
                                        >
                                            <Eye class="w-4 h-4 text-emerald-400" />
                                        </Link>

                                        <button 
                                            @click="openEditModal(item)"
                                            title="Edit Data"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition"
                                        >
                                            <Edit3 class="w-4 h-4 text-cyan-400" />
                                        </button>

                                        <button 
                                            v-if="userRole === 'admin_koperasi'"
                                            @click="handleDelete(item)"
                                            title="Hapus Data"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-slate-800 transition"
                                        >
                                            <Trash2 class="w-4 h-4 text-rose-400" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Footer -->
                <div class="px-6 py-4 bg-slate-950/60 border-t border-slate-800 flex items-center justify-between">
                    <div class="text-xs text-slate-400">
                        Halaman <span class="font-bold text-white">{{ koperasis.current_page }}</span> dari <span class="font-bold text-white">{{ koperasis.last_page }}</span>
                    </div>

                    <div class="flex items-center gap-1">
                        <Component
                            v-for="(link, index) in koperasis.links"
                            :key="index"
                            :is="link.url ? Link : 'span'"
                            :href="link.url"
                            v-html="link.label"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition"
                            :class="[
                                link.active ? 'bg-emerald-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800',
                                !link.url && 'opacity-40 cursor-not-allowed'
                            ]"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Create & Edit Form Modal -->
        <FormModal 
            :is-open="isModalOpen"
            :koperasi-to-edit="koperasiToEdit"
            :kabupaten-kota-list="kabupatenKotaList"
            @close="isModalOpen = false"
        />
    </AuthenticatedLayout>
</template>
