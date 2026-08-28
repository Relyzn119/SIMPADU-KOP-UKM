<script setup lang="ts">
import { ref, computed } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';
import {
    LayoutDashboard,
    Building2,
    FileSpreadsheet,
    ShieldCheck,
    AlertTriangle,
    Printer,
    LogOut,
    Menu,
    X,
    ChevronDown,
    Award,
    CheckCircle2,
    AlertCircle,
    Bell,
    Calendar,
    Filter,
    Plus,
    UserCheck,
    Search,
    HelpCircle,
    SlidersHorizontal
} from 'lucide-vue-next';

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

// Grouped navigation menu matching the reference image layout
const navGroups = computed(() => [
    {
        name: 'Analytics',
        items: [
            {
                name: 'Overview',
                href: '/dashboard',
                icon: LayoutDashboard,
                roles: ['admin_koperasi', 'bidang_pengawasan']
            }
        ]
    },
    {
        name: 'Kelembagaan',
        items: [
            {
                name: 'Data Koperasi',
                href: '/koperasi',
                icon: Building2,
                roles: ['admin_koperasi', 'bidang_pengawasan']
            },
            {
                name: 'Pelaporan RAT',
                href: '/rat',
                icon: FileSpreadsheet,
                roles: ['admin_koperasi', 'bidang_pengawasan']
            }
        ]
    },
    {
        name: 'Pengawasan',
        items: [
            {
                name: 'Pemeriksaan Kesehatan',
                href: '/pengawasan',
                icon: ShieldCheck,
                roles: ['admin_koperasi', 'bidang_pengawasan']
            },
            {
                name: 'Matriks Temuan Audit',
                href: '/temuan',
                icon: AlertTriangle,
                roles: ['admin_koperasi', 'bidang_pengawasan']
            }
        ]
    },
    {
        name: 'System & Output',
        items: [
            {
                name: 'Cetak Laporan',
                href: '/cetak',
                icon: Printer,
                roles: ['admin_koperasi', 'bidang_pengawasan']
            }
        ]
    }
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
    <div class="min-h-screen bg-[#f3f4f6] text-gray-800 flex flex-col font-sans antialiased selection:bg-emerald-500 selection:text-white">
        <!-- MOBILE HEADER BAR -->
        <header class="md:hidden sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-gray-200/80 px-4 py-3 flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-3">
                <button 
                    @click="isMobileMenuOpen = !isMobileMenuOpen"
                    class="p-2 rounded-xl text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition"
                    aria-label="Toggle Navigation"
                >
                    <X v-if="isMobileMenuOpen" class="w-6 h-6" />
                    <Menu v-else class="w-6 h-6" />
                </button>

                <Link href="/dashboard" class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-slate-900 flex items-center justify-center p-1.5 shadow-xs">
                        <img src="/images/icon-provsu.svg" alt="Provsu emblem" class="w-full h-full object-contain" />
                    </div>
                    <span class="font-bold text-sm text-gray-900 tracking-tight">SIMPADU KOP</span>
                </Link>
            </div>

            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs">
                    {{ userName.charAt(0) }}
                </div>
            </div>
        </header>

        <!-- MAIN APP WRAPPER (FLOATING CARD LAYOUT) -->
        <div class="flex-1 flex p-3 sm:p-4 md:p-6 gap-5 max-w-[1600px] w-full mx-auto relative">

            <!-- MOBILE SIDEBAR OVERLAY -->
            <div 
                v-if="isMobileMenuOpen" 
                @click="isMobileMenuOpen = false"
                class="fixed inset-0 bg-gray-900/40 backdrop-blur-xs z-40 md:hidden transition-opacity"
            ></div>

            <!-- FLOATING SIDEBAR (Desktop & Mobile Drawer) -->
            <aside 
                class="fixed inset-y-0 left-0 z-50 w-64 bg-white border border-gray-200/80 rounded-none md:rounded-3xl shadow-lg md:shadow-xs p-4 flex flex-col justify-between transition-transform duration-300 md:static md:translate-x-0 md:sticky md:top-6 md:h-[calc(100vh-3rem)] shrink-0"
                :class="isMobileMenuOpen ? 'translate-x-0' : '-translate-x-full'"
            >
                <!-- TOP SIDEBAR BRANDING & NAV LIST -->
                <div class="space-y-6 overflow-y-auto pr-1 flex-1 custom-scrollbar">

                    <!-- BRAND LOGO HEADER -->
                    <div class="px-2 pt-1 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-slate-900 flex items-center justify-center p-2 shadow-sm shrink-0">
                            <img src="/images/icon-provsu.svg" alt="Provsu Emblem" class="w-full h-full object-contain" />
                        </div>
                        <div>
                            <div class="font-extrabold text-base text-gray-900 tracking-tight leading-tight">
                                SIMPADU KOP
                            </div>
                            <div class="text-[10px] text-gray-400 font-semibold uppercase tracking-widest">
                                Prov. Sumut
                            </div>
                        </div>
                    </div>

                    <!-- NAVIGATION GROUPS -->
                    <nav class="space-y-5">
                        <div v-for="group in navGroups" :key="group.name" class="space-y-1.5">
                            <!-- Category Section Title -->
                            <div class="text-[11px] font-semibold text-gray-400 px-3 tracking-wide uppercase">
                                {{ group.name }}
                            </div>

                            <!-- Category Menu Items -->
                            <div class="space-y-1">
                                <template v-for="item in group.items" :key="item.name">
                                    <Link 
                                        v-if="item.roles.includes(userRole)"
                                        :href="item.href"
                                        @click="isMobileMenuOpen = false"
                                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs sm:text-sm transition-all duration-150 group"
                                        :class="isCurrentRoute(item.href) 
                                            ? 'bg-gray-100 text-gray-900 font-semibold shadow-2xs' 
                                            : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50 font-medium'"
                                    >
                                        <component 
                                            :is="item.icon" 
                                            class="w-4 h-4 transition-transform duration-200 group-hover:scale-110"
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
                <div class="pt-4 border-t border-gray-100 space-y-3 shrink-0">
                    <Link 
                        href="/koperasi" 
                        class="w-full flex items-center justify-center gap-2 py-2.5 px-3 rounded-2xl bg-indigo-50/90 hover:bg-indigo-100/90 border border-indigo-100 text-indigo-600 font-semibold text-xs transition shadow-2xs"
                    >
                        <Plus class="w-3.5 h-3.5" />
                        Master Koperasi
                    </Link>

                    <div class="text-center text-[11px] text-gray-400 font-medium">
                        Version 2.0 • Provsu
                    </div>
                </div>
            </aside>

            <!-- MAIN CONTENT AREA -->
            <div class="flex-1 flex flex-col min-w-0 space-y-5">
                
                <!-- TOP HEADER NAVBAR (DESKTOP) -->
                <header class="hidden md:flex items-center justify-between py-1">
                    <!-- Page Title / Overview -->
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Overview</h1>
                        <p class="text-xs text-gray-500 mt-0.5">Sistem Pengawasan & Administrasi Kelembagaan Koperasi</p>
                    </div>

                    <!-- Top Bar Action Controls (Filters, Notifications, Profile) -->
                    <div class="flex items-center gap-3">
                        <!-- Date Filter Pill Button -->
                        <div class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-gray-200/80 text-gray-700 text-xs font-semibold shadow-2xs hover:bg-gray-50 transition cursor-pointer">
                            <Calendar class="w-3.5 h-3.5 text-gray-500" />
                            <span>Tahun Buku 2024</span>
                            <ChevronDown class="w-3.5 h-3.5 text-gray-400" />
                        </div>

                        <!-- Region / Brand Filter Pill -->
                        <div class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-gray-200/80 text-gray-700 text-xs font-semibold shadow-2xs hover:bg-gray-50 transition cursor-pointer">
                            <Filter class="w-3.5 h-3.5 text-gray-500" />
                            <span>Semua Wilayah</span>
                            <ChevronDown class="w-3.5 h-3.5 text-gray-400" />
                        </div>

                        <!-- Notification Bell Button -->
                        <div class="relative">
                            <button 
                                @click="isNotificationOpen = !isNotificationOpen"
                                class="p-2 rounded-xl bg-white border border-gray-200/80 text-gray-600 hover:text-gray-900 hover:bg-gray-50 shadow-2xs transition relative"
                                aria-label="Notifications"
                            >
                                <Bell class="w-4 h-4" />
                                <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-emerald-500 ring-2 ring-white"></span>
                            </button>

                            <!-- Notification Dropdown -->
                            <div 
                                v-if="isNotificationOpen"
                                @click="isNotificationOpen = false"
                                class="absolute right-0 mt-2 w-72 rounded-2xl bg-white border border-gray-200 shadow-xl p-3 z-50 text-xs space-y-2"
                            >
                                <div class="font-bold text-gray-900 pb-2 border-b border-gray-100 flex items-center justify-between">
                                    <span>Notifikasi Sistem</span>
                                    <span class="text-[10px] text-emerald-600 font-semibold bg-emerald-50 px-2 py-0.5 rounded-md">2 Baru</span>
                                </div>
                                <div class="p-2 rounded-xl bg-gray-50 hover:bg-gray-100 transition cursor-pointer">
                                    <div class="font-semibold text-gray-800">Verifikasi RAT Selesai</div>
                                    <div class="text-[10px] text-gray-400 mt-0.5">KSP Sahabat Jaya • 10 menit lalu</div>
                                </div>
                                <div class="p-2 rounded-xl bg-gray-50 hover:bg-gray-100 transition cursor-pointer">
                                    <div class="font-semibold text-gray-800">Alert Temuan Kritis</div>
                                    <div class="text-[10px] text-gray-400 mt-0.5">Koperasi Makmur Bersama • 1 jam lalu</div>
                                </div>
                            </div>
                        </div>

                        <!-- User Profile Pill -->
                        <div class="relative">
                            <button 
                                @click="isProfileDropdownOpen = !isProfileDropdownOpen"
                                class="flex items-center gap-2.5 p-1 pl-1.5 pr-2.5 rounded-full bg-white border border-gray-200/80 shadow-2xs hover:bg-gray-50 transition"
                            >
                                <div class="w-7 h-7 rounded-full bg-slate-900 text-white font-bold text-xs flex items-center justify-center shadow-2xs">
                                    {{ userName.charAt(0) }}
                                </div>
                                <span class="text-xs font-bold text-gray-800 max-w-[120px] truncate">{{ userName }}</span>
                                <ChevronDown class="w-3.5 h-3.5 text-gray-400 transition-transform" :class="{ 'rotate-180': isProfileDropdownOpen }" />
                            </button>

                            <!-- Profile Dropdown -->
                            <div 
                                v-if="isProfileDropdownOpen"
                                @click="isProfileDropdownOpen = false"
                                class="absolute right-0 mt-2 w-56 rounded-2xl bg-white border border-gray-200 shadow-xl py-2 z-50 animate-in fade-in duration-150"
                            >
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <p class="text-xs font-bold text-gray-900">{{ userName }}</p>
                                    <p class="text-[10px] text-emerald-600 font-semibold mt-0.5">{{ roleLabel }}</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">NIP: {{ userNip }}</p>
                                </div>
                                <button 
                                    @click="handleLogout"
                                    class="w-full flex items-center gap-2 px-4 py-2.5 text-xs font-medium text-rose-600 hover:bg-rose-50 transition"
                                >
                                    <LogOut class="w-4 h-4" />
                                    Keluar Aplikasi (Logout)
                                </button>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- FLASH NOTIFICATIONS -->
                <div v-if="flash?.success" class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-2xs">
                    <div class="flex items-center gap-3">
                        <CheckCircle2 class="w-5 h-5 text-emerald-600 flex-shrink-0" />
                        <span class="text-xs font-semibold">{{ flash.success }}</span>
                    </div>
                </div>

                <div v-if="flash?.error" class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between shadow-2xs">
                    <div class="flex items-center gap-3">
                        <AlertCircle class="w-5 h-5 text-rose-600 flex-shrink-0" />
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

