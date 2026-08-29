<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { FileText, MoreVertical } from 'lucide-vue-next';
import { computed } from 'vue';

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

// Spline Line Chart Configuration (Revenue over time style)
const lineChartOptions = computed(() => ({
    chart: {
        type: 'area',
        toolbar: { show: false },
        background: 'transparent',
        fontFamily: 'Inter, sans-serif',
    },
    colors: ['#10b981', '#cbd5e1'],
    stroke: {
        curve: 'smooth',
        width: [3, 2],
        dashArray: [0, 4],
    },
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.25,
            opacityTo: 0.02,
            stops: [0, 95, 100],
        },
    },
    dataLabels: { enabled: false },
    legend: { show: false },
    xaxis: {
        categories: props.charts.per_wilayah.map((item) => item.kabupaten_kota.replace('Kabupaten ', '').replace('Kota ', '')),
        labels: {
            style: { colors: '#94a3b8', fontSize: '11px', fontWeight: 500 },
        },
        axisBorder: { show: false },
        axisTicks: { show: false },
    },
    yaxis: {
        labels: {
            style: { colors: '#94a3b8', fontSize: '11px' },
        },
    },
    grid: {
        borderColor: '#f1f5f9',
        strokeDashArray: 4,
    },
    tooltip: {
        theme: 'light',
        x: { show: true },
    },
}));

const lineSeries = computed(() => [
    {
        name: 'Koperasi Sehat / Cukup Sehat',
        data: props.charts.per_wilayah.map((item) => item.sehat_cukup),
    },
    {
        name: 'Total Koperasi Binaan',
        data: props.charts.per_wilayah.map((item) => item.total),
    },
]);

// Donut Chart Configuration (Traffic by source style)
const donutChartOptions = computed(() => ({
    chart: {
        type: 'donut',
        background: 'transparent',
        fontFamily: 'Inter, sans-serif',
    },
    labels: ['Sehat', 'Cukup Sehat', 'Dalam Pengawasan', 'Pengawasan Khusus'],
    colors: ['#10b981', '#06b6d4', '#f59e0b', '#f43f5e'],
    stroke: { width: 3, colors: ['#ffffff'] },
    legend: { show: false },
    dataLabels: { enabled: false },
    plotOptions: {
        pie: {
            donut: {
                size: '75%',
                labels: {
                    show: true,
                    total: {
                        show: true,
                        label: 'Total Koperasi',
                        color: '#64748b',
                        fontSize: '11px',
                        fontWeight: 600,
                        formatter: () => props.kpi.total_koperasi,
                    },
                    value: {
                        color: '#0f172a',
                        fontSize: '22px',
                        fontWeight: 800,
                    },
                },
            },
        },
    },
    tooltip: { theme: 'light' },
}));

const donutSeries = computed(() => [
    props.charts.predikat.Sehat || 0,
    props.charts.predikat['Cukup Sehat'] || 0,
    props.charts.predikat['Dalam Pengawasan'] || 0,
    props.charts.predikat['Pengawasan Khusus'] || 0,
]);

const getRiskBadge = (risiko: string) => {
    switch (risiko) {
        case 'Kritis':
            return 'bg-rose-50 text-rose-700 border-rose-200';
        case 'Tinggi':
            return 'bg-amber-50 text-amber-700 border-amber-200';
        case 'Sedang':
            return 'bg-yellow-50 text-yellow-700 border-yellow-200';
        default:
            return 'bg-blue-50 text-blue-700 border-blue-200';
    }
};

const totalPredikatCount = computed(() => {
    return (
        (props.charts.predikat.Sehat || 0) +
        (props.charts.predikat['Cukup Sehat'] || 0) +
        (props.charts.predikat['Dalam Pengawasan'] || 0) +
        (props.charts.predikat['Pengawasan Khusus'] || 0)
    );
});

