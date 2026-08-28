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
        class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 sm:p-6 animate-in fade-in duration-200"
    >
        <div class="relative w-full max-w-3xl rounded-3xl bg-white border border-gray-200/80 shadow-2xl overflow-hidden my-8">
            <!-- Header Modal -->
            <div class="px-6 py-4 bg-white border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <Building2 class="w-5 h-5" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">
                            {{ isEditing ? 'Edit Data Koperasi' : 'Tambah Koperasi Baru' }}
                        </h3>
                        <p class="text-xs text-gray-500">SIMPADU KOP-UKM Provinsi Sumatera Utara</p>
                    </div>
                </div>

                <button 
                    @click="emit('close')" 
                    class="p-1.5 rounded-xl text-gray-400 hover:text-gray-900 hover:bg-gray-100 transition"
                >
                    <X class="w-5 h-5" />
                </button>
            </div>

            <!-- Form Body -->
            <form @submit.prevent="handleSubmit" class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">
                <!-- Section 1: Kelembagaan -->
                <div class="space-y-4">
                    <div class="text-xs font-bold text-emerald-700 uppercase tracking-wider border-b border-gray-100 pb-2">
                        1. Data Kelembagaan & Wilayah
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Nama Koperasi *</label>
                            <input 
                                v-model="form.nama_koperasi" 
                                type="text"
                                required
                                placeholder="Contoh: KSP Harapan Kita Medan"
                                class="w-full rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm px-3.5 py-2.5 placeholder:text-gray-400 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150"
                            />
                            <div v-if="form.errors.nama_koperasi" class="text-rose-600 text-[11px] font-semibold mt-1">{{ form.errors.nama_koperasi }}</div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Nomor Badan Hukum *</label>
                            <input 
                                v-model="form.no_badan_hukum" 
                                type="text"
                                required
                                placeholder="AHU-0001.AH.01.26..."
                                class="w-full rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm px-3.5 py-2.5 placeholder:text-gray-400 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150"
                            />
                            <div v-if="form.errors.no_badan_hukum" class="text-rose-600 text-[11px] font-semibold mt-1">{{ form.errors.no_badan_hukum }}</div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">NIK Koperasi (Opsional)</label>
                            <input 
                                v-model="form.nik_koperasi" 
                                type="text"
                                placeholder="1271010001..."
                                class="w-full rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm px-3.5 py-2.5 placeholder:text-gray-400 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Jenis Koperasi *</label>
                            <select 
                                v-model="form.jenis_koperasi"
                                class="w-full rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm px-3.5 py-2.5 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150"
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
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Kabupaten / Kota *</label>
                            <select 
                                v-model="form.kabupaten_kota"
                                class="w-full rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm px-3.5 py-2.5 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150"
                            >
                                <option v-for="kab in kabupatenKotaList" :key="kab" :value="kab">{{ kab }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tahun Berdiri *</label>
                            <input 
                                v-model="form.tahun_berdiri" 
                                type="number"
                                required
                                class="w-full rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm px-3.5 py-2.5 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150"
                            />
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Alamat Lengkap *</label>
                            <textarea 
                                v-model="form.alamat" 
                                rows="2"
                                required
                                placeholder="Jl. Lintas Utama No. 123..."
                                class="w-full rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm px-3.5 py-2.5 placeholder:text-gray-400 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150"
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Keuangan Dasar -->
                <div class="space-y-4">
                    <div class="text-xs font-bold text-teal-700 uppercase tracking-wider border-b border-gray-100 pb-2">
                        2. Data Keuangan Dasar (IDR)
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Total Aset (Rp) *</label>
                            <input 
                                v-model="form.aset" 
                                type="number"
                                step="1000"
                                required
                                class="w-full rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm px-3.5 py-2.5 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150 font-mono"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Modal Sendiri (Rp) *</label>
                            <input 
                                v-model="form.modal_sendiri" 
                                type="number"
                                step="1000"
                                required
                                class="w-full rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm px-3.5 py-2.5 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150 font-mono"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Volume Usaha (Rp) *</label>
                            <input 
                                v-model="form.volume_usaha" 
                                type="number"
                                step="1000"
                                required
                                class="w-full rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm px-3.5 py-2.5 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150 font-mono"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Sisa Hasil Usaha / SHU (Rp) *</label>
                            <input 
                                v-model="form.shu" 
                                type="number"
                                step="1000"
                                required
                                class="w-full rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm px-3.5 py-2.5 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150 font-mono"
                            />
                        </div>
                    </div>
                </div>

                <!-- Section 3: Pengurus & Keanggotaan -->
                <div class="space-y-4">
                    <div class="text-xs font-bold text-indigo-700 uppercase tracking-wider border-b border-gray-100 pb-2">
                        3. Pengurus & Keanggotaan
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Ketua Pengurus *</label>
                            <input 
                                v-model="form.ketua" 
                                type="text"
                                required
                                class="w-full rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm px-3.5 py-2.5 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Sekretaris *</label>
                            <input 
                                v-model="form.sekretaris" 
                                type="text"
                                required
                                class="w-full rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm px-3.5 py-2.5 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Bendahara *</label>
                            <input 
                                v-model="form.bendahara" 
                                type="text"
                                required
                                class="w-full rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm px-3.5 py-2.5 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Ketua Pengawas *</label>
                            <input 
                                v-model="form.ketua_pengawas" 
                                type="text"
                                required
                                class="w-full rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm px-3.5 py-2.5 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Manajer (Opsional)</label>
                            <input 
                                v-model="form.manajer" 
                                type="text"
                                class="w-full rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm px-3.5 py-2.5 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Periode Jabatan *</label>
                            <input 
                                v-model="form.periode_jabatan" 
                                type="text"
                                placeholder="2023 - 2026"
                                required
                                class="w-full rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm px-3.5 py-2.5 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Jumlah Anggota Pria *</label>
                            <input 
                                v-model="form.jumlah_anggota_pria" 
                                type="number"
                                required
                                class="w-full rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm px-3.5 py-2.5 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Jumlah Anggota Wanita *</label>
                            <input 
                                v-model="form.jumlah_anggota_wanita" 
                                type="number"
                                required
                                class="w-full rounded-xl bg-gray-50 border border-gray-200 text-gray-900 text-xs sm:text-sm px-3.5 py-2.5 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition duration-150"
                            />
                        </div>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    <button 
                        type="button" 
                        @click="emit('close')"
                        class="px-4 py-2.5 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-100 transition"
                    >
                        Batal
                    </button>

                    <button 
                        type="submit"
                        :disabled="form.processing"
                        class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md shadow-slate-900/10 transition disabled:opacity-50"
                    >
                        {{ isEditing ? 'Simpan Perubahan' : 'Simpan Koperasi' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

