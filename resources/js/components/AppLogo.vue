<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';

interface Props {
    class?: string;
}

defineProps<Props>();
</script>

<template>
    <div class="flex aspect-square size-9 items-center justify-center rounded-xl bg-slate-900 p-1.5 text-white shadow-xs">
        <AppLogoIcon class="size-6 object-contain" />
    </div>
    <div class="ml-2 grid flex-1 text-left text-sm">
        <span class="truncate font-bold leading-none text-gray-900">SIMPADU KOP</span>
        <span class="truncate text-[10px] text-gray-400 font-semibold uppercase tracking-widest mt-0.5">Prov. Sumut</span>
    </div>
</template>
