<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { AlertCircle, ArrowLeft, Calculator, Plus, ShieldCheck, Trash2 } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
    koperasis: Array<{
        id: number;
        nama_koperasi: string;
        no_badan_hukum: string;
        kabupaten_kota: string;
    }>;
}>();

const form = useForm({
    koperasi_id: props.koperasis[0]?.id || '',
    no_surat_tugas: `ST-${Math.floor(100 + Math.random() * 900)}/DISKOP-SU/IX/2026`,
    tanggal_pemeriksaan: new Date().toISOString().split('T')[0],
    nama_tim_pengawas: 'Drs. Iskandar Muda, M.Si & Tim Pengawas Diskop Provsu',
    skor_tata_kelola: 80,
    skor_profil_risiko: 75,
    skor_kinerja_keuangan: 78,
    skor_permodalan: 82,
    kesimpulan_pengawasan:
        'Berdasarkan pemeriksaan lapangan 4 aspek, koperasi secara umum beroperasi dengan tata kelola dan permodalan yang cukup memadai.',
    file_berita_acara: null as File | null,

    // Dynamic Temuans List
    temuans: [
        {
            aspek_temuan: 'Operasional',
            deskripsi_temuan: 'Belum terdapat SOP tertulis mengenai manajemen risiko penyaluran pinjaman/pembiayaan.',
            rekomendasi: 'Pengurus wajib menyusun dan menyahkan SOP Manajemen Risiko serta menyampaikan salinan ke Dinas Koperasi Provsu.',
            batas_waktu: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
            tingkat_risiko: 'Sedang',
        },
    ],
});

// Real-time Automatic Score Calculator
const computedSkorTotal = computed(() => {
    const tk = Number(form.skor_tata_kelola) || 0;
    const pr = Number(form.skor_profil_risiko) || 0;
    const kk = Number(form.skor_kinerja_keuangan) || 0;
    const pm = Number(form.skor_permodalan) || 0;

    const total = tk * 0.3 + pr * 0.25 + kk * 0.3 + pm * 0.15;
    return Math.round(total * 100) / 100;
});

const computedPredikat = computed(() => {
    const score = computedSkorTotal.value;
    if (score >= 80.0) return 'Sehat';
    if (score >= 66.0) return 'Cukup Sehat';
    if (score >= 51.0) return 'Dalam Pengawasan';
    return 'Pengawasan Khusus';
});

const getPredikatColorClass = computed(() => {
    switch (computedPredikat.value) {
        case 'Sehat':
            return 'bg-emerald-50 text-emerald-700 border-emerald-200';
        case 'Cukup Sehat':
            return 'bg-cyan-50 text-cyan-700 border-cyan-200';
        case 'Dalam Pengawasan':
            return 'bg-amber-50 text-amber-700 border-amber-200';
        default:
            return 'bg-rose-50 text-rose-700 border-rose-200';
    }
});

const addTemuanRow = () => {
    form.temuans.push({
        aspek_temuan: 'Keuangan',
        deskripsi_temuan: '',
        rekomendasi: '',
        batas_waktu: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
        tingkat_risiko: 'Sedang',
    });
};

const removeTemuanRow = (index: number) => {
    form.temuans.splice(index, 1);
};

const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.file_berita_acara = target.files[0];
    }
};

