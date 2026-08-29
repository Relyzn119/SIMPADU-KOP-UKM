<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Eye, Plus, RotateCcw, Search, ShieldCheck, Trash2 } from 'lucide-vue-next';
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
        { preserveState: true, replace: true },
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
                        Instrumen Pengawasan 4 Aspek (Tata Kelola, Profil Risiko, Kinerja Keuangan, Permodalan) Permenkop No. 9/2020.
                    </p>
                </div>

                <Link
                    href="/pengawasan/create"
                    class="shadow-xs flex items-center gap-2 self-start rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-slate-800 sm:self-auto"
                >
                    <Plus class="h-4 w-4 text-emerald-400" />
                    Input Pengawasan Baru
                </Link>
            </div>

            <!-- SEARCH & FILTER BAR CARD -->
            <div class="shadow-2xs space-y-4 rounded-3xl border border-gray-200/70 bg-white p-5">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
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
                                <th class="px-4 py-3.5 text-center font-bold">Tgl Pemeriksaan</th>
                                <th class="px-4 py-3.5 text-center font-bold">Skor 4 Aspek</th>
                                <th class="px-4 py-3.5 text-center font-bold">Skor Total & Predikat</th>
                                <th class="px-4 py-3.5 text-center font-bold">Jumlah Temuan</th>
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
                                </td>

                                <td class="px-4 py-3.5 text-center font-mono font-medium text-gray-700">
                                    {{ item.tanggal_pemeriksaan }}
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

                                <td class="px-4 py-3.5 text-center">
                                    <div class="inline-flex flex-col items-center">
                                        <span
                                            class="inline-flex rounded-full border px-2.5 py-1 text-[10px] font-bold"
                                            :class="getPredikatBadge(item.predikat_kesehatan)"
                                        >
                                            {{ item.predikat_kesehatan }}
                                        </span>
                                        <span class="mt-0.5 font-mono text-[10px] text-gray-400">Skor: {{ item.skor_total }}</span>
                                    </div>
                                </td>

                                <td class="px-4 py-3.5 text-center">
                                    <span
                                        v-if="item.temuans && item.temuans.length > 0"
                                        class="inline-flex rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-[10px] font-bold text-amber-700"
                                    >
                                        {{ item.temuans.length }} Temuan
                                    </span>
                                    <span v-else class="text-[11px] italic text-gray-400">Nihil Temuan</span>
                                </td>

                                <td class="px-4 py-3.5 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <Link
                                            :href="`/pengawasan/${item.id}`"
                                            class="rounded-lg p-1.5 text-gray-400 transition hover:bg-indigo-50 hover:text-indigo-600"
                                            title="Lihat Detail Berita Acara"
                                        >
                                            <Eye class="h-4 w-4" />
                                        </Link>

                                        <button
                                            v-if="userRole === 'bidang_pengawasan' || userRole === 'admin_koperasi'"
                                            @click="handleDelete(item)"
                                            class="rounded-lg p-1.5 text-gray-400 transition hover:bg-rose-50 hover:text-rose-600"
                                            title="Hapus Pengawasan"
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
    </AuthenticatedLayout>
</template>
