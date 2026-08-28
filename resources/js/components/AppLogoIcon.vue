<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <img src="/images/icon-provsu.svg" alt="Provsu Emblem" :class="className" v-bind="$attrs" class="object-contain" />
</template>
