<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { CheckCircle2 } from 'lucide-vue-next';

const page = usePage();
const quote = page.props.quote;

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="flex min-h-screen items-center justify-center bg-[#f3f4f6] p-4 font-sans text-gray-800 antialiased selection:bg-emerald-500 selection:text-white sm:p-6 lg:p-8"
    >
        <div
            class="grid min-h-[600px] w-full max-w-5xl grid-cols-1 overflow-hidden rounded-3xl border border-gray-200/80 bg-white shadow-xl lg:grid-cols-12"
        >
            <!-- LEFT FEATURE PANEL -->
            <div
                class="relative hidden flex-col justify-between overflow-hidden bg-gradient-to-br from-slate-900 via-slate-950 to-emerald-950 p-10 text-white lg:col-span-6 lg:flex"
            >
                <div class="pointer-events-none absolute -left-20 -top-20 h-56 w-56 rounded-full bg-emerald-500/20 blur-3xl"></div>
                <div class="pointer-events-none absolute -bottom-20 -right-20 h-56 w-56 rounded-full bg-teal-500/20 blur-3xl"></div>

                <div class="relative z-10 space-y-4">
                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-2xl border border-white/20 bg-white/10 p-2.5 shadow-lg backdrop-blur-md"
                    >
                        <img src="/images/icon-provsu.svg" alt="Provsu Emblem" class="h-full w-full object-contain" />
                    </div>
                    <div>
                        <h2 class="text-2xl font-extrabold tracking-tight text-white">SIMPADU KOP-UKM</h2>
                        <p class="mt-0.5 text-xs font-semibold uppercase tracking-widest text-emerald-400">Dinas Koperasi & UKM Provsu</p>
                    </div>
                </div>

                <div v-if="quote" class="relative z-10 my-auto py-6">
                    <blockquote class="space-y-3 rounded-2xl border border-white/10 bg-white/5 p-6 backdrop-blur-md">
                        <p class="text-sm italic leading-relaxed text-slate-200">&ldquo;{{ quote.message }}&rdquo;</p>
                        <footer class="text-xs font-semibold text-emerald-400">&mdash; {{ quote.author }}</footer>
                    </blockquote>
                </div>
                <div v-else class="relative z-10 my-auto space-y-4 py-6">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-emerald-500/20 text-emerald-400">
                            <CheckCircle2 class="h-4 w-4" />
                        </div>
                        <p class="text-xs font-medium leading-relaxed text-slate-300">
                            Sistem Pengawasan Kelembagaan & Administrasi Koperasi Terpadu Provinsi Sumatera Utara.
                        </p>
                    </div>
                </div>

                <div class="relative z-10 border-t border-white/10 pt-4 text-[11px] font-medium text-slate-400">
                    Pemerintah Provinsi Sumatera Utara © 2026
                </div>
            </div>

            <!-- RIGHT FORM SLOT -->
            <div class="flex flex-col justify-center p-6 sm:p-10 lg:col-span-6">
                <div class="mb-6 flex flex-col items-center text-center lg:hidden">
                    <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-900 p-2.5 shadow-md">
                        <img src="/images/icon-provsu.svg" alt="Provsu Emblem" class="h-full w-full object-contain" />
                    </div>
                    <div class="text-base font-extrabold tracking-tight text-gray-900">SIMPADU KOP-UKM</div>
                    <div class="text-[10px] font-semibold uppercase tracking-widest text-emerald-600">Prov. Sumatera Utara</div>
                </div>

                <div v-if="title || description" class="mb-6 space-y-1">
                    <h1 v-if="title" class="text-xl font-bold tracking-tight text-gray-900 sm:text-2xl">{{ title }}</h1>
                    <p v-if="description" class="text-xs font-medium text-gray-500">{{ description }}</p>
                </div>

                <slot />
            </div>
        </div>
    </div>
</template>
