<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import {
    ShieldCheck,
    ArrowLeft,
    Plus,
    Trash2,
    Award,
    AlertCircle,
    FileText,
    Calculator
} from 'lucide-vue-next';

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
    kesimpulan_pengawasan: 'Berdasarkan pemeriksaan lapangan 4 aspek, koperasi secara umum beroperasi dengan tata kelola dan permodalan yang cukup memadai.',
    file_berita_acara: null as File | null,

    // Dynamic Temuans List
    temuans: [
        {
            aspek_temuan: 'Operasional',
            deskripsi_temuan: 'Belum terdapat SOP tertulis mengenai manajemen risiko penyaluran pinjaman/pembiayaan.',
            rekomendasi: 'Pengurus wajib menyusun dan menyahkan SOP Manajemen Risiko serta menyampaikan salinan ke Dinas Koperasi Provsu.',
            batas_waktu: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
            tingkat_risiko: 'Sedang',
        }
    ]
});

// Real-time Automatic Score Calculator
const computedSkorTotal = computed(() => {
    const tk = Number(form.skor_tata_kelola) || 0;
    const pr = Number(form.skor_profil_risiko) || 0;
    const kk = Number(form.skor_kinerja_keuangan) || 0;
    const pm = Number(form.skor_permodalan) || 0;

    const total = (tk * 0.30) + (pr * 0.25) + (kk * 0.30) + (pm * 0.15);
    return Math.round(total * 100) / 100;
});

const computedPredikat = computed(() => {
    const score = computedSkorTotal.value;
    if (score >= 80.00) return 'Sehat';
    if (score >= 66.00) return 'Cukup Sehat';
    if (score >= 51.00) return 'Dalam Pengawasan';
    return 'Pengawasan Khusus';
});

