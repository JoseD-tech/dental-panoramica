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
    <img src="/logo.png" alt="Logo web" class="h-10 w-auto" v-bind="$attrs" :class="['inline-block', className]" />
</template>
