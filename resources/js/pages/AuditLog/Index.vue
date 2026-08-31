<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { CheckCircle2, Clock, RotateCcw, Search, ShieldCheck, XCircle } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    logs: Array<{
        id: string;
        modul: string;
        judul: string;
        no_referensi: string;
        status: string;
        user?: string;
        timestamp?: string;
        alasan?: string;
    }>;
    filters: {
        search?: string;
        status?: string;
    };
}>();

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');

const applyFilters = () => {
    router.get(
        '/audit-log',
        {
            search: search.value || undefined,
            status: status.value || undefined,
        },
        { preserveState: true, replace: true },
    );
};

const resetFilters = () => {
    search.value = '';
    status.value = '';
    applyFilters();
};

const formatDate = (dateStr?: string) => {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Riwayat Log Verifikasi Pengawas" />

        <div class="space-y-6">
            <!-- PAGE TITLE BANNER CARD -->
            <div class="shadow-2xs rounded-3xl border border-gray-200/70 bg-white p-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-50 font-bold text-indigo-600">
                        <ShieldCheck class="h-6 w-6" />
                    </div>
                    <div>
                        <h1 class="text-xl font-bold tracking-tight text-gray-900 sm:text-2xl">
                            Riwayat Audit Log & Track Record Verifikasi
                        </h1>
                        <p class="mt-0.5 text-xs text-gray-500">
                            Catatan jejak audit (Audit Trail) seluruh keputusan pengesahan (Verified/Sah) dan penolakan (Rejected) oleh Pengawas Diskop.
                        </p>
                    </div>
                </div>
            </div>

            <!-- SEARCH & FILTER BAR -->
            <div class="shadow-2xs space-y-4 rounded-3xl border border-gray-200/70 bg-white p-5">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <div class="relative sm:col-span-2">
                        <Search class="absolute left-3.5 top-3 h-4 w-4 text-gray-400" />
                        <input
                            v-model="search"
                            @input="applyFilters"
                            type="text"
                            placeholder="Cari Nama Koperasi / No. Referensi / Modul..."
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-9 pr-4 text-xs text-gray-900 transition placeholder:text-gray-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                        />
                    </div>

                    <div>
                        <select
                            v-model="status"
                            @change="applyFilters"
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-xs text-gray-900 transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                        >
                            <option value="">Semua Keputusan</option>
                            <option value="verified">Verified (Disetujui / Sah)</option>
                            <option value="rejected">Rejected (Ditolak)</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-between border-t border-gray-100 pt-2 text-xs">
                    <span class="font-medium text-gray-500">Total Log Tercatat: <strong class="text-gray-900">{{ logs.length }}</strong> Entri</span>
                    <button @click="resetFilters" class="flex items-center gap-1 text-xs font-bold text-gray-500 transition hover:text-emerald-700">
                        <RotateCcw class="h-3.5 w-3.5" />
                        Reset Filter
                    </button>
                </div>
            </div>

            <!-- AUDIT LOG TABLE -->
            <div class="shadow-2xs overflow-hidden rounded-3xl border border-gray-200/70 bg-white">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="border-b border-gray-100 bg-gray-50/80 text-[10px] font-bold uppercase tracking-wider text-gray-500">
                            <tr>
                                <th class="px-4 py-3.5 font-bold">Waktu & Tanggal</th>
                                <th class="px-4 py-3.5 font-bold">Modul Subjek</th>
                                <th class="px-4 py-3.5 font-bold">Detail Entri & No. Referensi</th>
                                <th class="px-4 py-3.5 text-center font-bold">Keputusan Pengawas</th>
                                <th class="px-4 py-3.5 font-bold">Verifikator / Penolak</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="log in logs" :key="log.id" class="group transition hover:bg-gray-50/60">
                                <td class="whitespace-nowrap px-4 py-3.5 font-mono text-gray-600">
                                    <div class="flex items-center gap-1.5 font-bold text-gray-900">
                                        <Clock class="h-3.5 w-3.5 text-gray-400" />
                                        {{ formatDate(log.timestamp) }}
                                    </div>
                                </td>

                                <td class="px-4 py-3.5 font-bold text-indigo-700">
                                    <span class="rounded-lg bg-indigo-50 px-2.5 py-1 text-[11px] border border-indigo-100">
                                        {{ log.modul }}
                                    </span>
                                </td>

                                <td class="px-4 py-3.5">
                                    <div class="font-bold text-gray-900">{{ log.judul }}</div>
                                    <div class="font-mono text-[10px] text-gray-400">{{ log.no_referensi }}</div>
                                    <div v-if="log.alasan" class="mt-1 rounded-lg bg-rose-50 p-2 text-[11px] font-mono text-rose-800 border border-rose-100">
                                        <strong>Alasan Penolakan:</strong> {{ log.alasan }}
                                    </div>
                                </td>

                                <td class="px-4 py-3.5 text-center">
                                    <span
                                        v-if="log.status === 'verified'"
                                        class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[10px] font-bold text-emerald-700"
                                    >
                                        <CheckCircle2 class="h-3 w-3 text-emerald-600" />
                                        Verified (Sah)
                                    </span>
                                    <span
                                        v-else
                                        class="inline-flex items-center gap-1 rounded-full border border-rose-200 bg-rose-50 px-2.5 py-1 text-[10px] font-bold text-rose-700"
                                    >
                                        <XCircle class="h-3 w-3 text-rose-600" />
                                        Ditolak
                                    </span>
                                </td>

                                <td class="px-4 py-3.5 font-medium text-gray-900">
                                    {{ log.user || 'Bidang Pengawasan' }}
                                </td>
                            </tr>
                            <tr v-if="logs.length === 0">
                                <td colspan="5" class="py-8 text-center text-xs italic text-gray-400">
                                    Belum ada catatan riwayat audit log verifikasi.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
