<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Building2, X } from 'lucide-vue-next';
import { computed, watch } from 'vue';

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
    { immediate: true },
);

const handleSubmit = () => {
    if (isEditing.value) {
        form.put(`/koperasi/${props.koperasiToEdit.id}`, {
            onSuccess: () => {
                emit('close');
                form.reset();
            },
        });
    } else {
        form.post('/koperasi', {
            onSuccess: () => {
                emit('close');
                form.reset();
            },
        });
    }
};
</script>

<template>
    <div
        v-if="isOpen"
        class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/60 p-4 backdrop-blur-sm duration-200 animate-in fade-in sm:p-6"
    >
        <div class="relative my-8 w-full max-w-3xl overflow-hidden rounded-3xl border border-gray-200/80 bg-white shadow-2xl">
            <!-- Header Modal -->
            <div class="flex items-center justify-between border-b border-gray-100 bg-white px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                        <Building2 class="h-5 w-5" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">
                            {{ isEditing ? 'Edit Data Koperasi' : 'Tambah Koperasi Baru' }}
                        </h3>
                        <p class="text-xs text-gray-500">SIMPADU KOP-UKM Provinsi Sumatera Utara</p>
                    </div>
                </div>

                <button @click="emit('close')" class="rounded-xl p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-900">
                    <X class="h-5 w-5" />
                </button>
            </div>

            <!-- Form Body -->
            <form @submit.prevent="handleSubmit" class="max-h-[75vh] space-y-6 overflow-y-auto p-6">
                <!-- Section 1: Kelembagaan -->
                <div class="space-y-4">
                    <div class="border-b border-gray-100 pb-2 text-xs font-bold uppercase tracking-wider text-emerald-700">
                        1. Data Kelembagaan & Wilayah
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">Nama Koperasi *</label>
                            <input
                                v-model="form.nama_koperasi"
                                type="text"
                                required
                                placeholder="Contoh: KSP Harapan Kita Medan"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition duration-150 placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                            />
                            <div v-if="form.errors.nama_koperasi" class="mt-1 text-[11px] font-semibold text-rose-600">
                                {{ form.errors.nama_koperasi }}
                            </div>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">Nomor Badan Hukum *</label>
                            <input
                                v-model="form.no_badan_hukum"
                                type="text"
                                required
                                placeholder="AHU-0001.AH.01.26..."
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition duration-150 placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                            />
                            <div v-if="form.errors.no_badan_hukum" class="mt-1 text-[11px] font-semibold text-rose-600">
                                {{ form.errors.no_badan_hukum }}
                            </div>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">NIK Koperasi (Opsional)</label>
                            <input
                                v-model="form.nik_koperasi"
                                type="text"
                                placeholder="1271010001..."
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition duration-150 placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                            />
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">Jenis Koperasi *</label>
                            <select
                                v-model="form.jenis_koperasi"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition duration-150 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
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
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">Kabupaten / Kota *</label>
                            <select
                                v-model="form.kabupaten_kota"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition duration-150 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                            >
                                <option v-for="kab in kabupatenKotaList" :key="kab" :value="kab">{{ kab }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">Tahun Berdiri *</label>
                            <input
                                v-model="form.tahun_berdiri"
                                type="number"
                                required
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition duration-150 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                            />
                        </div>

                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">Alamat Lengkap *</label>
                            <textarea
                                v-model="form.alamat"
                                rows="2"
                                required
                                placeholder="Jl. Lintas Utama No. 123..."
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition duration-150 placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Keuangan Dasar -->
                <div class="space-y-4">
                    <div class="border-b border-gray-100 pb-2 text-xs font-bold uppercase tracking-wider text-teal-700">
                        2. Data Keuangan Dasar (IDR)
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">Total Aset (Rp) *</label>
                            <input
                                v-model="form.aset"
                                type="number"
                                step="1000"
                                required
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 font-mono text-xs text-gray-900 transition duration-150 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                            />
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">Modal Sendiri (Rp) *</label>
                            <input
                                v-model="form.modal_sendiri"
                                type="number"
                                step="1000"
                                required
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 font-mono text-xs text-gray-900 transition duration-150 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                            />
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">Volume Usaha (Rp) *</label>
                            <input
                                v-model="form.volume_usaha"
                                type="number"
                                step="1000"
                                required
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 font-mono text-xs text-gray-900 transition duration-150 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                            />
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">Sisa Hasil Usaha / SHU (Rp) *</label>
                            <input
                                v-model="form.shu"
                                type="number"
                                step="1000"
                                required
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 font-mono text-xs text-gray-900 transition duration-150 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                            />
                        </div>
                    </div>
                </div>

                <!-- Section 3: Pengurus & Keanggotaan -->
                <div class="space-y-4">
                    <div class="border-b border-gray-100 pb-2 text-xs font-bold uppercase tracking-wider text-indigo-700">
                        3. Pengurus & Keanggotaan
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">Ketua Pengurus *</label>
                            <input
                                v-model="form.ketua"
                                type="text"
                                required
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition duration-150 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                            />
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">Sekretaris *</label>
                            <input
                                v-model="form.sekretaris"
                                type="text"
                                required
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition duration-150 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                            />
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">Bendahara *</label>
                            <input
                                v-model="form.bendahara"
                                type="text"
                                required
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition duration-150 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                            />
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">Ketua Pengawas *</label>
                            <input
                                v-model="form.ketua_pengawas"
                                type="text"
                                required
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition duration-150 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                            />
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">Manajer (Opsional)</label>
                            <input
                                v-model="form.manajer"
                                type="text"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition duration-150 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                            />
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">Periode Jabatan *</label>
                            <input
                                v-model="form.periode_jabatan"
                                type="text"
                                placeholder="2023 - 2026"
                                required
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition duration-150 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                            />
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">Jumlah Anggota Pria *</label>
                            <input
                                v-model="form.jumlah_anggota_pria"
                                type="number"
                                required
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition duration-150 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                            />
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700">Jumlah Anggota Wanita *</label>
                            <input
                                v-model="form.jumlah_anggota_wanita"
                                type="number"
                                required
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-xs text-gray-900 transition duration-150 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 sm:text-sm"
                            />
                        </div>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-4">
                    <button
                        type="button"
                        @click="emit('close')"
                        class="rounded-xl px-4 py-2.5 text-xs font-bold text-gray-600 transition hover:bg-gray-100"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-xl bg-slate-900 px-5 py-2.5 text-xs font-bold text-white shadow-md shadow-slate-900/10 transition hover:bg-slate-800 disabled:opacity-50"
                    >
                        {{ isEditing ? 'Simpan Perubahan' : 'Simpan Koperasi' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
