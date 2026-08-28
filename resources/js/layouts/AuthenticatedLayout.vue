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
    User,
    ChevronDown,
    Award,
    CheckCircle2,
    AlertCircle
} from 'lucide-vue-next';

const page = usePage();
const auth = computed(() => page.props.auth as any);
const flash = computed(() => page.props.flash as any);

const isMobileMenuOpen = ref(false);
const isProfileDropdownOpen = ref(false);

const userRole = computed(() => auth.value?.user?.role || 'admin_koperasi');
const userName = computed(() => auth.value?.user?.name || 'Pengguna');
const userNip = computed(() => auth.value?.user?.nip || '-');
const roleLabel = computed(() => auth.value?.role_label || 'Administrasi Koperasi');

const navItems = [
    {
        name: 'Dashboard',
        href: '/dashboard',
        icon: LayoutDashboard,
        roles: ['admin_koperasi', 'bidang_pengawasan']
    },
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
    },
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
    },
    {
        name: 'Cetak Laporan Resmi',
        href: '/cetak',
        icon: Printer,
        roles: ['admin_koperasi', 'bidang_pengawasan']
    }
];

const filteredNav = computed(() => {
    return navItems.filter(item => item.roles.includes(userRole.value));
});

const isCurrentRoute = (url: string) => {
    return page.url.startsWith(url);
};

const handleLogout = () => {
    router.post('/logout');
};
</script>

<template>
    <div class="min-h-screen bg-slate-900 text-slate-100 flex flex-col font-sans antialiased selection:bg-emerald-500 selection:text-white">
        <!-- TOP APP BAR -->
        <header class="sticky top-0 z-40 bg-slate-900/90 backdrop-blur-md border-b border-slate-800 flex-shrink-0">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <!-- Brand & Mobile Toggle -->
                <div class="flex items-center gap-3">
                    <button 
                        @click="isMobileMenuOpen = !isMobileMenuOpen"
                        class="md:hidden p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition"
                    >
                        <X v-if="isMobileMenuOpen" class="w-6 h-6" />
                        <Menu v-else class="w-6 h-6" />
                    </button>

                    <Link href="/dashboard" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center shadow-lg shadow-emerald-500/20 group-hover:scale-105 transition-transform duration-300">
                            <Award class="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <div class="font-bold text-lg tracking-wider bg-gradient-to-r from-white via-slate-100 to-emerald-400 bg-clip-text text-transparent">
                                SIMPADU KOP-UKM
                            </div>
                            <div class="text-[10px] text-emerald-400 font-medium uppercase tracking-widest -mt-1">
                                Provsu - Sumatera Utara
                            </div>
                        </div>
                    </Link>
                </div>

                <!-- Right Action & User Profile -->
                <div class="flex items-center gap-4">
                    <!-- Role Badge -->
                    <div 
                        class="hidden sm:flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold tracking-wide border shadow-sm"
                        :class="userRole === 'bidang_pengawasan' 
                            ? 'bg-purple-950/60 text-purple-300 border-purple-800/60' 
                            : 'bg-emerald-950/60 text-emerald-300 border-emerald-800/60'"
                    >
                        <span class="w-2 h-2 rounded-full animate-pulse" :class="userRole === 'bidang_pengawasan' ? 'bg-purple-400' : 'bg-emerald-400'"></span>
                        {{ roleLabel }}
                    </div>

                    <!-- User Profile Dropdown -->
                    <div class="relative">
                        <button 
                            @click="isProfileDropdownOpen = !isProfileDropdownOpen"
                            class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-slate-800/80 border border-transparent hover:border-slate-700/60 transition"
                        >
                            <div class="w-8 h-8 rounded-lg bg-slate-700 flex items-center justify-center text-emerald-400 font-bold text-sm">
                                {{ userName.charAt(0) }}
                            </div>
                            <div class="hidden lg:block text-left">
                                <div class="text-xs font-semibold text-slate-200 leading-none">{{ userName }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">NIP: {{ userNip }}</div>
                            </div>
                            <ChevronDown class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': isProfileDropdownOpen }" />
                        </button>

                        <!-- Dropdown Menu -->
                        <div 
                            v-if="isProfileDropdownOpen"
                            @click="isProfileDropdownOpen = false"
                            class="absolute right-0 mt-2 w-56 rounded-xl bg-slate-800 border border-slate-700 shadow-2xl py-2 z-50 animate-in fade-in slide-in-from-top-2 duration-200"
                        >
                            <div class="px-4 py-2 border-b border-slate-700/60">
                                <p class="text-xs font-bold text-slate-200">{{ userName }}</p>
                                <p class="text-[11px] text-emerald-400 font-medium mt-0.5">{{ roleLabel }}</p>
                            </div>
                            <button 
                                @click="handleLogout"
                                class="w-full flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-rose-400 hover:bg-rose-500/10 transition"
                            >
                                <LogOut class="w-4 h-4" />
                                Keluar Aplikasi (Logout)
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- BODY WRAPPER WITH SIDEBAR -->
        <div class="flex-1 flex overflow-hidden">
            <!-- SIDEBAR NAV (Desktop & Mobile Drawer) -->
            <aside 
                class="fixed inset-y-0 left-0 z-30 w-64 bg-slate-900 border-r border-slate-800 flex flex-col transition-transform duration-300 md:static md:translate-x-0 pt-16 md:pt-0"
                :class="isMobileMenuOpen ? 'translate-x-0' : '-translate-x-full'"
            >
                <div class="p-4 flex-1 space-y-1.5 overflow-y-auto">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3 mb-2">
                        Navigasi Utama
                    </div>

                    <Link 
                        v-for="item in filteredNav" 
                        :key="item.name"
                        :href="item.href"
                        @click="isMobileMenuOpen = false"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition duration-200 group"
                        :class="isCurrentRoute(item.href) 
                            ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold shadow-md shadow-emerald-600/20' 
                            : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/60'"
                    >
                        <component 
                            :is="item.icon" 
                            class="w-5 h-5 transition-transform duration-200 group-hover:scale-110"
                            :class="isCurrentRoute(item.href) ? 'text-white' : 'text-slate-400 group-hover:text-emerald-400'"
                        />
                        <span>{{ item.name }}</span>
                    </Link>
                </div>

                <!-- Footer Info Sidebar -->
                <div class="p-4 border-t border-slate-800/80 bg-slate-950/40 text-center">
                    <div class="text-[11px] text-slate-400 font-semibold">Dinas Koperasi & UKM</div>
                    <div class="text-[10px] text-slate-500">Provinsi Sumatera Utara © 2026</div>
                </div>
            </aside>

            <!-- MAIN CONTENT AREA -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-slate-950/50">
                <!-- Flash Notification Banner -->
                <div v-if="flash?.success" class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 flex items-center justify-between shadow-lg">
                    <div class="flex items-center gap-3">
                        <CheckCircle2 class="w-5 h-5 text-emerald-400 flex-shrink-0" />
                        <span class="text-sm font-medium">{{ flash.success }}</span>
                    </div>
                </div>

                <div v-if="flash?.error" class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 flex items-center justify-between shadow-lg">
                    <div class="flex items-center gap-3">
                        <AlertCircle class="w-5 h-5 text-rose-400 flex-shrink-0" />
                        <span class="text-sm font-medium">{{ flash.error }}</span>
                    </div>
                </div>

                <slot />
            </main>
        </div>
    </div>
</template>
