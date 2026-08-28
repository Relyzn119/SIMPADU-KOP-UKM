<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import {
    FileSpreadsheet,
    Calendar,
    Search,
    Plus,
    Clock,
    FileText,
    CheckCircle2,
    AlertCircle,
    X,
    Upload,
    RotateCcw,
    Building2,
    Trash2,
    Edit3
} from 'lucide-vue-next';

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
    };
    kabupatenKotaList: string[];
}>();

const page = usePage();
const userRole = (page.props.auth as any)?.user?.role || 'admin_koperasi';

const tahunBuku = ref(props.filters.tahun_buku || 2024);
const search = ref(props.filters.search || '');
const statusRat = ref(props.filters.status_rat || '');
const kabupatenKota = ref(props.filters.kabupaten_kota || '');

const isModalOpen = ref(false);
const ratToEdit = ref<any | null>(null);

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
        },
        { preserveState: true, replace: true }
    );
};

const resetFilters = () => {
    search.value = '';
    statusRat.value = '';
    kabupatenKota.value = '';
    applyFilters();
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
            }
        });
    } else {
        form.post('/rat', {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            }
        });
    }
};

const handleDelete = (item: any) => {
    if (confirm(`Apakah Anda yakin ingin menghapus laporan RAT Koperasi "${item.koperasi?.nama_koperasi}"?`)) {
        router.delete(`/rat/${item.id}`);
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Monitoring & Pelaporan RAT Koperasi" />

        <div class="space-y-6">
            <!-- DEADLINE BANNER (UU NO. 25/1992) -->
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-teal-950 via-slate-900 to-slate-900 border border-teal-800/40 p-6 shadow-2xl">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 border border-teal-500/30 text-teal-300 text-xs font-semibold mb-2">
                            <Clock class="w-3.5 h-3.5" />
                            Amanat Undang-Undang No. 25 Tahun 1992
                        </div>
                        <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">
                            Monitoring & Pelaporan RAT (Tahun Buku {{ tahunBuku }})
                        </h1>
                        <p class="text-xs text-slate-400 mt-1 max-w-2xl">
                            Pelaksanaan RAT paling lambat diselenggarakan 6 (enam) bulan setelah tutup tahun buku (Batas Waktu: <span class="text-teal-400 font-bold">30 Juni {{ tahunBuku + 1 }}</span>).
                        </p>
                    </div>

                    <button 
                        @click="openCreateModal"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-semibold text-xs shadow-lg shadow-teal-600/30 transition self-start sm:self-auto"
                    >
                        <Plus class="w-4 h-4" />
                        Input RAT Koperasi
                    </button>
                </div>
            </div>

            <!-- KPI SUMMARY COUNTER CARDS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl">
                    <div class="text-xs font-semibold text-slate-400 uppercase">Kepatuhan RAT</div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-3xl font-extrabold text-white">{{ summary.kepatuhan_pct }}%</span>
                        <span class="text-xs font-semibold text-teal-400">Target Diskop</span>
                    </div>
                    <div class="w-full bg-slate-800 rounded-full h-1.5 mt-3 overflow-hidden">
                        <div class="bg-teal-500 h-full rounded-full" :style="{ width: `${summary.kepatuhan_pct}%` }"></div>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl">
                    <div class="text-xs font-semibold text-emerald-400 uppercase">RAT Tepat Waktu</div>
                    <div class="mt-2 text-3xl font-extrabold text-white">{{ summary.rat_tepat_waktu }}</div>
                    <p class="text-[11px] text-slate-500 mt-1">Sebelum 30 Juni {{ tahunBuku + 1 }}</p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl">
                    <div class="text-xs font-semibold text-amber-400 uppercase">RAT Terlambat</div>
                    <div class="mt-2 text-3xl font-extrabold text-white">{{ summary.rat_terlambat }}</div>
                    <p class="text-[11px] text-slate-500 mt-1">Setelah 30 Juni {{ tahunBuku + 1 }}</p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl">
                    <div class="text-xs font-semibold text-rose-400 uppercase">Belum Melaporkan</div>
                    <div class="mt-2 text-3xl font-extrabold text-white">{{ summary.belum_rat }}</div>
                    <p class="text-[11px] text-slate-500 mt-1">Perlu Surat Teguran</p>
                </div>
            </div>

            <!-- SEARCH & FILTER BAR -->
            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    <!-- Select Tahun Buku -->
                    <div>
                        <select 
                            v-model="tahunBuku"
                            @change="applyFilters"
                            class="w-full py-2.5 px-3 rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs font-bold focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition"
                        >
                            <option :value="2025">Tahun Buku 2025</option>
                            <option :value="2024">Tahun Buku 2024</option>
                            <option :value="2023">Tahun Buku 2023</option>
                        </select>
                    </div>

                    <!-- Search Input -->
                    <div class="relative lg:col-span-2">
                        <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" />
                        <input 
                            v-model="search"
                            @input="applyFilters"
                            type="text"
                            placeholder="Cari Koperasi / No. BH..."
                            class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs placeholder:text-slate-500 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition"
                        />
                    </div>

                    <!-- Status RAT -->
                    <div>
                        <select 
                            v-model="statusRat"
                            @change="applyFilters"
                            class="w-full py-2.5 px-3 rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition"
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
                            class="w-full py-2.5 px-3 rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition"
                        >
                            <option value="">Semua Kab/Kota</option>
                            <option v-for="kab in kabupatenKotaList" :key="kab" :value="kab">{{ kab }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- TABLE CONTAINER -->
            <div class="rounded-2xl bg-slate-900 border border-slate-800 shadow-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-300">
                        <thead class="bg-slate-950/60 text-slate-400 uppercase tracking-wider text-[11px] border-b border-slate-800">
                            <tr>
                                <th class="py-3.5 px-4 font-bold">Koperasi & BH</th>
                                <th class="py-3.5 px-4 font-bold text-center">Tgl RAT</th>
                                <th class="py-3.5 px-4 font-bold">Tempat Pelaksanaan</th>
                                <th class="py-3.5 px-4 font-bold text-center">Hadir & Kuorum</th>
                                <th class="py-3.5 px-4 font-bold text-center">Status Kepatuhan</th>
                                <th class="py-3.5 px-4 font-bold text-center">Berkas LPJ</th>
                                <th class="py-3.5 px-4 font-bold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            <tr 
                                v-for="item in rats.data" 
                                :key="item.id"
                                class="hover:bg-slate-800/40 transition group"
                            >
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-100 group-hover:text-teal-400 transition">
                                        {{ item.koperasi?.nama_koperasi }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 flex items-center gap-2 mt-0.5">
                                        <span>{{ item.koperasi?.kabupaten_kota }}</span>
                                        <span>•</span>
                                        <span class="font-mono text-slate-500">{{ item.koperasi?.no_badan_hukum }}</span>
                                    </div>
                                </td>

                                <td class="py-3.5 px-4 text-center font-mono text-slate-200">
                                    {{ item.tanggal_rat }}
                                </td>

                                <td class="py-3.5 px-4 text-slate-300">
                                    {{ item.tempat_pelaksanaan }}
                                </td>

                                <td class="py-3.5 px-4 text-center">
                                    <div class="font-bold text-white">{{ item.jumlah_anggota_hadir }} Orang</div>
                                    <span 
                                        class="text-[10px] font-semibold"
                                        :class="item.kuorum ? 'text-emerald-400' : 'text-rose-400'"
                                    >
                                        {{ item.kuorum ? 'Kuorum Mencecah' : 'Tidak Kuorum' }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4 text-center">
                                    <span 
                                        class="px-2.5 py-1 rounded-full text-[10px] font-semibold border"
                                        :class="item.status_rat === 'Sudah RAT Tepat Waktu' 
                                            ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' 
                                            : 'bg-amber-500/10 text-amber-400 border-amber-500/30'"
                                    >
                                        {{ item.status_rat }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4 text-center">
                                    <a 
                                        v-if="item.file_lpj_path"
                                        :href="`/${item.file_lpj_path}`"
                                        target="_blank"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-teal-500/10 text-teal-300 border border-teal-500/30 hover:bg-teal-500/20 text-[11px] font-medium transition"
                                    >
                                        <FileText class="w-3.5 h-3.5" />
                                        Unduh LPJ
                                    </a>
                                    <span v-else class="text-[11px] text-slate-500 italic">Belum Ada LPJ</span>
                                </td>

                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button 
                                            @click="openEditModal(item)"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition"
                                            title="Edit Data RAT"
                                        >
                                            <Edit3 class="w-4 h-4 text-cyan-400" />
                                        </button>

                                        <button 
                                            v-if="userRole === 'admin_koperasi'"
                                            @click="handleDelete(item)"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-slate-800 transition"
                                            title="Hapus Data RAT"
                                        >
                                            <Trash2 class="w-4 h-4 text-rose-400" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- FORM MODAL INPUT RAT -->
        <div 
            v-if="isModalOpen" 
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 sm:p-6 animate-in fade-in duration-200"
        >
            <div class="relative w-full max-w-xl rounded-2xl bg-slate-900 border border-slate-800 shadow-2xl overflow-hidden my-8">
                <div class="px-6 py-4 bg-slate-900 border-b border-slate-800 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-teal-500/10 text-teal-400 flex items-center justify-center">
                            <FileSpreadsheet class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-white">
                                {{ ratToEdit ? 'Edit Pelaksanaan RAT' : 'Input Pelaksanaan RAT Baru' }}
                            </h3>
                            <p class="text-xs text-slate-400">Verifikasi Batas Waktu 30 Juni</p>
                        </div>
                    </div>

                    <button @click="isModalOpen = false" class="p-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Pilih Koperasi *</label>
                        <select 
                            v-model="form.koperasi_id"
                            :disabled="!!ratToEdit"
                            required
                            class="w-full rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs px-3.5 py-2.5 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition disabled:opacity-60"
                        >
                            <option v-for="k in koperasis" :key="k.id" :value="k.id">
                                {{ k.nama_koperasi }} ({{ k.kabupaten_kota }})
                            </option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Tahun Buku *</label>
                            <input 
                                v-model="form.tahun_buku"
                                type="number"
                                required
                                class="w-full rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs px-3.5 py-2.5 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Tanggal Pelaksanaan RAT *</label>
                            <input 
                                v-model="form.tanggal_rat"
                                type="date"
                                required
                                class="w-full rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs px-3.5 py-2.5 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Tempat Pelaksanaan *</label>
                        <input 
                            v-model="form.tempat_pelaksanaan"
                            type="text"
                            required
                            placeholder="Aula Kantor Koperasi / Gedung Pertemuan..."
                            class="w-full rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs px-3.5 py-2.5 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Jumlah Anggota Hadir *</label>
                            <input 
                                v-model="form.jumlah_anggota_hadir"
                                type="number"
                                required
                                class="w-full rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs px-3.5 py-2.5 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Status Kuorum *</label>
                            <select 
                                v-model="form.kuorum"
                                class="w-full rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs px-3.5 py-2.5 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition"
                            >
                                <option :value="true">Memenuhi Kuorum</option>
                                <option :value="false">Tidak Kuorum</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Upload Berkas LPJ / Berita Acara (PDF/DOC)</label>
                        <input 
                            @change="handleFileChange"
                            type="file"
                            accept=".pdf,.doc,.docx"
                            class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-teal-400 hover:file:bg-slate-700 transition"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Catatan Pelaksanaan (Opsional)</label>
                        <textarea 
                            v-model="form.catatan"
                            rows="2"
                            placeholder="Catatan persetujuan pertanggungjawaban pengurus..."
                            class="w-full rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs px-3.5 py-2.5 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition"
                        ></textarea>
                    </div>

                    <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
                        <button 
                            type="button" 
                            @click="isModalOpen = false"
                            class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:bg-slate-800 transition"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit"
                            :disabled="form.processing"
                            class="px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-semibold text-xs shadow-lg shadow-teal-600/30 transition disabled:opacity-50"
                        >
                            Simpan Data RAT
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
