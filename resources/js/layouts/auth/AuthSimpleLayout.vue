<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ShieldCheck, CheckCircle2 } from 'lucide-vue-next';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="min-h-screen bg-[#f3f4f6] text-gray-800 flex items-center justify-center p-4 sm:p-6 lg:p-8 font-sans antialiased selection:bg-emerald-500 selection:text-white">
        <div class="w-full max-w-4xl grid grid-cols-1 lg:grid-cols-12 bg-white rounded-3xl border border-gray-200/80 shadow-xl overflow-hidden min-h-[580px]">
            
            <!-- LEFT FEATURE HERO PANEL (DESKTOP) -->
            <div class="hidden lg:flex lg:col-span-5 bg-gradient-to-br from-slate-900 via-slate-950 to-emerald-950 p-8 text-white flex-col justify-between relative overflow-hidden">
                <!-- Background Glow -->
                <div class="absolute -top-16 -left-16 w-48 h-48 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-16 -right-16 w-48 h-48 bg-teal-500/20 rounded-full blur-3xl pointer-events-none"></div>

                <!-- Top Branding -->
                <div class="relative z-10 space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 p-2 flex items-center justify-center shadow-lg">
                        <img src="/images/icon-provsu.svg" alt="Provsu Emblem" class="w-full h-full object-contain" />
                    </div>
                    <div>
                        <h2 class="text-xl font-extrabold tracking-tight text-white">
                            SIMPADU KOP-UKM
                        </h2>
                        <p class="text-xs text-emerald-400 font-semibold uppercase tracking-widest mt-0.5">
                            Provinsi Sumatera Utara
                        </p>
                    </div>
                </div>

                <!-- Feature Highlights -->
                <div class="relative z-10 space-y-3.5 my-auto py-6">
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">
                            <CheckCircle2 class="w-3.5 h-3.5" />
                        </div>
                        <p class="text-xs text-slate-300 font-medium leading-relaxed">
                            Pemeriksaan Kesehatan 4 Aspek Permenkop No. 9/2020 Terintegrasi.
                        </p>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">
                            <CheckCircle2 class="w-3.5 h-3.5" />
                        </div>
                        <p class="text-xs text-slate-300 font-medium leading-relaxed">
                            Monitoring Pelaporan RAT & Matriks Temuan Audit Real-Time.
                        </p>
                    </div>
                </div>

                <!-- Footer Tag -->
                <div class="relative z-10 pt-4 border-t border-white/10 text-[11px] text-slate-400 font-medium">
                    Dinas Koperasi & UKM Provsu © 2026
                </div>
            </div>

            <!-- RIGHT AUTH CONTENT FORM CARD -->
            <div class="lg:col-span-7 p-6 sm:p-10 flex flex-col justify-center">
                
                <!-- MOBILE LOGO BRANDING HEADER -->
                <div class="lg:hidden flex flex-col items-center mb-6 text-center">
                    <div class="w-12 h-12 rounded-2xl bg-slate-900 p-2.5 flex items-center justify-center shadow-md mb-3">
                        <img src="/images/icon-provsu.svg" alt="Provsu Emblem" class="w-full h-full object-contain" />
                    </div>
                    <div class="font-extrabold text-base text-gray-900 tracking-tight">SIMPADU KOP-UKM</div>
                    <div class="text-[10px] text-emerald-600 font-semibold uppercase tracking-widest">Prov. Sumatera Utara</div>
                </div>

                <!-- TITLE & DESCRIPTION -->
                <div class="mb-6 space-y-1">
                    <h1 v-if="title" class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">{{ title }}</h1>
                    <p v-if="description" class="text-xs text-gray-500 font-medium">{{ description }}</p>
                </div>

                <!-- MAIN FORM SLOT -->
                <slot />
            </div>
        </div>
    </div>
</template>