const handleSubmit = () => {
    form.post('/pengawasan', {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Input Form Pemeriksaan & Penilaian Kesehatan Koperasi" />

        <div class="mx-auto max-w-5xl space-y-6">
            <!-- TOP BACK NAVIGATION -->
            <div>
                <Link href="/pengawasan" class="inline-flex items-center gap-2 text-xs font-semibold text-gray-500 transition hover:text-emerald-600">
                    <ArrowLeft class="h-4 w-4" />
                    Kembali ke Daftar Pengawasan
                </Link>
            </div>

            <!-- TITLE BANNER CARD -->
            <div class="shadow-2xs rounded-3xl border border-gray-200/70 bg-white p-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-50 font-bold text-indigo-600">
                        <ShieldCheck class="h-6 w-6" />
                    </div>
                    <div>
                        <h1 class="text-xl font-bold tracking-tight text-gray-900 sm:text-2xl">
                            Form Penilaian Kesehatan Koperasi (4 Aspek Permenkop)
                        </h1>
                        <p class="mt-0.5 text-xs text-gray-500">Kalkulasi skor & predikat kesehatan otomatis dihitung secara real-time.</p>
                    </div>
                </div>
            </div>

            <!-- FORM WRAPPER -->
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <!-- SECTION 1: SURAT TUGAS & KOPERASI -->
                <div class="shadow-2xs space-y-4 rounded-3xl border border-gray-200/70 bg-white p-6">
                    <div class="border-b border-gray-100 pb-3 text-xs font-bold uppercase tracking-wider text-indigo-700">
                        1. Informasi Surat Tugas & Objek Koperasi
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">Pilih Koperasi *</label>
                            <select
                                v-model="form.koperasi_id"
                                required
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition duration-150 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                            >
                                <option v-for="k in koperasis" :key="k.id" :value="k.id">
                                    {{ k.nama_koperasi }} ({{ k.kabupaten_kota }}) - {{ k.no_badan_hukum }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">Nomor Surat Tugas (STP) *</label>
                            <input
                                v-model="form.no_surat_tugas"
                                type="text"
                                required
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 font-mono text-xs text-gray-900 transition duration-150 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                            />
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700"
                                >Tanggal Pemeriksaan Lapangan *</label
                            >
                            <input
                                v-model="form.tanggal_pemeriksaan"
                                type="date"
                                required
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition duration-150 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                            />
                        </div>

                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700"
                                >Nama Tim Pengawas Diskop Provsu *</label
                            >
                            <input
                                v-model="form.nama_tim_pengawas"
                                type="text"
                                required
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition duration-150 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                            />
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: REAL-TIME 4 ASPECT HEALTH SCORING ENGINE -->
                <div class="shadow-2xs space-y-6 rounded-3xl border border-gray-200/70 bg-white p-6">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-emerald-700">
                            <Calculator class="h-4 w-4 text-emerald-600" />
                            2. Engine Perhitungan Skor Kesehatan (4 Aspek Permenkop)
                        </div>
                    </div>

                    <!-- Live Score & Predicate Result Gauge Card -->
                    <div class="flex flex-col items-center justify-between gap-4 rounded-2xl bg-slate-900 p-5 text-white shadow-md sm:flex-row">
                        <div>
                            <div class="text-xs font-bold uppercase tracking-wider text-slate-300">Kalkulasi Skor Total Real-time</div>
                            <div class="mt-1 font-mono text-3xl font-extrabold text-white">
                                {{ computedSkorTotal }} <span class="text-xs font-normal text-slate-400">/ 100</span>
                            </div>
                        </div>

                        <div class="text-right">
                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-300">Hasil Predikat Instan</div>
                            <div class="mt-1 inline-block rounded-full border px-4 py-1.5 text-xs font-extrabold" :class="getPredikatColorClass">
                                {{ computedPredikat }}
                            </div>
                        </div>
                    </div>

                    <!-- 4 Aspects Input Grid -->
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Aspek 1: Tata Kelola -->
                        <div class="space-y-2 rounded-2xl border border-gray-200/80 bg-gray-50 p-4">
                            <div class="flex justify-between text-xs font-bold text-gray-900">
                                <span>Tata Kelola (Bobot 30%)</span>
                                <span class="font-mono font-bold text-emerald-700">{{ form.skor_tata_kelola }}</span>
                            </div>
                            <input
                                v-model.number="form.skor_tata_kelola"
                                type="range"
                                min="0"
                                max="100"
                                step="0.5"
                                class="w-full cursor-pointer accent-emerald-600"
                            />
                            <div class="flex justify-between text-[10px] font-medium text-gray-500">
                                <span>Nilai 0 - 100</span>
                                <span>Kontribusi Skor: {{ (form.skor_tata_kelola * 0.3).toFixed(2) }}</span>
                            </div>
                        </div>

                        <!-- Aspek 2: Profil Risiko -->
                        <div class="space-y-2 rounded-2xl border border-gray-200/80 bg-gray-50 p-4">
                            <div class="flex justify-between text-xs font-bold text-gray-900">
                                <span>Profil Risiko (Bobot 25%)</span>
                                <span class="font-mono font-bold text-indigo-700">{{ form.skor_profil_risiko }}</span>
                            </div>
                            <input
                                v-model.number="form.skor_profil_risiko"
                                type="range"
                                min="0"
                                max="100"
                                step="0.5"
                                class="w-full cursor-pointer accent-indigo-600"
                            />
                            <div class="flex justify-between text-[10px] font-medium text-gray-500">
                                <span>Nilai 0 - 100</span>
                                <span>Kontribusi Skor: {{ (form.skor_profil_risiko * 0.25).toFixed(2) }}</span>
                            </div>
                        </div>

                        <!-- Aspek 3: Kinerja Keuangan -->
                        <div class="space-y-2 rounded-2xl border border-gray-200/80 bg-gray-50 p-4">
                            <div class="flex justify-between text-xs font-bold text-gray-900">
                                <span>Kinerja Keuangan (Bobot 30%)</span>
                                <span class="font-mono font-bold text-cyan-700">{{ form.skor_kinerja_keuangan }}</span>
                            </div>
                            <input
                                v-model.number="form.skor_kinerja_keuangan"
                                type="range"
                                min="0"
                                max="100"
                                step="0.5"
                                class="w-full cursor-pointer accent-cyan-600"
                            />
                            <div class="flex justify-between text-[10px] font-medium text-gray-500">
                                <span>Nilai 0 - 100</span>
                                <span>Kontribusi Skor: {{ (form.skor_kinerja_keuangan * 0.3).toFixed(2) }}</span>
                            </div>
                        </div>

                        <!-- Aspek 4: Permodalan -->
                        <div class="space-y-2 rounded-2xl border border-gray-200/80 bg-gray-50 p-4">
                            <div class="flex justify-between text-xs font-bold text-gray-900">
                                <span>Permodalan (Bobot 15%)</span>
                                <span class="font-mono font-bold text-teal-700">{{ form.skor_permodalan }}</span>
                            </div>
                            <input
                                v-model.number="form.skor_permodalan"
                                type="range"
                                min="0"
                                max="100"
                                step="0.5"
                                class="w-full cursor-pointer accent-teal-600"
                            />
                            <div class="flex justify-between text-[10px] font-medium text-gray-500">
                                <span>Nilai 0 - 100</span>
                                <span>Kontribusi Skor: {{ (form.skor_permodalan * 0.15).toFixed(2) }}</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700"
                            >Kesimpulan & Catatan Hasil Pengawasan</label
                        >
                        <textarea
                            v-model="form.kesimpulan_pengawasan"
                            rows="2"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition duration-150 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                        ></textarea>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700"
                            >Upload Berkas Berita Acara Pemeriksaan (PDF)</label
                        >
                        <input
                            @change="handleFileChange"
                            type="file"
                            accept=".pdf,.doc,.docx"
                            class="w-full text-xs text-gray-500 transition file:mr-4 file:rounded-xl file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-xs file:font-bold file:text-white hover:file:bg-slate-800"
                        />
                    </div>
                </div>

                <!-- SECTION 3: INLINE MATRIKS TEMUAN AUDIT GENERATOR -->
                <div class="shadow-2xs space-y-4 rounded-3xl border border-gray-200/70 bg-white p-6">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-amber-700">
                            <AlertCircle class="h-4 w-4 text-amber-600" />
                            3. Matriks Temuan Pemeriksaan & Rekomendasi Audit
                        </div>

                        <button
                            type="button"
                            @click="addTemuanRow"
                            class="flex items-center gap-1.5 rounded-xl border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-700 transition hover:bg-amber-100"
                        >
                            <Plus class="h-3.5 w-3.5" />
                            Tambah Temuan
                        </button>
                    </div>

                    <div v-if="form.temuans.length > 0" class="space-y-4">
                        <div
                            v-for="(t, idx) in form.temuans"
                            :key="idx"
                            class="relative space-y-3 rounded-2xl border border-gray-200/80 bg-gray-50 p-4"
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-amber-700">Temuan #{{ idx + 1 }}</span>
                                <button type="button" @click="removeTemuanRow(idx)" class="p-1 text-gray-400 transition hover:text-rose-600">
                                    <Trash2 class="h-4 w-4" />
                                </button>
                            </div>

                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                <div>
                                    <label class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-gray-700">Aspek Temuan *</label>
                                    <select
                                        v-model="t.aspek_temuan"
                                        class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs text-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                                    >
                                        <option value="Kelembagaan">Kelembagaan</option>
                                        <option value="Keuangan">Keuangan</option>
                                        <option value="Operasional">Operasional</option>
                                        <option value="Usaha">Usaha</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-gray-700">Tingkat Risiko *</label>
                                    <select
                                        v-model="t.tingkat_risiko"
                                        class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs text-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                                    >
                                        <option value="Rendah">Rendah (Biru)</option>
                                        <option value="Sedang">Sedang (Kuning)</option>
                                        <option value="Tinggi">Tinggi (Oranye)</option>
                                        <option value="Kritis">Kritis (Merah)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-gray-700"
                                        >Batas Waktu (Jatuh Tempo) *</label
                                    >
                                    <input
                                        v-model="t.batas_waktu"
                                        type="date"
                                        required
                                        class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs text-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                                    />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-gray-700"
                                        >Deskripsi Temuan Lapangan *</label
                                    >
                                    <textarea
                                        v-model="t.deskripsi_temuan"
                                        rows="2"
                                        required
                                        placeholder="Contoh: Belum terdapat SOP tertulis mengenai penyaluran..."
                                        class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs text-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                                    ></textarea>
                                </div>

                                <div>
                                    <label class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-gray-700"
                                        >Rekomendasi Tim Pengawas *</label
                                    >
                                    <textarea
                                        v-model="t.rekomendasi"
                                        rows="2"
                                        required
                                        placeholder="Contoh: Pengurus wajib menyusun dan mengesahkan SOP..."
                                        class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs text-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                                    ></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="py-4 text-center text-xs italic text-gray-400">
                        Tidak ada temuan audit yang ditambahkan. Klik "Tambah Temuan" jika terdapat catatan hasil pemeriksaan.
                    </div>
                </div>

                <!-- SUBMIT BUTTON -->
                <div class="flex items-center justify-end gap-4">
                    <Link href="/pengawasan" class="rounded-xl px-5 py-2.5 text-xs font-bold text-gray-600 transition hover:bg-gray-100">
                        Batal
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-xl bg-slate-900 px-6 py-3 text-xs font-bold text-white shadow-md shadow-slate-900/10 transition hover:bg-slate-800 disabled:opacity-50"
                    >
                        Simpan Berita Acara & Nilai Kesehatan
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
