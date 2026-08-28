<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ShieldCheck,
    Building2,
    FileSpreadsheet,
    AlertTriangle,
    ArrowRight,
    CheckCircle2,
    BarChart3,
    Award,
    Clock,
    FileText,
    Users
} from 'lucide-vue-next';

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
    laravelVersion?: string;
    phpVersion?: string;
}>();
</script>

<template>
    <Head title="SIMPADU KOP-UKM - Portal Pelaporan & Pengawasan Koperasi Provsu">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous" />
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    </Head>

    <div class="min-h-screen bg-[#f3f4f6] text-gray-900 font-['Plus_Jakarta_Sans',sans-serif] antialiased selection:bg-emerald-500 selection:text-white flex flex-col justify-between">
        
        <!-- FLOATING NAVBAR HEADER -->
        <div class="sticky top-0 z-50 pt-4 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
            <header class="bg-white/90 backdrop-blur-md border border-gray-200/80 rounded-full px-6 py-3 shadow-2xs flex items-center justify-between transition">
                <!-- LOGO & BRAND -->
                <div class="flex items-center gap-3">
                    <img src="/images/icon-provsu.svg" alt="Provsu Emblem" class="w-8 h-8 object-contain" />
                    <div>
                        <div class="font-extrabold text-sm sm:text-base tracking-tight text-gray-900 flex items-center gap-1.5">
                            SIMPADU <span class="text-emerald-600">KOP-UKM</span>
                        </div>
                        <div class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider hidden sm:block">
                            Dinas Koperasi & UKM Prov. Sumatera Utara
                        </div>
                    </div>
                </div>

                <!-- NAV ACTIONS -->
                <nav class="flex items-center gap-2">
                    <template v-if="$page.props.auth?.user">
                        <Link
                            :href="route('dashboard')"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-xs transition"
                        >
                            Ke Dashboard
                            <ArrowRight class="w-3.5 h-3.5 text-emerald-400" />
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="px-4 py-2 rounded-full text-xs font-bold text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition"
                        >
                            Masuk
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="px-4 py-2 rounded-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-xs transition"
                        >
                            Daftar Koperasi
                        </Link>
                    </template>
                </nav>
            </header>
        </div>

        <!-- MAIN HERO CONTENT -->
        <main class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full space-y-12 my-auto">
            
            <!-- HERO BANNER SECTION -->
            <div class="text-center space-y-6 max-w-3xl mx-auto pt-6">
                <!-- BADGE -->
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold shadow-2xs">
                    <ShieldCheck class="w-4 h-4 text-emerald-600" />
                    Sistem Terintegrasi Permenkop UKM No. 9 Tahun 2020
                </div>

                <!-- MAIN HEADLINE -->
                <h1 class="text-3xl sm:text-5xl font-extrabold text-gray-900 tracking-tight leading-tight">
                    Pengawasan & Kesehatan Koperasi Terpadu <span class="text-emerald-600 underline decoration-emerald-300">Sumatera Utara</span>
                </h1>

                <p class="text-sm sm:text-base text-gray-600 leading-relaxed font-medium">
                    Platform digital resmi Dinas Koperasi & UKM Provinsi Sumatera Utara untuk pembinaan kelembagaan, pelaporan RAT tepat waktu, serta penilaian otomatis 4 aspek kesehatan koperasi.
                </p>

                <!-- HERO BUTTONS -->
                <div class="flex flex-wrap items-center justify-center gap-4 pt-2">
                    <Link
                        v-if="$page.props.auth?.user"
                        :href="route('dashboard')"
                        class="px-6 py-3.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs sm:text-sm shadow-md transition flex items-center gap-2"
                    >
                        Akses Dashboard Terpadu
                        <ArrowRight class="w-4 h-4 text-emerald-400" />
                    </Link>
                    <Link
                        v-else
                        :href="route('login')"
                        class="px-6 py-3.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs sm:text-sm shadow-md transition flex items-center gap-2"
                    >
                        Masuk Sistem SIMPADU
                        <ArrowRight class="w-4 h-4 text-emerald-400" />
                    </Link>

                    <a 
                        href="#fitur" 
                        class="px-6 py-3.5 rounded-2xl bg-white hover:bg-gray-50 border border-gray-200 text-gray-800 font-bold text-xs sm:text-sm shadow-2xs transition"
                    >
                        Pelajari Modul Utama
                    </a>
                </div>
            </div>

            <!-- LIVE METRICS GRID CARDS -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 pt-4">
                <div class="p-6 rounded-3xl bg-white border border-gray-200/70 shadow-2xs text-center space-y-2">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto font-bold">
                        <Building2 class="w-5 h-5" />
                    </div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-gray-900 font-mono">1,248</div>
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Koperasi Binaan</div>
                </div>

                <div class="p-6 rounded-3xl bg-white border border-gray-200/70 shadow-2xs text-center space-y-2">
                    <div class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center mx-auto font-bold">
                        <FileSpreadsheet class="w-5 h-5" />
                    </div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-gray-900 font-mono">33</div>
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Kabupaten / Kota</div>
                </div>

                <div class="p-6 rounded-3xl bg-white border border-gray-200/70 shadow-2xs text-center space-y-2">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto font-bold">
                        <Clock class="w-5 h-5" />
                    </div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-emerald-700 font-mono">94.2%</div>
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Kepatuhan RAT</div>
                </div>

                <div class="p-6 rounded-3xl bg-white border border-gray-200/70 shadow-2xs text-center space-y-2">
                    <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto font-bold">
                        <Award class="w-5 h-5" />
                    </div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-gray-900 font-mono">82.5</div>
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Rata-rata Skor Health</div>
                </div>
            </div>

            <!-- FEATURES OVERVIEW SECTION -->
            <div id="fitur" class="space-y-6 pt-8">
                <div class="text-center space-y-1">
                    <h2 class="text-2xl font-bold text-gray-900 tracking-tight">4 Pilar Modul Pengawasan Digital</h2>
                    <p class="text-xs text-gray-500 font-medium">Solusi menyeluruh dari pembinaan kelembagaan hingga audit tindak lanjut.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Feature 1 -->
                    <div class="p-6 rounded-3xl bg-white border border-gray-200/70 shadow-2xs space-y-3">
                        <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                            <Building2 class="w-5 h-5" />
                        </div>
                        <h3 class="text-base font-bold text-gray-900">1. Pendataan Kelembagaan & Pengurus</h3>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            Database terpusat profil koperasi, No. Badan Hukum, susunan pengurus & pengawas, domisili wilayah 33 Kab/Kota, serta riwayat perkembangan aset secara transparan.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="p-6 rounded-3xl bg-white border border-gray-200/70 shadow-2xs space-y-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                            <ShieldCheck class="w-5 h-5" />
                        </div>
                        <h3 class="text-base font-bold text-gray-900">2. Engine Penilaian Kesehatan 4 Aspek</h3>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            Penilaian otomatis berstandar Permenkop UKM No. 9/2020 mencakup Tata Kelola (30%), Profil Risiko (25%), Kinerja Keuangan (30%), dan Permodalan (15%) secara instant.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="p-6 rounded-3xl bg-white border border-gray-200/70 shadow-2xs space-y-3">
                        <div class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center font-bold">
                            <FileSpreadsheet class="w-5 h-5" />
                        </div>
                        <h3 class="text-base font-bold text-gray-900">3. Monitoring & Pelaporan RAT</h3>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            Pantau kepatuhan pelaksanaan Rapat Anggota Tahunan sesuai batas waktu undang-undang (30 Juni), verifikasi kuorum anggota, dan upload dokumen LPJ.
                        </p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="p-6 rounded-3xl bg-white border border-gray-200/70 shadow-2xs space-y-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                            <AlertTriangle class="w-5 h-5" />
                        </div>
                        <h3 class="text-base font-bold text-gray-900">4. Matriks Temuan Audit & Action Plan</h3>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            Matriks temuan pemeriksaan berbasis tingkat risiko (Kritis, Tinggi, Sedang, Rendah), pelaporan tanggapan action plan pengurus, dan approval pengawas.
                        </p>
                    </div>
                </div>
            </div>
        </main>

        <!-- FOOTER SECTION -->
        <footer class="bg-white border-t border-gray-200/80 py-8 px-4 sm:px-6 lg:px-8 mt-12">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-gray-500 font-medium">
                <div class="flex items-center gap-3">
                    <img src="/images/icon-provsu.svg" alt="Provsu Emblem" class="w-6 h-6 object-contain" />
                    <span>© 2026 Pemerintah Provinsi Sumatera Utara. Dinas Koperasi dan UKM.</span>
                </div>
                <div class="flex items-center gap-4">
                    <span>Jl. Gatot Subroto No. 222 Medan</span>
                    <span>•</span>
                    <span>Telepon: (061) 8452110</span>
                </div>
            </div>
        </footer>
    </div>
</template>