const getPredikatColorClass = computed(() => {
    switch (computedPredikat.value) {
        case 'Sehat': return 'bg-emerald-500/20 text-emerald-400 border-emerald-500/40';
        case 'Cukup Sehat': return 'bg-cyan-500/20 text-cyan-400 border-cyan-500/40';
        case 'Dalam Pengawasan': return 'bg-amber-500/20 text-amber-400 border-amber-500/40';
        default: return 'bg-rose-500/20 text-rose-400 border-rose-500/40';
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
        }
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Input Form Pemeriksaan & Penilaian Kesehatan Koperasi" />

        <div class="space-y-6 max-w-5xl mx-auto">
            <!-- TOP BACK NAVIGATION -->
            <div>
                <Link href="/pengawasan" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-purple-400 transition">
                    <ArrowLeft class="w-4 h-4" />
                    Kembali ke Daftar Pengawasan
                </Link>
            </div>

            <!-- TITLE BANNER -->
            <div class="rounded-2xl bg-gradient-to-r from-purple-950 via-slate-900 to-slate-900 border border-purple-800/40 p-6 shadow-2xl">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center">
                        <ShieldCheck class="w-6 h-6" />
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">
                            Form Penilaian Kesehatan Koperasi (4 Aspek Permenkop)
                        </h1>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Kalkulasi skor & predikat kesehatan otomatis dihitung secara real-time.
                        </p>
                    </div>
                </div>
            </div>

            <!-- FORM WRAPPER -->
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <!-- SECTION 1: SURAT TUGAS & KOPERASI -->
                <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
                    <div class="text-xs font-bold text-purple-400 uppercase tracking-wider border-b border-slate-800 pb-2">
                        1. Informasi Surat Tugas & Objek Koperasi
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Pilih Koperasi *</label>
                            <select 
                                v-model="form.koperasi_id"
                                required
                                class="w-full rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs px-3.5 py-2.5 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition"
                            >
                                <option v-for="k in koperasis" :key="k.id" :value="k.id">
                                    {{ k.nama_koperasi }} ({{ k.kabupaten_kota }}) - {{ k.no_badan_hukum }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Nomor Surat Tugas (STP) *</label>
                            <input 
                                v-model="form.no_surat_tugas"
                                type="text"
                                required
                                class="w-full rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs px-3.5 py-2.5 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Tanggal Pemeriksaan Lapangan *</label>
                            <input 
                                v-model="form.tanggal_pemeriksaan"
                                type="date"
                                required
                                class="w-full rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs px-3.5 py-2.5 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition"
                            />
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Tim Pengawas Diskop Provsu *</label>
                            <input 
                                v-model="form.nama_tim_pengawas"
                                type="text"
                                required
                                class="w-full rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs px-3.5 py-2.5 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition"
                            />
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: REAL-TIME 4 ASPECT HEALTH SCORING ENGINE -->
                <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                        <div class="text-xs font-bold text-emerald-400 uppercase tracking-wider flex items-center gap-2">
                            <Calculator class="w-4 h-4" />
                            2. Engine Perhitungan Skor Kesehatan (4 Aspek Permenkop)
                        </div>
                    </div>

                    <!-- Live Score & Predicate Result Gauge -->
                    <div class="p-5 rounded-xl bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 border border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div>
                            <div class="text-xs font-semibold text-slate-400 uppercase">Kalkulasi Skor Total Real-time</div>
                            <div class="text-3xl font-extrabold text-white mt-1">
                                {{ computedSkorTotal }} <span class="text-xs font-normal text-slate-400">/ 100</span>
                            </div>
                        </div>

                        <div class="text-right">
                            <div class="text-[11px] font-semibold text-slate-400 uppercase">Hasil Predikat Instan</div>
                            <div class="text-sm font-extrabold px-4 py-1.5 rounded-full border mt-1 inline-block" :class="getPredikatColorClass">
                                {{ computedPredikat }}
                            </div>
                        </div>
                    </div>

                    <!-- 4 Aspects Input Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Aspek 1: Tata Kelola -->
                        <div class="p-4 rounded-xl bg-slate-800/60 border border-slate-700/60 space-y-2">
                            <div class="flex justify-between text-xs font-bold text-slate-200">
                                <span>Tata Kelola (Bobot 30%)</span>
                                <span class="text-emerald-400 font-mono">{{ form.skor_tata_kelola }}</span>
                            </div>
                            <input 
                                v-model.number="form.skor_tata_kelola"
                                type="range" 
                                min="0" 
                                max="100" 
                                step="0.5"
                                class="w-full accent-emerald-500 cursor-pointer"
                            />
                            <div class="flex justify-between text-[10px] text-slate-400">
                                <span>Nilai 0 - 100</span>
                                <span>Kontribusi Skor: {{ (form.skor_tata_kelola * 0.30).toFixed(2) }}</span>
                            </div>
                        </div>

                        <!-- Aspek 2: Profil Risiko -->
                        <div class="p-4 rounded-xl bg-slate-800/60 border border-slate-700/60 space-y-2">
                            <div class="flex justify-between text-xs font-bold text-slate-200">
                                <span>Profil Risiko (Bobot 25%)</span>
                                <span class="text-purple-400 font-mono">{{ form.skor_profil_risiko }}</span>
                            </div>
                            <input 
                                v-model.number="form.skor_profil_risiko"
                                type="range" 
                                min="0" 
                                max="100" 
                                step="0.5"
                                class="w-full accent-purple-500 cursor-pointer"
                            />
                            <div class="flex justify-between text-[10px] text-slate-400">
                                <span>Nilai 0 - 100</span>
                                <span>Kontribusi Skor: {{ (form.skor_profil_risiko * 0.25).toFixed(2) }}</span>
                            </div>
                        </div>

                        <!-- Aspek 3: Kinerja Keuangan -->
                        <div class="p-4 rounded-xl bg-slate-800/60 border border-slate-700/60 space-y-2">
                            <div class="flex justify-between text-xs font-bold text-slate-200">
                                <span>Kinerja Keuangan (Bobot 30%)</span>
                                <span class="text-cyan-400 font-mono">{{ form.skor_kinerja_keuangan }}</span>
                            </div>
                            <input 
                                v-model.number="form.skor_kinerja_keuangan"
                                type="range" 
                                min="0" 
                                max="100" 
                                step="0.5"
                                class="w-full accent-cyan-500 cursor-pointer"
                            />
                            <div class="flex justify-between text-[10px] text-slate-400">
                                <span>Nilai 0 - 100</span>
                                <span>Kontribusi Skor: {{ (form.skor_kinerja_keuangan * 0.30).toFixed(2) }}</span>
                            </div>
                        </div>

                        <!-- Aspek 4: Permodalan -->
                        <div class="p-4 rounded-xl bg-slate-800/60 border border-slate-700/60 space-y-2">
                            <div class="flex justify-between text-xs font-bold text-slate-200">
                                <span>Permodalan (Bobot 15%)</span>
                                <span class="text-teal-400 font-mono">{{ form.skor_permodalan }}</span>
                            </div>
                            <input 
                                v-model.number="form.skor_permodalan"
                                type="range" 
                                min="0" 
                                max="100" 
                                step="0.5"
                                class="w-full accent-teal-500 cursor-pointer"
                            />
                            <div class="flex justify-between text-[10px] text-slate-400">
                                <span>Nilai 0 - 100</span>
                                <span>Kontribusi Skor: {{ (form.skor_permodalan * 0.15).toFixed(2) }}</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Kesimpulan & Catatan Hasil Pengawasan</label>
                        <textarea 
                            v-model="form.kesimpulan_pengawasan"
                            rows="2"
                            class="w-full rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs px-3.5 py-2.5 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Upload Berkas Berita Acara Pemeriksaan (PDF)</label>
                        <input 
                            @change="handleFileChange"
                            type="file"
                            accept=".pdf,.doc,.docx"
                            class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-purple-400 hover:file:bg-slate-700 transition"
                        />
                    </div>
                </div>

                <!-- SECTION 3: INLINE MATRIKS TEMUAN AUDIT GENERATOR -->
                <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                        <div class="text-xs font-bold text-amber-400 uppercase tracking-wider flex items-center gap-2">
                            <AlertCircle class="w-4 h-4" />
                            3. Matriks Temuan Pemeriksaan & Rekomendasi Audit
                        </div>

                        <button 
                            type="button"
                            @click="addTemuanRow"
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-500/10 text-amber-300 border border-amber-500/30 text-xs font-semibold hover:bg-amber-500/20 transition"
                        >
                            <Plus class="w-3.5 h-3.5" />
                            Tambah Temuan
                        </button>
                    </div>

                    <div v-if="form.temuans.length > 0" class="space-y-4">
                        <div 
                            v-for="(t, idx) in form.temuans" 
                            :key="idx"
                            class="p-4 rounded-xl bg-slate-800/40 border border-slate-700/60 relative space-y-3"
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-amber-400">Temuan #{{ idx + 1 }}</span>
                                <button 
                                    type="button" 
                                    @click="removeTemuanRow(idx)"
                                    class="p-1 text-slate-400 hover:text-rose-400 transition"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-300 mb-1">Aspek Temuan *</label>
                                    <select 
                                        v-model="t.aspek_temuan"
                                        class="w-full rounded-xl bg-slate-800 border border-slate-700 text-white text-xs px-3 py-2"
                                    >
                                        <option value="Kelembagaan">Kelembagaan</option>
                                        <option value="Keuangan">Keuangan</option>
                                        <option value="Operasional">Operasional</option>
                                        <option value="Usaha">Usaha</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-300 mb-1">Tingkat Risiko *</label>
                                    <select 
                                        v-model="t.tingkat_risiko"
                                        class="w-full rounded-xl bg-slate-800 border border-slate-700 text-white text-xs px-3 py-2"
                                    >
                                        <option value="Rendah">Rendah (Biru)</option>
                                        <option value="Sedang">Sedang (Kuning)</option>
                                        <option value="Tinggi">Tinggi (Oranye)</option>
                                        <option value="Kritis">Kritis (Merah)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-300 mb-1">Batas Waktu (Jatuh Tempo) *</label>
                                    <input 
                                        v-model="t.batas_waktu"
                                        type="date"
                                        required
                                        class="w-full rounded-xl bg-slate-800 border border-slate-700 text-white text-xs px-3 py-2"
                                    />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-300 mb-1">Deskripsi Temuan Lapangan *</label>
                                    <textarea 
                                        v-model="t.deskripsi_temuan"
                                        rows="2"
                                        required
                                        placeholder="Contoh: Belum terdapat SOP tertulis mengenai penyaluran..."
                                        class="w-full rounded-xl bg-slate-800 border border-slate-700 text-white text-xs px-3 py-2"
                                    ></textarea>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-300 mb-1">Rekomendasi Tim Pengawas *</label>
                                    <textarea 
                                        v-model="t.rekomendasi"
                                        rows="2"
                                        required
                                        placeholder="Contoh: Pengurus wajib menyusun dan mengesahkan SOP..."
                                        class="w-full rounded-xl bg-slate-800 border border-slate-700 text-white text-xs px-3 py-2"
                                    ></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-xs text-slate-500 italic text-center py-4">
                        Tidak ada temuan audit yang ditambahkan. Klik "Tambah Temuan" jika terdapat catatan hasil pemeriksaan.
                    </div>
                </div>

                <!-- SUBMIT BUTTON -->
                <div class="flex items-center justify-end gap-4">
                    <Link href="/pengawasan" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-300 hover:bg-slate-800 transition">
                        Batal
                    </Link>
                    <button 
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-3 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-semibold text-xs shadow-lg shadow-purple-600/30 transition disabled:opacity-50"
                    >
                        Simpan Berita Acara & Nilai Kesehatan
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
