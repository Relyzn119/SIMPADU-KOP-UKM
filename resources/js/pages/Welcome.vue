<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { AlertTriangle, ArrowRight, Award, Building2, Clock, FileSpreadsheet, ShieldCheck } from 'lucide-vue-next';

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

    <div
        class="flex min-h-screen flex-col justify-between bg-[#f3f4f6] font-['Plus_Jakarta_Sans',sans-serif] text-gray-900 antialiased selection:bg-emerald-500 selection:text-white"
    >
        <!-- FLOATING NAVBAR HEADER -->
        <div class="sticky top-0 z-50 mx-auto w-full max-w-7xl px-4 pt-4 sm:px-6 lg:px-8">
            <header
                class="shadow-2xs flex items-center justify-between rounded-full border border-gray-200/80 bg-white/90 px-6 py-3 backdrop-blur-md transition"
            >
                <!-- LOGO & BRAND -->
                <div class="flex items-center gap-3">
                    <img src="/images/icon-provsu.svg" alt="Provsu Emblem" class="h-8 w-8 object-contain" />
                    <div>
                        <div class="flex items-center gap-1.5 text-sm font-extrabold tracking-tight text-gray-900 sm:text-base">
                            SIMPADU <span class="text-emerald-600">KOP-UKM</span>
                        </div>
                        <div class="hidden text-[10px] font-semibold uppercase tracking-wider text-gray-500 sm:block">
                            Dinas Koperasi & UKM Prov. Sumatera Utara
                        </div>
                    </div>
                </div>

                <!-- NAV ACTIONS -->
                <nav class="flex items-center gap-2">
                    <template v-if="$page.props.auth?.user">
                        <Link
                            :href="route('dashboard')"
                            class="shadow-xs inline-flex items-center gap-2 rounded-full bg-slate-900 px-4 py-2 text-xs font-bold text-white transition hover:bg-slate-800"
                        >
                            Ke Dashboard
                            <ArrowRight class="h-3.5 w-3.5 text-emerald-400" />
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="rounded-full px-4 py-2 text-xs font-bold text-gray-700 transition hover:bg-gray-100 hover:text-gray-900"
                        >
                            Masuk
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="shadow-xs rounded-full bg-slate-900 px-4 py-2 text-xs font-bold text-white transition hover:bg-slate-800"
                        >
                            Daftar Koperasi
                        </Link>
                    </template>
                </nav>
            </header>
        </div>

        <!-- MAIN HERO CONTENT -->
        <main class="mx-auto my-auto w-full max-w-7xl space-y-12 px-4 py-12 sm:px-6 lg:px-8">
            <!-- HERO BANNER SECTION -->
            <div class="mx-auto max-w-3xl space-y-6 pt-6 text-center">
                <!-- BADGE -->
                <div
                    class="shadow-2xs inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-4 py-1.5 text-xs font-bold text-emerald-700"
                >
                    <ShieldCheck class="h-4 w-4 text-emerald-600" />
                    Sistem Terintegrasi Permenkop UKM No. 9 Tahun 2020
                </div>

                <!-- MAIN HEADLINE -->
                <h1 class="text-3xl font-extrabold leading-tight tracking-tight text-gray-900 sm:text-5xl">
                    Pengawasan & Kesehatan Koperasi Terpadu <span class="text-emerald-600 underline decoration-emerald-300">Sumatera Utara</span>
                </h1>

                <p class="text-sm font-medium leading-relaxed text-gray-600 sm:text-base">
                    Platform digital resmi Dinas Koperasi & UKM Provinsi Sumatera Utara untuk pembinaan kelembagaan, pelaporan RAT tepat waktu, serta
                    penilaian otomatis 4 aspek kesehatan koperasi.
                </p>

                <!-- HERO BUTTONS -->
                <div class="flex flex-wrap items-center justify-center gap-4 pt-2">
                    <Link
                        v-if="$page.props.auth?.user"
                        :href="route('dashboard')"
                        class="flex items-center gap-2 rounded-2xl bg-slate-900 px-6 py-3.5 text-xs font-bold text-white shadow-md transition hover:bg-slate-800 sm:text-sm"
                    >
                        Akses Dashboard Terpadu
                        <ArrowRight class="h-4 w-4 text-emerald-400" />
                    </Link>
                    <Link
                        v-else
                        :href="route('login')"
                        class="flex items-center gap-2 rounded-2xl bg-slate-900 px-6 py-3.5 text-xs font-bold text-white shadow-md transition hover:bg-slate-800 sm:text-sm"
                    >
                        Masuk Sistem SIMPADU
                        <ArrowRight class="h-4 w-4 text-emerald-400" />
                    </Link>

                    <a
                        href="#fitur"
                        class="shadow-2xs rounded-2xl border border-gray-200 bg-white px-6 py-3.5 text-xs font-bold text-gray-800 transition hover:bg-gray-50 sm:text-sm"
                    >
                        Pelajari Modul Utama
                    </a>
                </div>
            </div>

            <!-- LIVE METRICS GRID CARDS -->
            <div class="grid grid-cols-2 gap-4 pt-4 sm:gap-6 lg:grid-cols-4">
                <div class="shadow-2xs space-y-2 rounded-3xl border border-gray-200/70 bg-white p-6 text-center">
                    <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-50 font-bold text-indigo-600">
                        <Building2 class="h-5 w-5" />
                    </div>
                    <div class="font-mono text-2xl font-extrabold text-gray-900 sm:text-3xl">1,248</div>
                    <div class="text-xs font-bold uppercase tracking-wider text-gray-500">Koperasi Binaan</div>
                </div>

                <div class="shadow-2xs space-y-2 rounded-3xl border border-gray-200/70 bg-white p-6 text-center">
                    <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-2xl bg-teal-50 font-bold text-teal-600">
                        <FileSpreadsheet class="h-5 w-5" />
                    </div>
                    <div class="font-mono text-2xl font-extrabold text-gray-900 sm:text-3xl">33</div>
                    <div class="text-xs font-bold uppercase tracking-wider text-gray-500">Kabupaten / Kota</div>
                </div>

                <div class="shadow-2xs space-y-2 rounded-3xl border border-gray-200/70 bg-white p-6 text-center">
                    <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-50 font-bold text-emerald-600">
                        <Clock class="h-5 w-5" />
                    </div>
                    <div class="font-mono text-2xl font-extrabold text-emerald-700 sm:text-3xl">94.2%</div>
                    <div class="text-xs font-bold uppercase tracking-wider text-gray-500">Kepatuhan RAT</div>
                </div>

                <div class="shadow-2xs space-y-2 rounded-3xl border border-gray-200/70 bg-white p-6 text-center">
                    <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-50 font-bold text-amber-600">
                        <Award class="h-5 w-5" />
                    </div>
                    <div class="font-mono text-2xl font-extrabold text-gray-900 sm:text-3xl">82.5</div>
                    <div class="text-xs font-bold uppercase tracking-wider text-gray-500">Rata-rata Skor Health</div>
                </div>
            </div>

            <!-- FEATURES OVERVIEW SECTION -->
            <div id="fitur" class="space-y-6 pt-8">
                <div class="space-y-1 text-center">
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900">4 Pilar Modul Pengawasan Digital</h2>
                    <p class="text-xs font-medium text-gray-500">Solusi menyeluruh dari pembinaan kelembagaan hingga audit tindak lanjut.</p>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Feature 1 -->
                    <div class="shadow-2xs space-y-3 rounded-3xl border border-gray-200/70 bg-white p-6">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-50 font-bold text-blue-600">
                            <Building2 class="h-5 w-5" />
                        </div>
                        <h3 class="text-base font-bold text-gray-900">1. Pendataan Kelembagaan & Pengurus</h3>
                        <p class="text-xs leading-relaxed text-gray-600">
                            Database terpusat profil koperasi, No. Badan Hukum, susunan pengurus & pengawas, domisili wilayah 33 Kab/Kota, serta
                            riwayat perkembangan aset secara transparan.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="shadow-2xs space-y-3 rounded-3xl border border-gray-200/70 bg-white p-6">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-50 font-bold text-emerald-600">
                            <ShieldCheck class="h-5 w-5" />
                        </div>
                        <h3 class="text-base font-bold text-gray-900">2. Engine Penilaian Kesehatan 4 Aspek</h3>
                        <p class="text-xs leading-relaxed text-gray-600">
                            Penilaian otomatis berstandar Permenkop UKM No. 9/2020 mencakup Tata Kelola (30%), Profil Risiko (25%), Kinerja Keuangan
                            (30%), dan Permodalan (15%) secara instant.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="shadow-2xs space-y-3 rounded-3xl border border-gray-200/70 bg-white p-6">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-teal-50 font-bold text-teal-600">
                            <FileSpreadsheet class="h-5 w-5" />
                        </div>
                        <h3 class="text-base font-bold text-gray-900">3. Monitoring & Pelaporan RAT</h3>
                        <p class="text-xs leading-relaxed text-gray-600">
                            Pantau kepatuhan pelaksanaan Rapat Anggota Tahunan sesuai batas waktu undang-undang (30 Juni), verifikasi kuorum anggota,
                            dan upload dokumen LPJ.
                        </p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="shadow-2xs space-y-3 rounded-3xl border border-gray-200/70 bg-white p-6">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-50 font-bold text-amber-600">
                            <AlertTriangle class="h-5 w-5" />
                        </div>
                        <h3 class="text-base font-bold text-gray-900">4. Matriks Temuan Audit & Action Plan</h3>
                        <p class="text-xs leading-relaxed text-gray-600">
                            Matriks temuan pemeriksaan berbasis tingkat risiko (Kritis, Tinggi, Sedang, Rendah), pelaporan tanggapan action plan
                            pengurus, dan approval pengawas.
                        </p>
                    </div>
                </div>
            </div>
        </main>

        <!-- FOOTER SECTION -->
        <footer class="mt-12 border-t border-gray-200/80 bg-white px-4 py-8 sm:px-6 lg:px-8">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 text-xs font-medium text-gray-500 sm:flex-row">
                <div class="flex items-center gap-3">
                    <img src="/images/icon-provsu.svg" alt="Provsu Emblem" class="h-6 w-6 object-contain" />
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
