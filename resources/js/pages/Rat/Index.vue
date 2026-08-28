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
            <!-- DEADLINE BANNER CARD (UU NO. 25/1992) -->
            <div class="rounded-3xl bg-white border border-gray-200/70 p-6 shadow-2xs">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-50 border border-teal-200 text-teal-700 text-xs font-bold mb-2">
                            <Clock class="w-3.5 h-3.5" />
                            Amanat Undang-Undang No. 25 Tahun 1992
                        </div>
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">
                            Monitoring & Pelaporan RAT (Tahun Buku {{ tahunBuku }})
                        </h1>
                        <p class="text-xs text-gray-500 mt-1 max-w-2xl">
                            Pelaksanaan RAT paling lambat diselenggarakan 6 (enam) bulan setelah tutup tahun buku (Batas Waktu: <span class="text-teal-700 font-bold">30 Juni {{ tahunBuku + 1 }}</span>).
                        </p>
                    </div>

                    <button 
                        @click="openCreateModal"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-xs transition self-start sm:self-auto"
                    >
                        <Plus class="w-4 h-4 text-emerald-400" />
                        Input RAT Koperasi
                    </button>
                </div>
            </div>

            <!-- KPI SUMMARY COUNTER CARDS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-5 rounded-3xl bg-white border border-gray-200/70 shadow-2xs">
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Kepatuhan RAT</div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-3xl font-extrabold text-gray-900 font-mono">{{ summary.kepatuhan_pct }}%</span>
                        <span class="text-xs font-bold text-teal-700">Target Diskop</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5 mt-3 overflow-hidden">
                        <div class="bg-teal-600 h-full rounded-full" :style="{ width: `${summary.kepatuhan_pct}%` }"></div>
                    </div>
                </div>

                <div class="p-5 rounded-3xl bg-white border border-gray-200/70 shadow-2xs">
                    <div class="text-xs font-bold text-emerald-700 uppercase tracking-wider">RAT Tepat Waktu</div>
                    <div class="mt-2 text-3xl font-extrabold text-gray-900 font-mono">{{ summary.rat_tepat_waktu }}</div>
                    <p class="text-[11px] text-gray-400 font-medium mt-1">Sebelum 30 Juni {{ tahunBuku + 1 }}</p>
                </div>

                <div class="p-5 rounded-3xl bg-white border border-gray-200/70 shadow-2xs">
                    <div class="text-xs font-bold text-amber-700 uppercase tracking-wider">RAT Terlambat</div>
                    <div class="mt-2 text-3xl font-extrabold text-gray-900 font-mono">{{ summary.rat_terlambat }}</div>
                    <p class="text-[11px] text-gray-400 font-medium mt-1">Setelah 30 Juni {{ tahunBuku + 1 }}</p>
                </div>

                <div class="p-5 rounded-3xl bg-white border border-gray-200/70 shadow-2xs">
                    <div class="text-xs font-bold text-rose-700 uppercase tracking-wider">Belum Melaporkan</div>
                    <div class="mt-2 text-3xl font-extrabold text-gray-900 font-mono">{{ summary.belum_rat }}</div>
                    <p class="text-[11px] text-gray-400 font-medium mt-1">Perlu Surat Teguran</p>
                </div>
            </div>

            <!-- SEARCH & FILTER BAR CARD -->
            <div class="p-5 rounded-3xl bg-white border border-gray-200/70 shadow-2xs space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    <!-- Select Tahun Buku -->
                    <div>
                        <select 
                            v-model="tahunBuku"
                            @change="applyFilters"
                            class="w-full py-2.5 px-3 rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs font-bold focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition"
                        >
                            <option :value="2025">Tahun Buku 2025</option>
                            <option :value="2024">Tahun Buku 2024</option>
                            <option :value="2023">Tahun Buku 2023</option>
                        </select>
                    </div>

                    <!-- Search Input -->
                    <div class="relative lg:col-span-2">
                        <Search class="w-4 h-4 text-gray-400 absolute left-3.5 top-3" />
                        <input 
                            v-model="search"
                            @input="applyFilters"
                            type="text"
                            placeholder="Cari Koperasi / No. BH..."
                            class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs placeholder:text-gray-400 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition"
                        />
                    </div>

                    <!-- Status RAT -->
                    <div>
                        <select 
                            v-model="statusRat"
                            @change="applyFilters"
                            class="w-full py-2.5 px-3 rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition"
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
                            class="w-full py-2.5 px-3 rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition"
                        >
                            <option value="">Semua Kab/Kota</option>
                            <option v-for="kab in kabupatenKotaList" :key="kab" :value="kab">{{ kab }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- TABLE CONTAINER CARD -->
            <div class="rounded-3xl bg-white border border-gray-200/70 shadow-2xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50/80 text-gray-500 uppercase tracking-wider text-[10px] font-bold border-b border-gray-100">
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
                        <tbody class="divide-y divide-gray-100">
                            <tr 
                                v-for="item in rats.data" 
                                :key="item.id"
                                class="hover:bg-gray-50/60 transition group"
                            >
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-gray-900 group-hover:text-teal-600 transition">
                                        {{ item.koperasi?.nama_koperasi }}
                                    </div>
                                    <div class="text-[11px] text-gray-500 font-medium flex items-center gap-2 mt-0.5">
                                        <span>{{ item.koperasi?.kabupaten_kota }}</span>
                                        <span>•</span>
                                        <span class="font-mono text-gray-400">{{ item.koperasi?.no_badan_hukum }}</span>
                                    </div>
                                </td>

                                <td class="py-3.5 px-4 text-center font-mono font-medium text-gray-700">
                                    {{ item.tanggal_rat }}
                                </td>

                                <td class="py-3.5 px-4 text-gray-700 font-medium">
                                    {{ item.tempat_pelaksanaan }}
                                </td>

                                <td class="py-3.5 px-4 text-center">
                                    <div class="font-bold text-gray-900">{{ item.jumlah_anggota_hadir }} Orang</div>
                                    <span 
                                        class="text-[10px] font-bold"
                                        :class="item.kuorum ? 'text-emerald-600' : 'text-rose-600'"
                                    >
                                        {{ item.kuorum ? 'Kuorum Memenuhi' : 'Tidak Kuorum' }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4 text-center">
                                    <span 
                                        class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-bold border"
                                        :class="item.status_rat === 'Sudah RAT Tepat Waktu' 
                                            ? 'bg-emerald-50 text-emerald-700 border-emerald-200' 
                                            : 'bg-amber-50 text-amber-700 border-amber-200'"
                                    >
                                        {{ item.status_rat }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4 text-center">
                                    <a 
                                        v-if="item.file_lpj_path"
                                        :href="`/${item.file_lpj_path}`"
                                        target="_blank"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-teal-50 text-teal-700 border border-teal-200 text-xs font-bold hover:bg-teal-100 transition"
                                    >
                                        <FileText class="w-3.5 h-3.5" />
                                        Unduh LPJ
                                    </a>
                                    <span v-else class="text-[11px] text-gray-400 italic">Belum Ada LPJ</span>
                                </td>

                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <button 
                                            @click="openEditModal(item)"
                                            class="p-1.5 rounded-lg text-gray-400 hover:text-cyan-600 hover:bg-cyan-50 transition"
                                            title="Edit Data RAT"
                                        >
                                            <Edit3 class="w-4 h-4" />
                                        </button>

                                        <button 
                                            v-if="userRole === 'admin_koperasi'"
                                            @click="handleDelete(item)"
                                            class="p-1.5 rounded-lg text-gray-400 hover:text-rose-600 hover:bg-rose-50 transition"
                                            title="Hapus Data RAT"
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
                        Halaman <span class="font-bold text-gray-900">{{ rats.current_page }}</span> dari <span class="font-bold text-gray-900">{{ rats.last_page }}</span>
                    </div>

                    <div class="flex items-center gap-1">
                        <Component
                            v-for="(link, index) in rats.links"
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

        <!-- FORM MODAL INPUT RAT DIALOG -->
        <div 
            v-if="isModalOpen" 
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 sm:p-6 animate-in fade-in duration-200"
        >
            <div class="relative w-full max-w-xl rounded-3xl bg-white border border-gray-200/80 shadow-2xl overflow-hidden my-8">
                <div class="px-6 py-4 bg-white border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center">
                            <FileSpreadsheet class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">
                                {{ ratToEdit ? 'Edit Pelaksanaan RAT' : 'Input Pelaksanaan RAT Baru' }}
                            </h3>
                            <p class="text-xs text-gray-500">Verifikasi Batas Waktu 30 Juni</p>
                        </div>
                    </div>

                    <button @click="isModalOpen = false" class="p-1.5 rounded-xl text-gray-400 hover:text-gray-900 hover:bg-gray-100 transition">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Pilih Koperasi *</label>
                        <select 
                            v-model="form.koperasi_id"
                            :disabled="!!ratToEdit"
                            required
                            class="w-full rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm px-3.5 py-2.5 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150 disabled:opacity-60"
                        >
                            <option v-for="k in koperasis" :key="k.id" :value="k.id">
                                {{ k.nama_koperasi }} ({{ k.kabupaten_kota }})
                            </option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tahun Buku *</label>
                            <input 
                                v-model="form.tahun_buku"
                                type="number"
                                required
                                class="w-full rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm px-3.5 py-2.5 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tanggal Pelaksanaan RAT *</label>
                            <input 
                                v-model="form.tanggal_rat"
                                type="date"
                                required
                                class="w-full rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm px-3.5 py-2.5 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tempat Pelaksanaan *</label>
                        <input 
                            v-model="form.tempat_pelaksanaan"
                            type="text"
                            required
                            placeholder="Aula Kantor Koperasi / Gedung Pertemuan..."
                            class="w-full rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm px-3.5 py-2.5 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Jumlah Anggota Hadir *</label>
                            <input 
                                v-model="form.jumlah_anggota_hadir"
                                type="number"
                                required
                                class="w-full rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm px-3.5 py-2.5 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Status Kuorum *</label>
                            <select 
                                v-model="form.kuorum"
                                class="w-full rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm px-3.5 py-2.5 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150"
                            >
                                <option :value="true">Memenuhi Kuorum</option>
                                <option :value="false">Tidak Kuorum</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Upload Berkas LPJ / Berita Acara (PDF/DOC)</label>
                        <input 
                            @change="handleFileChange"
                            type="file"
                            accept=".pdf,.doc,.docx"
                            class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-900 file:text-white hover:file:bg-slate-800 transition"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Catatan Pelaksanaan (Opsional)</label>
                        <textarea 
                            v-model="form.catatan"
                            rows="2"
                            placeholder="Catatan persetujuan pertanggungjawaban pengurus..."
                            class="w-full rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm px-3.5 py-2.5 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150"
                        ></textarea>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                        <button 
                            type="button" 
                            @click="isModalOpen = false"
                            class="px-4 py-2.5 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-100 transition"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit"
                            :disabled="form.processing"
                            class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md shadow-slate-900/10 transition disabled:opacity-50"
                        >
                            Simpan Data RAT
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

