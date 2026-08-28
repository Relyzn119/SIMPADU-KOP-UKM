<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ShieldCheck, CheckCircle2 } from 'lucide-vue-next';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="min-h-screen bg-[#f3f4f6] text-gray-800 flex items-center justify-center p-4 sm:p-6 font-sans antialiased selection:bg-emerald-500 selection:text-white">
        <div class="w-full max-w-md bg-white rounded-3xl border border-gray-200/80 shadow-xl p-8 sm:p-10 space-y-6">
            <!-- BRAND LOGO HEADER -->
            <div class="flex flex-col items-center text-center space-y-2">
                <div class="w-14 h-14 rounded-2xl bg-slate-900 p-3 flex items-center justify-center shadow-md shadow-slate-900/10 mb-1">
                    <img src="/images/icon-provsu.svg" alt="Provsu Emblem" class="w-full h-full object-contain" />
                </div>
                <h2 class="text-xl font-extrabold text-gray-900 tracking-tight leading-tight">SIMPADU KOP-UKM</h2>
                <p class="text-[11px] text-emerald-600 font-semibold uppercase tracking-widest">Dinas Koperasi & UKM Provsu</p>
            </div>

            <!-- TITLE & DESCRIPTION -->
            <div v-if="title || description" class="text-center space-y-1">
                <h1 v-if="title" class="text-lg font-bold text-gray-900 tracking-tight">{{ title }}</h1>
                <p v-if="description" class="text-xs text-gray-500 font-medium">{{ description }}</p>
            </div>

            <!-- FORM SLOT -->
            <slot />
        </div>
    </div>
</template>

