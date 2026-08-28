<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import {
    Building2,
    FileSpreadsheet,
    ShieldCheck,
    AlertTriangle,
    Plus,
    Printer,
    Search,
    Clock,
    AlertCircle,
    ChevronRight,
    TrendingUp,
    CheckCircle2
} from 'lucide-vue-next';

const props = defineProps<{
    kpi: {
        total_koperasi: number;
        koperasi_aktif: number;
        kepatuhan_rat_pct: number;
        total_diawasi: number;
        total_temuan_open: number;
        temuan_kritis_open: number;
    };
    charts: {
        predikat: {
            Sehat: number;
            'Cukup Sehat': number;
            'Dalam Pengawasan': number;
            'Pengawasan Khusus': number;
        };
        per_wilayah: Array<{
            kabupaten_kota: string;
            total: number;
            sehat_cukup: number;
        }>;
    };
    alerts: {
        belum_rat: Array<{
            id: number;
            nama_koperasi: string;
            no_badan_hukum: string;
            kabupaten_kota: string;
            jenis_koperasi: string;
        }>;
        temuan_emergency: Array<{
            id: number;
            koperasi_id: number;
            aspek_temuan: string;
            deskripsi_temuan: string;
            batas_waktu: string;
            tingkat_risiko: string;
            status_tindak_lanjut: string;
            koperasi: {
                id: number;
                nama_koperasi: string;
                kabupaten_kota: string;
            };
        }>;
    };
}>();

// Donut Chart Configuration
const donutChartOptions = computed(() => ({
    chart: {
        type: 'donut',
        background: 'transparent',
    },
    labels: ['Sehat', 'Cukup Sehat', 'Dalam Pengawasan', 'Pengawasan Khusus'],
    colors: ['#10b981', '#06b6d4', '#f59e0b', '#ef4444'],
    theme: { mode: 'dark' },
    stroke: { show: false },
    legend: {
        position: 'bottom',
        labels: { colors: '#94a3b8' }
    },
    dataLabels: {
        enabled: true,
        style: { fontSize: '11px', fontFamily: 'Inter, sans-serif' }
    },
    plotOptions: {
        pie: {
            donut: {
                size: '70%',
                labels: {
                    show: true,
                    total: {
                        show: true,
                        label: 'Total Koperasi',
                        color: '#94a3b8',
                        fontSize: '12px',
                        formatter: () => props.kpi.total_koperasi
                    }
                }
            }
        }
    }
}));

const donutSeries = computed(() => [
    props.charts.predikat.Sehat || 0,
    props.charts.predikat['Cukup Sehat'] || 0,
    props.charts.predikat['Dalam Pengawasan'] || 0,
    props.charts.predikat['Pengawasan Khusus'] || 0,
]);

// Bar Chart Configuration
const barChartOptions = computed(() => ({
    chart: {
        type: 'bar',
        toolbar: { show: false },
        background: 'transparent'
    },
    theme: { mode: 'dark' },
    colors: ['#10b981', '#3b82f6'],
    plotOptions: {
        bar: {
            horizontal: false,
            columnWidth: '55%',
            borderRadius: 4
        }
    },
    dataLabels: { enabled: false },
    stroke: { show: true, width: 2, colors: ['transparent'] },
    xaxis: {
        categories: props.charts.per_wilayah.map(item => item.kabupaten_kota.replace('Kabupaten ', '').replace('Kota ', '')),
        labels: {
            style: { colors: '#94a3b8', fontSize: '10px' }
        }
    },
    yaxis: {
        labels: { style: { colors: '#94a3b8' } }
    },
    grid: { borderColor: '#1e293b' },
    legend: {
        position: 'top',
        labels: { colors: '#94a3b8' }
    }
}));

const barSeries = computed(() => [
    {
        name: 'Koperasi Sehat / Cukup Sehat',
        data: props.charts.per_wilayah.map(item => item.sehat_cukup)
    },
    {
        name: 'Total Koperasi',
        data: props.charts.per_wilayah.map(item => item.total)
    }
]);

