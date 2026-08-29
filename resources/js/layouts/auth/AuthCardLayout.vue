<script setup lang="ts">
defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="flex min-h-screen items-center justify-center bg-[#f3f4f6] p-4 font-sans text-gray-800 antialiased selection:bg-emerald-500 selection:text-white sm:p-6"
    >
        <div class="w-full max-w-md space-y-6 rounded-3xl border border-gray-200/80 bg-white p-8 shadow-xl sm:p-10">
            <!-- BRAND LOGO HEADER -->
            <div class="flex flex-col items-center space-y-2 text-center">
                <div class="mb-1 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-900 p-3 shadow-md shadow-slate-900/10">
                    <img src="/images/icon-provsu.svg" alt="Provsu Emblem" class="h-full w-full object-contain" />
                </div>
                <h2 class="text-xl font-extrabold leading-tight tracking-tight text-gray-900">SIMPADU KOP-UKM</h2>
                <p class="text-[11px] font-semibold uppercase tracking-widest text-emerald-600">Dinas Koperasi & UKM Provsu</p>
            </div>

            <!-- TITLE & DESCRIPTION -->
            <div v-if="title || description" class="space-y-1 text-center">
                <h1 v-if="title" class="text-lg font-bold tracking-tight text-gray-900">{{ title }}</h1>
                <p v-if="description" class="text-xs font-medium text-gray-500">{{ description }}</p>
            </div>

            <!-- FORM SLOT -->
            <slot />
        </div>
    </div>
</template>
