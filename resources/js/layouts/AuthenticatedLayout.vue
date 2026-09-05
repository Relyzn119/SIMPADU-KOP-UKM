<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import {
    AlertCircle,
    AlertTriangle,
    Bell,
    Building2,
    Calendar,
    CheckCircle2,
    ChevronDown,
    FileSpreadsheet,
    Filter,
    KeyRound,
    LayoutDashboard,
    LogOut,
    Menu,
    Plus,
    Printer,
    ShieldCheck,
    User,
    X,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

const page = usePage();
const auth = computed(() => page.props.auth as any);
const flash = computed(() => page.props.flash as any);

const isMobileMenuOpen = ref(false);
const isProfileDropdownOpen = ref(false);
const isNotificationOpen = ref(false);

const userRole = computed(() => auth.value?.user?.role || 'admin_koperasi');
const userName = computed(() => auth.value?.user?.name || 'Pengguna');
const userNip = computed(() => auth.value?.user?.nip || '-');
const roleLabel = computed(() => auth.value?.role_label || 'Administrasi Koperasi');

// Global Interactive Filter States for Tahun Buku & Wilayah
const urlParams = typeof window !== 'undefined' ? new URLSearchParams(window.location.search) : new URLSearchParams();
const selectedTahunBuku = ref(urlParams.get('tahun_buku') || (page.props.filters as any)?.tahun_buku || '2024');
const selectedKabupatenKota = ref(urlParams.get('kabupaten_kota') || (page.props.filters as any)?.kabupaten_kota || 'semua');

const kabupatenKotaList = computed(() => {
    return (
        (page.props.kabupatenKotaList as string[]) || [
            'Kota Medan',
            'Kota Pematangsiantar',
            'Kota Sibolga',
            'Kota Tanjungbalai',
            'Kota Binjai',
            'Kota Tebing Tinggi',
            'Kota Padangsidimpuan',
            'Kota Gunungsitoli',
            'Kabupaten Deli Serdang',
            'Kabupaten Karo',
            'Kabupaten Simalungun',
            'Kabupaten Asahan',
            'Kabupaten Dairi',
            'Kabupaten Tapanuli Utara',
            'Kabupaten Tapanuli Tengah',
            'Kabupaten Tapanuli Selatan',
            'Kabupaten Langkat',
            'Kabupaten Nias',
            'Kabupaten Labuhanbatu',
            'Kabupaten Toba',
            'Kabupaten Mandailing Natal',
            'Kabupaten Nias Selatan',
            'Kabupaten Pakpak Bharat',
            'Kabupaten Humbang Hasundutan',
            'Kabupaten Samosir',
            'Kabupaten Serdang Bedagai',
            'Kabupaten Batubara',
            'Kabupaten Padang Lawas Utara',
            'Kabupaten Padang Lawas',
            'Kabupaten Labuhanbatu Selatan',
            'Kabupaten Labuhanbatu Utara',
            'Kabupaten Nias Utara',
            'Kabupaten Nias Barat',
        ]
    );
});

const applyGlobalFilters = () => {
    const currentPath = page.url.split('?')[0];
    const query: Record<string, any> = {};

    if (selectedTahunBuku.value && selectedTahunBuku.value !== 'semua') {
        query.tahun_buku = selectedTahunBuku.value;
    }
    if (selectedKabupatenKota.value && selectedKabupatenKota.value !== 'semua') {
        query.kabupaten_kota = selectedKabupatenKota.value;
    }

    router.get(currentPath, query, { preserveState: true, replace: true });
};

// Grouped navigation menu matching the reference image layout
const navGroups = computed(() => [
    {
        name: 'Analytics',
        items: [
            {
                name: 'Overview',
                href: '/dashboard',
                icon: LayoutDashboard,
                roles: ['admin_koperasi', 'bidang_pengawasan'],
            },
        ],
    },
    {
        name: 'Kelembagaan',
        items: [
            {
                name: 'Data Koperasi',
                href: '/koperasi',
                icon: Building2,
                roles: ['admin_koperasi', 'bidang_pengawasan'],
            },
            {
                name: 'Pelaporan RAT',
                href: '/rat',
                icon: FileSpreadsheet,
                roles: ['admin_koperasi', 'bidang_pengawasan'],
            },
        ],
    },
    {
        name: 'Pengawasan',
        items: [
            {
                name: 'Pemeriksaan Kesehatan',
                href: '/pengawasan',
                icon: ShieldCheck,
                roles: ['admin_koperasi', 'bidang_pengawasan'],
            },
            /* Archived menu items:
            {
                name: 'Matriks Temuan Audit',
                href: '/temuan',
                icon: AlertTriangle,
                roles: ['admin_koperasi', 'bidang_pengawasan'],
            },
            {
                name: 'Audit Log Verifikasi',
                href: '/audit-log',
                icon: ShieldCheck,
                roles: ['admin_koperasi', 'bidang_pengawasan'],
            },
            */
        ],
    },
    {
        name: 'System & Output',
        items: [
            {
                name: 'Cetak Laporan',
                href: '/cetak',
                icon: Printer,
                roles: ['admin_koperasi', 'bidang_pengawasan'],
            },
        ],
    },
]);

const isCurrentRoute = (url: string) => {
    if (url === '/dashboard') {
        return page.url === '/dashboard' || page.url === '/';
    }
    return page.url.startsWith(url);
};

const handleLogout = () => {
    router.post('/logout');
};
</script>

<template>
    <div class="flex min-h-screen flex-col bg-[#f3f4f6] font-sans text-gray-800 antialiased selection:bg-emerald-500 selection:text-white">
        <!-- MOBILE HEADER BAR -->
        <header
            class="shadow-xs sticky top-0 z-40 flex items-center justify-between border-b border-gray-200/80 bg-white/95 px-4 py-3 backdrop-blur-md md:hidden print:hidden"
        >
            <div class="flex items-center gap-3">
                <button
                    @click="isMobileMenuOpen = !isMobileMenuOpen"
                    class="rounded-xl p-2 text-gray-600 transition hover:bg-gray-100 hover:text-gray-900"
                    aria-label="Toggle Navigation"
                >
                    <X v-if="isMobileMenuOpen" class="h-6 w-6" />
                    <Menu v-else class="h-6 w-6" />
                </button>

                <Link href="/dashboard" class="flex items-center gap-2.5">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center p-0">
                        <img src="/images/icon-provsu.svg" alt="Provsu emblem" class="h-full w-full object-contain" />
                    </div>
                    <span class="text-sm font-bold tracking-tight text-gray-900">SIMPADU KOP</span>
                </Link>
            </div>

            <div class="flex items-center gap-2">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-900 text-xs font-bold text-white">
                    {{ userName.charAt(0) }}
                </div>
            </div>
        </header>

        <!-- MAIN APP WRAPPER (FLOATING CARD LAYOUT) -->
        <div class="relative mx-auto flex w-full max-w-[1600px] flex-1 gap-5 p-3 sm:p-4 md:p-6 print:m-0 print:max-w-full print:p-0">
            <!-- MOBILE SIDEBAR OVERLAY -->
            <div
                v-if="isMobileMenuOpen"
                @click="isMobileMenuOpen = false"
                class="backdrop-blur-xs fixed inset-0 z-40 bg-gray-900/40 transition-opacity md:hidden print:hidden"
            ></div>

            <!-- FLOATING SIDEBAR (Desktop & Mobile Drawer) -->
            <aside
                class="md:shadow-xs fixed inset-y-0 left-0 z-50 flex w-64 shrink-0 flex-col justify-between rounded-none border border-gray-200/80 bg-white p-4 shadow-lg transition-transform duration-300 md:static md:sticky md:top-6 md:h-[calc(100vh-3rem)] md:translate-x-0 md:rounded-3xl print:hidden"
                :class="isMobileMenuOpen ? 'translate-x-0' : '-translate-x-full'"
            >
                <!-- TOP SIDEBAR BRANDING & NAV LIST -->
                <div class="custom-scrollbar flex-1 space-y-6 overflow-y-auto pr-1">
                    <!-- BRAND LOGO HEADER -->
                    <div class="flex items-center gap-3 px-2 pt-1">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center p-0">
                            <img src="/images/icon-provsu.svg" alt="Provsu Emblem" class="h-full w-full object-contain" />
                        </div>
                        <div>
                            <div class="text-base font-extrabold leading-tight tracking-tight text-gray-900">SIMPADU KOP</div>
                            <div class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Prov. Sumut</div>
                        </div>
                    </div>

                    <!-- NAVIGATION GROUPS -->
                    <nav class="space-y-5">
                        <div v-for="group in navGroups" :key="group.name" class="space-y-1.5">
                            <!-- Category Section Title -->
                            <div class="px-3 text-[11px] font-semibold uppercase tracking-wide text-gray-400">
                                {{ group.name }}
                            </div>

                            <!-- Category Menu Items -->
                            <div class="space-y-1">
                                <template v-for="item in group.items" :key="item.name">
                                    <Link
                                        v-if="item.roles.includes(userRole)"
                                        :href="item.href"
                                        @click="isMobileMenuOpen = false"
                                        class="group flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-xs transition-all duration-150 sm:text-sm"
                                        :class="
                                            isCurrentRoute(item.href)
                                                ? 'shadow-2xs bg-gray-100 font-semibold text-gray-900'
                                                : 'font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-900'
                                        "
                                    >
                                        <component
                                            :is="item.icon"
                                            class="h-4 w-4 transition-transform duration-200 group-hover:scale-110"
                                            :class="isCurrentRoute(item.href) ? 'text-gray-900' : 'text-gray-400 group-hover:text-gray-700'"
                                        />
                                        <span>{{ item.name }}</span>
                                    </Link>
                                </template>
                            </div>
                        </div>
                    </nav>
                </div>

                <!-- SIDEBAR FOOTER & CTA -->
                <div class="shrink-0 space-y-3 border-t border-gray-100 pt-4">
                    <div class="text-center text-[11px] font-medium text-gray-400">Version 2.0 • Provsu</div>
                </div>
            </aside>

            <!-- MAIN CONTENT AREA -->
            <div class="flex min-w-0 flex-1 flex-col space-y-5 print:m-0 print:space-y-0 print:p-0">
                <!-- TOP HEADER NAVBAR (DESKTOP) -->
                <header class="hidden items-center justify-between py-1 md:flex print:hidden">
                    <!-- Page Title / Overview -->
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900">Overview</h1>
                        <p class="mt-0.5 text-xs text-gray-500">Sistem Pengawasan & Administrasi Kelembagaan Koperasi</p>
                    </div>

                    <!-- Top Bar Action Controls (Filters, Notifications, Profile) -->
                    <div class="flex items-center gap-3">
                        <!-- Date Filter Select Dropdown -->
                        <div class="relative flex items-center">
                            <Calendar class="pointer-events-none absolute left-3.5 z-10 h-3.5 w-3.5 text-gray-500" />
                            <select
                                v-model="selectedTahunBuku"
                                @change="applyGlobalFilters"
                                class="shadow-2xs cursor-pointer appearance-none rounded-xl border border-gray-200/80 bg-white py-2 pl-9 pr-8 text-xs font-semibold text-gray-700 transition hover:bg-gray-50 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                            >
                                <option value="semua">Semua Tahun Buku</option>
                                <option value="2026">Tahun Buku 2026</option>
                                <option value="2025">Tahun Buku 2025</option>
                                <option value="2024">Tahun Buku 2024</option>
                                <option value="2023">Tahun Buku 2023</option>
                                <option value="2022">Tahun Buku 2022</option>
                            </select>
                            <ChevronDown class="pointer-events-none absolute right-3 z-10 h-3.5 w-3.5 text-gray-400" />
                        </div>

                        <!-- Region / Brand Filter Select Dropdown -->
                        <div class="relative flex items-center">
                            <Filter class="pointer-events-none absolute left-3.5 z-10 h-3.5 w-3.5 text-gray-500" />
                            <select
                                v-model="selectedKabupatenKota"
                                @change="applyGlobalFilters"
                                class="shadow-2xs max-w-[210px] cursor-pointer appearance-none truncate rounded-xl border border-gray-200/80 bg-white py-2 pl-9 pr-8 text-xs font-semibold text-gray-700 transition hover:bg-gray-50 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
                            >
                                <option value="semua">Semua Wilayah</option>
                                <option v-for="kab in kabupatenKotaList" :key="kab" :value="kab">{{ kab }}</option>
                            </select>
                            <ChevronDown class="pointer-events-none absolute right-3 z-10 h-3.5 w-3.5 text-gray-400" />
                        </div>

                        <!-- Notification Bell Button -->
                        <div class="relative">
                            <button
                                @click="isNotificationOpen = !isNotificationOpen"
                                class="shadow-2xs relative rounded-xl border border-gray-200/80 bg-white p-2 text-gray-600 transition hover:bg-gray-50 hover:text-gray-900"
                                aria-label="Notifications"
                            >
                                <Bell class="h-4 w-4" />
                                <span class="absolute right-1.5 top-1.5 h-2 w-2 rounded-full bg-emerald-500 ring-2 ring-white"></span>
                            </button>

                            <!-- Notification Dropdown -->
                            <div
                                v-if="isNotificationOpen"
                                @click="isNotificationOpen = false"
                                class="absolute right-0 z-50 mt-2 w-72 space-y-2 rounded-2xl border border-gray-200 bg-white p-3 text-xs shadow-xl"
                            >
                                <div class="flex items-center justify-between border-b border-gray-100 pb-2 font-bold text-gray-900">
                                    <span>Notifikasi Sistem</span>
                                    <span class="rounded-md bg-emerald-50 px-2 py-0.5 text-[10px] font-semibold text-emerald-600">2 Baru</span>
                                </div>
                                <div class="cursor-pointer rounded-xl bg-gray-50 p-2 transition hover:bg-gray-100">
                                    <div class="font-semibold text-gray-800">Verifikasi RAT Selesai</div>
                                    <div class="mt-0.5 text-[10px] text-gray-400">KSP Sahabat Jaya • 10 menit lalu</div>
                                </div>
                                <div class="cursor-pointer rounded-xl bg-gray-50 p-2 transition hover:bg-gray-100">
                                    <div class="font-semibold text-gray-800">Alert Temuan Kritis</div>
                                    <div class="mt-0.5 text-[10px] text-gray-400">Koperasi Makmur Bersama • 1 jam lalu</div>
                                </div>
                            </div>
                        </div>

                        <!-- User Profile Pill -->
                        <div class="relative">
                            <button
                                @click="isProfileDropdownOpen = !isProfileDropdownOpen"
                                class="shadow-2xs flex items-center gap-2.5 rounded-full border border-gray-200/80 bg-white p-1 pl-1.5 pr-2.5 transition hover:bg-gray-50"
                            >
                                <div
                                    class="shadow-2xs flex h-7 w-7 items-center justify-center rounded-full bg-slate-900 text-xs font-bold text-white"
                                >
                                    {{ userName.charAt(0) }}
                                </div>
                                <span class="max-w-[120px] truncate text-xs font-bold text-gray-800">{{ userName }}</span>
                                <ChevronDown
                                    class="h-3.5 w-3.5 text-gray-400 transition-transform"
                                    :class="{ 'rotate-180': isProfileDropdownOpen }"
                                />
                            </button>

                            <!-- Profile Dropdown -->
                            <div
                                v-if="isProfileDropdownOpen"
                                @click="isProfileDropdownOpen = false"
                                class="absolute right-0 z-50 mt-2 w-56 rounded-2xl border border-gray-200 bg-white py-2 shadow-xl duration-150 animate-in fade-in"
                            >
                                <div class="border-b border-gray-100 px-4 py-2">
                                    <p class="text-xs font-bold text-gray-900">{{ userName }}</p>
                                    <p class="mt-0.5 text-[10px] font-semibold text-emerald-600">{{ roleLabel }}</p>
                                    <p class="mt-0.5 text-[10px] text-gray-400">NIP: {{ userNip }}</p>
                                </div>
                                <div class="space-y-0.5 border-b border-gray-100 py-1">
                                    <Link
                                        href="/settings/profile"
                                        class="flex w-full items-center gap-2 px-4 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50"
                                    >
                                        <User class="h-3.5 w-3.5 text-gray-400" />
                                        <span>Profil Pengguna</span>
                                    </Link>
                                    <Link
                                        href="/settings/password"
                                        class="flex w-full items-center gap-2 px-4 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50"
                                    >
                                        <KeyRound class="h-3.5 w-3.5 text-gray-400" />
                                        <span>Ubah Password</span>
                                    </Link>
                                </div>
                                <button
                                    @click="handleLogout"
                                    class="flex w-full items-center gap-2 px-4 py-2.5 text-xs font-medium text-rose-600 transition hover:bg-rose-50"
                                >
                                    <LogOut class="h-4 w-4" />
                                    Keluar Aplikasi (Logout)
                                </button>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- FLASH NOTIFICATIONS -->
                <div
                    v-if="flash?.success"
                    class="shadow-2xs flex items-center justify-between rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 print:hidden"
                >
                    <div class="flex items-center gap-3">
                        <CheckCircle2 class="h-5 w-5 flex-shrink-0 text-emerald-600" />
                        <span class="text-xs font-semibold">{{ flash.success }}</span>
                    </div>
                </div>

                <div
                    v-if="flash?.error"
                    class="shadow-2xs flex items-center justify-between rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-800 print:hidden"
                >
                    <div class="flex items-center gap-3">
                        <AlertCircle class="h-5 w-5 flex-shrink-0 text-rose-600" />
                        <span class="text-xs font-semibold">{{ flash.error }}</span>
                    </div>
                </div>

                <!-- MAIN PAGE CONTENT SLOT -->
                <main class="flex-1">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 9999px;
}
</style>

<style>
@media print {
    @page {
        size: A4 portrait;
        margin: 10mm 12mm;
    }
    header,
    aside,
    nav,
    button,
    .print\:hidden {
        display: none !important;
    }
    body,
    html,
    #app,
    .min-h-screen {
        background: #ffffff !important;
        color: #000000 !important;
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
    }
    .flex-1,
    .p-3,
    .sm\:p-4,
    .md\:p-6,
    .gap-5,
    .space-y-5,
    .space-y-6 {
        padding: 0 !important;
        margin: 0 !important;
        gap: 0 !important;
        max-width: 100% !important;
        width: 100% !important;
    }
    .print\:p-0 {
        padding: 0 !important;
        margin: 0 !important;
        box-shadow: none !important;
        border: none !important;
        border-radius: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
    }
    table {
        width: 100% !important;
        border-collapse: collapse !important;
        font-size: 10px !important;
        table-layout: auto !important;
    }
    thead {
        display: table-header-group !important;
    }
    tr {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }
    th,
    td {
        padding: 4px 6px !important;
    }
    .print-signature {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }
}
</style>
