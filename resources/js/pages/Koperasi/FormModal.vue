<script setup lang="ts">
import { watch, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { X, Building2, UserCheck, DollarSign } from 'lucide-vue-next';

const props = defineProps<{
    isOpen: boolean;
    koperasiToEdit: any | null;
    kabupatenKotaList: string[];
}>();

const emit = defineEmits(['close']);

const isEditing = computed(() => !!props.koperasiToEdit);

const form = useForm({
    no_badan_hukum: '',
    nik_koperasi: '',
    nama_koperasi: '',
    jenis_koperasi: 'KSP',
    kabupaten_kota: 'Kota Medan',
    alamat: '',
    tahun_berdiri: new Date().getFullYear(),
    status_keaktifan: 'Aktif',
    aset: 0,
    modal_sendiri: 0,
    volume_usaha: 0,
    shu: 0,

    // Pengurus
    ketua: '',
    sekretaris: '',
    bendahara: '',
    ketua_pengawas: '',
    manajer: '',
    jumlah_anggota_pria: 0,
    jumlah_anggota_wanita: 0,
    periode_jabatan: '2023 - 2026',
});

watch(
    () => props.koperasiToEdit,
    (val) => {
        if (val) {
            form.no_badan_hukum = val.no_badan_hukum || '';
            form.nik_koperasi = val.nik_koperasi || '';
            form.nama_koperasi = val.nama_koperasi || '';
            form.jenis_koperasi = val.jenis_koperasi || 'KSP';
            form.kabupaten_kota = val.kabupaten_kota || 'Kota Medan';
            form.alamat = val.alamat || '';
            form.tahun_berdiri = val.tahun_berdiri || new Date().getFullYear();
            form.status_keaktifan = val.status_keaktifan || 'Aktif';
            form.aset = val.aset || 0;
            form.modal_sendiri = val.modal_sendiri || 0;
            form.volume_usaha = val.volume_usaha || 0;
            form.shu = val.shu || 0;

            if (val.pengurus) {
                form.ketua = val.pengurus.ketua || '';
                form.sekretaris = val.pengurus.sekretaris || '';
                form.bendahara = val.pengurus.bendahara || '';
                form.ketua_pengawas = val.pengurus.ketua_pengawas || '';
                form.manajer = val.pengurus.manajer || '';
                form.jumlah_anggota_pria = val.pengurus.jumlah_anggota_pria || 0;
                form.jumlah_anggota_wanita = val.pengurus.jumlah_anggota_wanita || 0;
                form.periode_jabatan = val.pengurus.periode_jabatan || '2023 - 2026';
            }
        } else {
            form.reset();
        }
    },
    { immediate: true }
);

const handleSubmit = () => {
    if (isEditing.value) {
        form.put(`/koperasi/${props.koperasiToEdit.id}`, {
            onSuccess: () => {
                emit('close');
                form.reset();
            }
        });
    } else {
        form.post('/koperasi', {
            onSuccess: () => {
                emit('close');
                form.reset();
            }
        });
    }
};
</script>

<template>
    <div 
        v-if="isOpen" 
        class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 sm:p-6 animate-in fade-in duration-200"
    >
        <div class="relative w-full max-w-3xl rounded-2xl bg-slate-900 border border-slate-800 shadow-2xl overflow-hidden my-8">
            <!-- Header Modal -->
            <div class="px-6 py-4 bg-slate-900 border-b border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                        <Building2 class="w-5 h-5" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-white">
                            {{ isEditing ? 'Edit Data Koperasi' : 'Tambah Koperasi Baru' }}
                        </h3>
                        <p class="text-xs text-slate-400">SIMPADU KOP-UKM Provinsi Sumatera Utara</p>
                    </div>
                </div>

                <button 
                    @click="emit('close')" 
                    class="p-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition"
                >
                    <X class="w-5 h-5" />
                </button>
            </div>

            <!-- Form Body -->
            <form @submit.prevent="handleSubmit" class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">
                <!-- Section 1: Kelembagaan -->
                <div class="space-y-4">
                    <div class="text-xs font-bold text-emerald-400 uppercase tracking-wider border-b border-slate-800 pb-1">
                        1. Data Kelembagaan & Wilayah
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Koperasi *</label>
                            <input 
                                v-model="form.nama_koperasi" 
                                type="text"
                                required
                                placeholder="Contoh: KSP Harapan Kita Medan"
                                class="w-full rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs px-3.5 py-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                            />
                            <div v-if="form.errors.nama_koperasi" class="text-rose-400 text-[11px] mt-1">{{ form.errors.nama_koperasi }}</div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Nomor Badan Hukum *</label>
                            <input 
                                v-model="form.no_badan_hukum" 
                                type="text"
                                required
                                placeholder="AHU-0001.AH.01.26..."
                                class="w-full rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs px-3.5 py-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                            />
                            <div v-if="form.errors.no_badan_hukum" class="text-rose-400 text-[11px] mt-1">{{ form.errors.no_badan_hukum }}</div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">NIK Koperasi (Opsional)</label>
                            <input 
                                v-model="form.nik_koperasi" 
                                type="text"
                                placeholder="1271010001..."
                                class="w-full rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs px-3.5 py-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Jenis Koperasi *</label>
                            <select 
                                v-model="form.jenis_koperasi"
                                class="w-full rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs px-3.5 py-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                            >
                                <option value="KSP">KSP (Simpan Pinjam)</option>
                                <option value="KSU">KSU (Serba Usaha)</option>
                                <option value="Produsen">Produsen</option>
                                <option value="Pemasaran">Pemasaran</option>
                                <option value="Konsumen">Konsumen</option>
                                <option value="Jasa">Jasa</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Kabupaten / Kota *</label>
                            <select 
                                v-model="form.kabupaten_kota"
                                class="w-full rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs px-3.5 py-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                            >
                                <option v-for="kab in kabupatenKotaList" :key="kab" :value="kab">{{ kab }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Tahun Berdiri *</label>
                            <input 
                                v-model="form.tahun_berdiri" 
                                type="number"
                                required
                                class="w-full rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs px-3.5 py-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                            />
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Alamat Lengkap *</label>
                            <textarea 
                                v-model="form.alamat" 
                                rows="2"
                                required
                                placeholder="Jl. Lintas Utama No. 123..."
                                class="w-full rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs px-3.5 py-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Keuangan Dasar -->
                <div class="space-y-4">
                    <div class="text-xs font-bold text-teal-400 uppercase tracking-wider border-b border-slate-800 pb-1">
                        2. Data Keuangan Dasar (IDR)
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Total Aset (Rp) *</label>
                            <input 
                                v-model="form.aset" 
                                type="number"
                                step="1000"
                                required
                                class="w-full rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs px-3.5 py-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Modal Sendiri (Rp) *</label>
                            <input 
                                v-model="form.modal_sendiri" 
                                type="number"
                                step="1000"
                                required
                                class="w-full rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs px-3.5 py-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Volume Usaha (Rp) *</label>
                            <input 
                                v-model="form.volume_usaha" 
                                type="number"
                                step="1000"
                                required
                                class="w-full rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs px-3.5 py-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Sisa Hasil Usaha / SHU (Rp) *</label>
                            <input 
                                v-model="form.shu" 
                                type="number"
                                step="1000"
                                required
                                class="w-full rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs px-3.5 py-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                            />
                        </div>
                    </div>
                </div>

                <!-- Section 3: Pengurus & Keanggotaan -->
                <div class="space-y-4">
                    <div class="text-xs font-bold text-purple-400 uppercase tracking-wider border-b border-slate-800 pb-1">
                        3. Pengurus & Keanggotaan
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Ketua Pengurus *</label>
                            <input 
                                v-model="form.ketua" 
                                type="text"
                                required
                                class="w-full rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs px-3.5 py-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Sekretaris *</label>
                            <input 
                                v-model="form.sekretaris" 
                                type="text"
                                required
                                class="w-full rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs px-3.5 py-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Bendahara *</label>
                            <input 
                                v-model="form.bendahara" 
                                type="text"
                                required
                                class="w-full rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs px-3.5 py-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Ketua Pengawas *</label>
                            <input 
                                v-model="form.ketua_pengawas" 
                                type="text"
                                required
                                class="w-full rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs px-3.5 py-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Manajer (Opsional)</label>
                            <input 
                                v-model="form.manajer" 
                                type="text"
                                class="w-full rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs px-3.5 py-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Periode Jabatan *</label>
                            <input 
                                v-model="form.periode_jabatan" 
                                type="text"
                                placeholder="2023 - 2026"
                                required
                                class="w-full rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs px-3.5 py-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Jumlah Anggota Pria *</label>
                            <input 
                                v-model="form.jumlah_anggota_pria" 
                                type="number"
                                required
                                class="w-full rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs px-3.5 py-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Jumlah Anggota Wanita *</label>
                            <input 
                                v-model="form.jumlah_anggota_wanita" 
                                type="number"
                                required
                                class="w-full rounded-xl bg-slate-800/80 border border-slate-700/80 text-white text-xs px-3.5 py-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                            />
                        </div>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
                    <button 
                        type="button" 
                        @click="emit('close')"
                        class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:bg-slate-800 transition"
                    >
                        Batal
                    </button>

                    <button 
                        type="submit"
                        :disabled="form.processing"
                        class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-lg shadow-emerald-600/30 transition disabled:opacity-50"
                    >
                        {{ isEditing ? 'Simpan Perubahan' : 'Simpan Koperasi' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