const getRiskBadge = (risiko: string) => {
    switch (risiko) {
        case 'Kritis': return 'bg-rose-500/20 text-rose-300 border-rose-500/40';
        case 'Tinggi': return 'bg-amber-500/20 text-amber-300 border-amber-500/40';
        case 'Sedang': return 'bg-yellow-500/20 text-yellow-300 border-yellow-500/40';
        default: return 'bg-blue-500/20 text-blue-300 border-blue-500/40';
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Dashboard Pengawasan & Administrasi" />

        <div class="space-y-6">
            <!-- HERO HEADER BANNER -->
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-emerald-950 to-slate-900 border border-emerald-800/40 p-6 sm:p-8 shadow-2xl">
                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-semibold mb-3">
                            <CheckCircle2 class="w-3.5 h-3.5" />
                            Sistem Pengawasan Koperasi Terpadu Provsu
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                            SIMPADU KOP-UKM PROVSU
                        </h1>
                        <p class="text-slate-400 text-sm mt-1 max-w-2xl">
                            Dashboard Ringkasan Eksekutif Administrasi Kelembagaan & Pengawasan Koperasi pada Dinas Koperasi dan UKM Provinsi Sumatera Utara.
                        </p>
                    </div>

                    <!-- Quick Actions -->
                    <div class="flex flex-wrap items-center gap-3">
                        <Link 
                            href="/koperasi"
                            class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs transition shadow-lg shadow-emerald-600/30"
                        >
                            <Plus class="w-4 h-4" />
                            Master Koperasi
                        </Link>
                        <Link 
                            href="/pengawasan"
                            class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-semibold text-xs transition shadow-lg shadow-purple-600/30"
                        >
                            <ShieldCheck class="w-4 h-4" />
                            Input Pengawasan
                        </Link>
                        <Link 
                            href="/cetak"
                            class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-semibold text-xs transition"
                        >
                            <Printer class="w-4 h-4 text-emerald-400" />
                            Cetak Laporan
                        </Link>
                    </div>
                </div>

                <!-- Abstract Decorative Background Glow -->
                <div class="absolute top-0 right-0 -mt-12 -mr-12 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            </div>

            <!-- KPI METRIC CARDS GRID -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Card 1 -->
                <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800/80 hover:border-emerald-500/40 transition duration-300 shadow-xl group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Koperasi</span>
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center group-hover:scale-110 transition">
                            <Building2 class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline gap-2">
                        <span class="text-3xl font-bold text-white">{{ kpi.total_koperasi }}</span>
                        <span class="text-xs font-medium text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-md">
                            {{ kpi.koperasi_aktif }} Aktif
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-2">Tersebar di 33 Kab/Kota Sumut</p>
                </div>

                <!-- Card 2 -->
                <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800/80 hover:border-emerald-500/40 transition duration-300 shadow-xl group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Kepatuhan RAT</span>
                        <div class="w-10 h-10 rounded-xl bg-teal-500/10 text-teal-400 flex items-center justify-center group-hover:scale-110 transition">
                            <FileSpreadsheet class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline gap-2">
                        <span class="text-3xl font-bold text-white">{{ kpi.kepatuhan_rat_pct }}%</span>
                        <span class="text-xs font-medium text-teal-400 bg-teal-500/10 px-2 py-0.5 rounded-md">
                            TB 2024
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-2">Target Batas Waktu 30 Juni (UU 25/1992)</p>
                </div>

                <!-- Card 3 -->
                <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800/80 hover:border-emerald-500/40 transition duration-300 shadow-xl group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Koperasi Diawasi</span>
                        <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center group-hover:scale-110 transition">
                            <ShieldCheck class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline gap-2">
                        <span class="text-3xl font-bold text-white">{{ kpi.total_diawasi }}</span>
                        <span class="text-xs font-medium text-purple-400 bg-purple-500/10 px-2 py-0.5 rounded-md">
                            Pemeriksaan 4 Aspek
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-2">Skor Kesehatan Terverifikasi</p>
                </div>

                <!-- Card 4 -->
                <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800/80 hover:border-emerald-500/40 transition duration-300 shadow-xl group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Temuan Belum Selesai</span>
                        <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center group-hover:scale-110 transition">
                            <AlertTriangle class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline gap-2">
                        <span class="text-3xl font-bold text-white">{{ kpi.total_temuan_open }}</span>
                        <span v-if="kpi.temuan_kritis_open > 0" class="text-xs font-semibold text-rose-400 bg-rose-500/10 px-2 py-0.5 rounded-md">
                            {{ kpi.temuan_kritis_open }} Kritis
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-2">Perlu Verifikasi & Action Plan</p>
                </div>
            </div>

            <!-- CHARTS SECTION -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Donut Chart: Predikat Kesehatan -->
                <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800/80 shadow-xl flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-sm font-bold text-slate-200 uppercase tracking-wider flex items-center gap-2">
                                <TrendingUp class="w-4 h-4 text-emerald-400" />
                                Predikat Kesehatan Koperasi
                            </h2>
                        </div>
                        <p class="text-xs text-slate-400 mb-4">
                            Distribusi tingkat kesehatan berdasarkan hasil pemeriksaan 4 aspek Permenkop No. 9/2020.
                        </p>
                    </div>

                    <div class="flex-1 flex items-center justify-center py-4">
                        <apexchart 
                            width="100%" 
                            type="donut" 
                            :options="donutChartOptions" 
                            :series="donutSeries"
                        />
                    </div>
                </div>

                <!-- Bar Chart: Kepatuhan RAT & Kesehatan per Wilayah -->
                <div class="lg:col-span-2 p-6 rounded-2xl bg-slate-900 border border-slate-800/80 shadow-xl flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-sm font-bold text-slate-200 uppercase tracking-wider flex items-center gap-2">
                                <Building2 class="w-4 h-4 text-emerald-400" />
                                Sebaran Koperasi & Kesehatan per Kabupaten/Kota
                            </h2>
                        </div>
                        <p class="text-xs text-slate-400 mb-2">
                            Perbandingan Koperasi Predikat Sehat/Cukup Sehat terhadap Total Koperasi di 12 Wilayah Terbesar Sumut.
                        </p>
                    </div>

                    <div class="flex-1 py-2">
                        <apexchart 
                            height="300" 
                            type="bar" 
                            :options="barChartOptions" 
                            :series="barSeries"
                        />
                    </div>
                </div>
            </div>

            <!-- CRITICAL ALERTS & SLA NOTIFICATION CARDS -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Alert Card 1: Koperasi Terlambat / Belum RAT -->
                <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800/80 shadow-xl space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold text-slate-200 uppercase tracking-wider flex items-center gap-2">
                            <Clock class="w-4 h-4 text-amber-400" />
                            Koperasi Belum / Terlambat RAT (TB 2024)
                        </h3>
                        <Link href="/rat" class="text-xs font-semibold text-emerald-400 hover:underline flex items-center gap-1">
                            Lihat Semua <ChevronRight class="w-3.5 h-3.5" />
                        </Link>
                    </div>

                    <div class="divide-y divide-slate-800/60">
                        <div 
                            v-for="item in alerts.belum_rat" 
                            :key="item.id"
                            class="py-3 flex items-center justify-between gap-4 group"
                        >
                            <div>
                                <h4 class="text-xs font-bold text-slate-200 group-hover:text-emerald-400 transition">
                                    {{ item.nama_koperasi }}
                                </h4>
                                <div class="text-[11px] text-slate-400 flex items-center gap-2 mt-0.5">
                                    <span>{{ item.kabupaten_kota }}</span>
                                    <span>•</span>
                                    <span class="text-slate-400 font-mono">{{ item.no_badan_hukum }}</span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/30 whitespace-nowrap">
                                Belum RAT
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Alert Card 2: Temuan Audit Emergency (Risiko Kritis / Tinggi) -->
                <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800/80 shadow-xl space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold text-slate-200 uppercase tracking-wider flex items-center gap-2">
                            <AlertCircle class="w-4 h-4 text-rose-400" />
                            Temuan Audit Risiko Tinggi / Kritis (Open)
                        </h3>
                        <Link href="/temuan" class="text-xs font-semibold text-emerald-400 hover:underline flex items-center gap-1">
                            Lihat Matriks <ChevronRight class="w-3.5 h-3.5" />
                        </Link>
                    </div>

                    <div class="divide-y divide-slate-800/60">
                        <div 
                            v-for="item in alerts.temuan_emergency" 
                            :key="item.id"
                            class="py-3 flex items-center justify-between gap-4 group"
                        >
                            <div>
                                <h4 class="text-xs font-bold text-slate-200 group-hover:text-emerald-400 transition">
                                    {{ item.koperasi?.nama_koperasi }}
                                </h4>
                                <p class="text-[11px] text-slate-400 line-clamp-1 mt-0.5">
                                    {{ item.deskripsi_temuan }}
                                </p>
                            </div>
                            <span 
                                class="px-2.5 py-1 rounded-full text-[10px] font-semibold border whitespace-nowrap"
                                :class="getRiskBadge(item.tingkat_risiko)"
                            >
                                {{ item.tingkat_risiko }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
