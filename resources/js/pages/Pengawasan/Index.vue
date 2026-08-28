<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import {
    ShieldCheck,
    Search,
    Plus,
    Eye,
    Trash2,
    FileText,
    Award,
    RotateCcw,
    AlertTriangle
} from 'lucide-vue-next';

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
        predikat_kesehatan?: string;
        kabupaten_kota?: string;
    };
    kabupatenKotaList: string[];
}>();

const page = usePage();
const userRole = (page.props.auth as any)?.user?.role || 'admin_koperasi';

const search = ref(props.filters.search || '');
const predikatKesehatan = ref(props.filters.predikat_kesehatan || '');
const kabupatenKota = ref(props.filters.kabupaten_kota || '');

const applyFilters = () => {
    router.get(
        '/pengawasan',
        {
            search: search.value || undefined,
            predikat_kesehatan: predikatKesehatan.value || undefined,
            kabupaten_kota: kabupatenKota.value || undefined,
        },
        { preserveState: true, replace: true }
    );
};

const resetFilters = () => {
    search.value = '';
    predikatKesehatan.value = '';
    kabupatenKota.value = '';
    applyFilters();
};

const handleDelete = (item: any) => {
    if (confirm(`Apakah Anda yakin ingin menghapus data pemeriksaan "${item.no_surat_tugas}"?`)) {
        router.delete(`/pengawasan/${item.id}`);
    }
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
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Pemeriksaan & Penilaian Kesehatan Koperasi" />

        <div class="space-y-6">
            <!-- PAGE TITLE BANNER -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight flex items-center gap-2.5">
                        <ShieldCheck class="w-6 h-6 text-indigo-600" />
                        Pemeriksaan & Penilaian Kesehatan Koperasi
                    </h1>
                    <p class="text-xs text-gray-500 mt-1">
                        Instrumen Pengawasan 4 Aspek (Tata Kelola, Profil Risiko, Kinerja Keuangan, Permodalan) Permenkop No. 9/2020.
                    </p>
                </div>

                <Link 
                    href="/pengawasan/create"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-xs transition self-start sm:self-auto"
                >
                    <Plus class="w-4 h-4 text-emerald-400" />
                    Input Pengawasan Baru
                </Link>
            </div>

            <!-- SEARCH & FILTER BAR CARD -->
            <div class="p-5 rounded-3xl bg-white border border-gray-200/70 shadow-2xs space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <!-- Search Input -->
                    <div class="relative lg:col-span-2">
                        <Search class="w-4 h-4 text-gray-400 absolute left-3.5 top-3" />
                        <input 
                            v-model="search"
                            @input="applyFilters"
                            type="text"
                            placeholder="Cari No. Surat Tugas / Nama Koperasi..."
                            class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs placeholder:text-gray-400 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition"
                        />
                    </div>

                    <!-- Filter Predikat -->
                    <div>
                        <select 
                            v-model="predikatKesehatan"
                            @change="applyFilters"
                            class="w-full py-2.5 px-3 rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition"
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
                            class="w-full py-2.5 px-3 rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition"
                        >
                            <option value="">Semua Kab/Kota</option>
                            <option v-for="kab in kabupatenKotaList" :key="kab" :value="kab">{{ kab }}</option>
                        </select>
                    </div>
                </div>

                <!-- Reset Filters -->
                <div class="flex items-center justify-between pt-3 border-t border-gray-100 text-xs">
                    <span class="text-gray-500 font-medium">Menampilkan <span class="font-bold text-gray-900">{{ pengawasans.from || 0 }} - {{ pengawasans.to || 0 }}</span> dari <span class="font-bold text-gray-900">{{ pengawasans.total }}</span> Laporan Pemeriksaan</span>
                    <button 
                        @click="resetFilters"
                        class="text-xs text-gray-500 hover:text-emerald-600 font-semibold flex items-center gap-1.5 transition"
                    >
                        <RotateCcw class="w-3.5 h-3.5" />
                        Reset Filter
                    </button>
                </div>
            </div>

            <!-- TABLE CONTAINER CARD -->
            <div class="rounded-3xl bg-white border border-gray-200/70 shadow-2xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50/80 text-gray-500 uppercase tracking-wider text-[10px] font-bold border-b border-gray-100">
                            <tr>
                                <th class="py-3.5 px-4 font-bold">Koperasi & Surat Tugas</th>
                                <th class="py-3.5 px-4 font-bold text-center">Tgl Pemeriksaan</th>
                                <th class="py-3.5 px-4 font-bold text-center">Skor 4 Aspek</th>
                                <th class="py-3.5 px-4 font-bold text-center">Skor Total & Predikat</th>
                                <th class="py-3.5 px-4 font-bold text-center">Jumlah Temuan</th>
                                <th class="py-3.5 px-4 font-bold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr 
                                v-for="item in pengawasans.data" 
                                :key="item.id"
                                class="hover:bg-gray-50/60 transition group"
                            >
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-gray-900 group-hover:text-indigo-600 transition">
                                        {{ item.koperasi?.nama_koperasi }}
                                    </div>
                                    <div class="text-[11px] text-gray-500 font-medium flex items-center gap-2 mt-0.5">
                                        <span class="font-mono text-indigo-600 font-semibold">{{ item.no_surat_tugas }}</span>
                                        <span>•</span>
                                        <span>{{ item.koperasi?.kabupaten_kota }}</span>
                                    </div>
                                </td>

                                <td class="py-3.5 px-4 text-center font-mono font-medium text-gray-700">
                                    {{ item.tanggal_pemeriksaan }}
                                </td>

                                <td class="py-3.5 px-4 text-center">
                                    <div class="text-[10px] text-gray-500 space-x-1.5 font-mono">
                                        <span>TK: <strong class="text-gray-900">{{ item.skor_tata_kelola }}</strong></span>
                                        <span>PR: <strong class="text-gray-900">{{ item.skor_profil_risiko }}</strong></span>
                                        <span>KK: <strong class="text-gray-900">{{ item.skor_kinerja_keuangan }}</strong></span>
                                        <span>PM: <strong class="text-gray-900">{{ item.skor_permodalan }}</strong></span>
                                    </div>
                                </td>

                                <td class="py-3.5 px-4 text-center">
                                    <div class="inline-flex flex-col items-center">
                                        <span 
                                            class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-bold border"
                                            :class="getPredikatBadge(item.predikat_kesehatan)"
                                        >
                                            {{ item.predikat_kesehatan }}
                                        </span>
                                        <span class="text-[10px] text-gray-400 font-mono mt-0.5">Skor: {{ item.skor_total }}</span>
                                    </div>
                                </td>

                                <td class="py-3.5 px-4 text-center">
                                    <span 
                                        v-if="item.temuans && item.temuans.length > 0"
                                        class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200"
                                    >
                                        {{ item.temuans.length }} Temuan
                                    </span>
                                    <span v-else class="text-[11px] text-gray-400 italic">Nihil Temuan</span>
                                </td>

                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <Link 
                                            :href="`/pengawasan/${item.id}`"
                                            class="p-1.5 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition"
                                            title="Lihat Detail Berita Acara"
                                        >
                                            <Eye class="w-4 h-4" />
                                        </Link>

                                        <button 
                                            v-if="userRole === 'bidang_pengawasan' || userRole === 'admin_koperasi'"
                                            @click="handleDelete(item)"
                                            class="p-1.5 rounded-lg text-gray-400 hover:text-rose-600 hover:bg-rose-50 transition"
                                            title="Hapus Pengawasan"
                                        >
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Footer -->
                <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex items-center justify-between">
                    <div class="text-xs text-gray-500 font-medium">
                        Halaman <span class="font-bold text-gray-900">{{ pengawasans.current_page }}</span> dari <span class="font-bold text-gray-900">{{ pengawasans.last_page }}</span>
                    </div>

                    <div class="flex items-center gap-1">
                        <Component
                            v-for="(link, index) in pengawasans.links"
                            :key="index"
                            :is="link.url ? Link : 'span'"
                            :href="link.url"
                            v-html="link.label"
                            class="px-3 py-1.5 rounded-xl text-xs font-semibold transition"
                            :class="[
                                link.active ? 'bg-slate-900 text-white shadow-2xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100',
                                !link.url && 'opacity-40 cursor-not-allowed'
                            ]"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

