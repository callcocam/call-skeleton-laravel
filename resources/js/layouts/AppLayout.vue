<script setup lang="ts">
import AppLayout from './app/components/AppHeaderLayout.vue';
import type { BreadcrumbItemType } from './../types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />
    </AppLayout>
</template>
