<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';

interface Props {
    class?: string;
}

defineProps<Props>();
</script>

<template>
    <div class="flex aspect-square size-9 items-center justify-center p-0.5">
        <AppLogoIcon class="size-8 object-contain" />
    </div>
    <div class="ml-2 grid flex-1 text-left text-sm">
        <span class="truncate font-bold leading-none text-gray-900">SIMPADU KOP</span>
        <span class="mt-0.5 truncate text-[10px] font-semibold uppercase tracking-widest text-gray-400">Prov. Sumut</span>
    </div>
</template>
