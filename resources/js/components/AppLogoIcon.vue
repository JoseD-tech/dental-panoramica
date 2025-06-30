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
    <img src="/logo.svg" alt="Logo web" class="h-14 w-auto" v-bind="$attrs" :class="['inline-block', className]" />
</template>