const getPercentage = (count: number) => {
    if (!totalPredikatCount.value) return 0;
    return Math.round((count / totalPredikatCount.value) * 100);
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Dashboard Administrasi Koperasi" />

        <div class="space-y-6">
            <!-- 1. TOP ROW: 4 SUMMARY KPI CARDS (Matching Skymetrics design) -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <!-- KPI Card 1: Total Koperasi -->
                <div class="shadow-2xs space-y-3 rounded-3xl border border-gray-200/70 bg-white p-5 transition-all duration-200 hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-gray-500">Total Koperasi</span>
                        <span
                            class="inline-flex items-center gap-0.5 rounded-full border border-emerald-100 bg-emerald-50 px-2 py-0.5 text-[11px] font-bold text-emerald-700"
                        >
                            +12.5%
                        </span>
                    </div>

                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="font-mono text-3xl font-extrabold tracking-tight text-gray-900">{{ kpi.total_koperasi }}</span>
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        </div>
                        <p class="mt-1 text-[11px] font-medium text-gray-400">vs {{ kpi.koperasi_aktif }} Koperasi Aktif</p>
                    </div>
                </div>

                <!-- KPI Card 2: Kepatuhan RAT -->
                <div class="shadow-2xs space-y-3 rounded-3xl border border-gray-200/70 bg-white p-5 transition-all duration-200 hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-gray-500">Kepatuhan RAT</span>
                        <span
                            class="inline-flex items-center gap-0.5 rounded-full border border-emerald-100 bg-emerald-50 px-2 py-0.5 text-[11px] font-bold text-emerald-700"
                        >
                            +8.7%
                        </span>
                    </div>

                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="font-mono text-3xl font-extrabold tracking-tight text-gray-900">{{ kpi.kepatuhan_rat_pct }}%</span>
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        </div>
                        <p class="mt-1 text-[11px] font-medium text-gray-400">Target Batas Waktu 30 Juni</p>
                    </div>
                </div>

                <!-- KPI Card 3: Koperasi Diawasi -->
                <div class="shadow-2xs space-y-3 rounded-3xl border border-gray-200/70 bg-white p-5 transition-all duration-200 hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-gray-500">Koperasi Diawasi</span>
                        <span
                            class="inline-flex items-center gap-0.5 rounded-full border border-emerald-100 bg-emerald-50 px-2 py-0.5 text-[11px] font-bold text-emerald-700"
                        >
                            +4.3%
                        </span>
                    </div>

                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="font-mono text-3xl font-extrabold tracking-tight text-gray-900">{{ kpi.total_diawasi }}</span>
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        </div>
                        <p class="mt-1 text-[11px] font-medium text-gray-400">Terverifikasi 4 Aspek Kesehatan</p>
                    </div>
                </div>

                <!-- KPI Card 4: Temuan Belum Selesai -->
                <div class="shadow-2xs space-y-3 rounded-3xl border border-gray-200/70 bg-white p-5 transition-all duration-200 hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-gray-500">Temuan Open</span>
                        <span
                            class="inline-flex items-center gap-0.5 rounded-full border border-rose-100 bg-rose-50 px-2 py-0.5 text-[11px] font-bold text-rose-700"
                        >
                            -3.2%
                        </span>
                    </div>

                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="font-mono text-3xl font-extrabold tracking-tight text-gray-900">{{ kpi.total_temuan_open }}</span>
                            <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                        </div>
                        <p class="mt-1 text-[11px] font-medium text-gray-400">{{ kpi.temuan_kritis_open }} Temuan Risiko Kritis</p>
                    </div>
                </div>
            </div>

            <!-- 2. MIDDLE ROW: CHARTS SECTION (2/3 line chart + 1/3 donut chart) -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Left Chart Card: Revenue over time style -->
                <div class="shadow-2xs flex flex-col justify-between space-y-4 rounded-3xl border border-gray-200/70 bg-white p-6 lg:col-span-2">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-bold tracking-tight text-gray-900">Revenue over time</h2>
                            <p class="text-xs text-gray-400">Tren Kepatuhan & Kesehatan Koperasi per Wilayah Sumut</p>
                        </div>

                        <!-- Legend Items -->
                        <div class="flex items-center gap-4 text-xs font-semibold">
                            <div class="flex items-center gap-1.5">
                                <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                                <span class="text-gray-700">Koperasi Sehat</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="h-2.5 w-2.5 rounded-full bg-slate-300"></span>
                                <span class="text-gray-400">Total Binaan</span>
                            </div>
                        </div>
                    </div>

                    <div class="py-2">
                        <apexchart height="280" type="area" :options="lineChartOptions" :series="lineSeries" />
                    </div>
                </div>

                <!-- Right Chart Card: Traffic by source style (Donut) -->
                <div class="shadow-2xs flex flex-col justify-between space-y-4 rounded-3xl border border-gray-200/70 bg-white p-6">
                    <div>
                        <h2 class="text-base font-bold tracking-tight text-gray-900">Traffic by source</h2>
                        <p class="text-xs text-gray-400">Distribusi Predikat Kesehatan Koperasi</p>
                    </div>

                    <!-- Donut Chart Canvas -->
                    <div class="relative flex items-center justify-center py-2">
                        <apexchart width="100%" type="donut" :options="donutChartOptions" :series="donutSeries" />
                    </div>

                    <!-- Donut Stat Callout Badges -->
                    <div class="grid grid-cols-2 gap-3 border-t border-gray-100 pt-2 text-xs">
                        <div class="space-y-0.5">
                            <div class="flex items-center gap-1.5 font-medium text-gray-500">
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                <span>Sehat</span>
                            </div>
                            <div class="font-bold text-gray-900">
                                {{ charts.predikat.Sehat || 0 }} ({{ getPercentage(charts.predikat.Sehat || 0) }}%)
                            </div>
                        </div>

                        <div class="space-y-0.5">
                            <div class="flex items-center gap-1.5 font-medium text-gray-500">
                                <span class="h-2 w-2 rounded-full bg-cyan-500"></span>
                                <span>Cukup Sehat</span>
                            </div>
                            <div class="font-bold text-gray-900">
                                {{ charts.predikat['Cukup Sehat'] || 0 }} ({{ getPercentage(charts.predikat['Cukup Sehat'] || 0) }}%)
                            </div>
                        </div>

                        <div class="space-y-0.5">
                            <div class="flex items-center gap-1.5 font-medium text-gray-500">
                                <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                                <span>Dalam Pengawasan</span>
                            </div>
                            <div class="font-bold text-gray-900">
                                {{ charts.predikat['Dalam Pengawasan'] || 0 }} ({{ getPercentage(charts.predikat['Dalam Pengawasan'] || 0) }}%)
                            </div>
                        </div>

                        <div class="space-y-0.5">
                            <div class="flex items-center gap-1.5 font-medium text-gray-500">
                                <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                                <span>Pengawasan Khusus</span>
                            </div>
                            <div class="font-bold text-gray-900">
                                {{ charts.predikat['Pengawasan Khusus'] || 0 }} ({{ getPercentage(charts.predikat['Pengawasan Khusus'] || 0) }}%)
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. BOTTOM ROW: 2 TABLES / LISTS (Recent orders & Top products) -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Left Card: Recent orders style (Koperasi Terlambat / Belum RAT) -->
                <div class="shadow-2xs space-y-4 rounded-3xl border border-gray-200/70 bg-white p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold tracking-tight text-gray-900">Recent orders</h3>
                            <p class="text-xs text-gray-400">Daftar Koperasi Belum / Terlambat RAT (TB 2024)</p>
                        </div>
                        <Link href="/rat" class="rounded-xl p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-700">
                            <MoreVertical class="h-4 w-4" />
                        </Link>
                    </div>

                    <!-- Table Header Bar -->
                    <div class="overflow-hidden rounded-2xl border border-gray-100">
                        <table class="w-full text-left text-xs">
                            <thead class="border-b border-gray-100 bg-gray-50/80 text-[10px] font-semibold uppercase tracking-wider text-gray-500">
                                <tr>
                                    <th class="px-4 py-3">Koperasi</th>
                                    <th class="px-4 py-3">Wilayah</th>
                                    <th class="px-4 py-3 text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="item in alerts.belum_rat" :key="item.id" class="group cursor-pointer transition hover:bg-gray-50/60">
                                    <!-- Customer / Koperasi name & avatar -->
                                    <td class="px-4 py-3.5">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-900 text-xs font-bold text-white"
                                            >
                                                {{ item.nama_koperasi.charAt(0) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-900 transition group-hover:text-emerald-600">
                                                    {{ item.nama_koperasi }}
                                                </div>
                                                <div class="font-mono text-[10px] text-gray-400">
                                                    {{ item.no_badan_hukum }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Wilayah -->
                                    <td class="px-4 py-3.5 font-medium text-gray-500">
                                        {{ item.kabupaten_kota }}
                                    </td>

                                    <!-- Status Badge -->
                                    <td class="px-4 py-3.5 text-right">
                                        <span
                                            class="inline-flex rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-[10px] font-bold text-amber-700"
                                        >
                                            Belum RAT
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Right Card: Top products style (Matriks Temuan Audit Kritis) -->
                <div class="shadow-2xs space-y-4 rounded-3xl border border-gray-200/70 bg-white p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold tracking-tight text-gray-900">Top products</h3>
                            <p class="text-xs text-gray-400">Matriks Temuan Audit Risiko Tinggi & Kritis</p>
                        </div>
                        <Link href="/temuan" class="rounded-xl p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-700">
                            <MoreVertical class="h-4 w-4" />
                        </Link>
                    </div>

                    <!-- Table Header Bar -->
                    <div class="overflow-hidden rounded-2xl border border-gray-100">
                        <table class="w-full text-left text-xs">
                            <thead class="border-b border-gray-100 bg-gray-50/80 text-[10px] font-semibold uppercase tracking-wider text-gray-500">
                                <tr>
                                    <th class="px-4 py-3">Temuan & Koperasi</th>
                                    <th class="px-4 py-3 text-center">Tingkat Risiko</th>
                                    <th class="px-4 py-3 text-right">Batas Waktu</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr
                                    v-for="item in alerts.temuan_emergency"
                                    :key="item.id"
                                    class="group cursor-pointer transition hover:bg-gray-50/60"
                                >
                                    <!-- Product / Temuan Icon & Title -->
                                    <td class="px-4 py-3.5">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-slate-900 text-white">
                                                <FileText class="h-4 w-4 text-emerald-400" />
                                            </div>
                                            <div class="max-w-[200px]">
                                                <div class="truncate font-bold text-gray-900 transition group-hover:text-emerald-600">
                                                    {{ item.koperasi?.nama_koperasi }}
                                                </div>
                                                <div class="truncate text-[10px] text-gray-400">
                                                    {{ item.deskripsi_temuan }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Tingkat Risiko -->
                                    <td class="px-4 py-3.5 text-center">
                                        <span
                                            class="inline-flex rounded-full border px-2.5 py-1 text-[10px] font-bold"
                                            :class="getRiskBadge(item.tingkat_risiko)"
                                        >
                                            {{ item.tingkat_risiko }}
                                        </span>
                                    </td>

                                    <!-- Batas Waktu -->
                                    <td class="px-4 py-3.5 text-right font-mono font-bold text-gray-800">
                                        {{ item.batas_waktu || '30 Hari' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
