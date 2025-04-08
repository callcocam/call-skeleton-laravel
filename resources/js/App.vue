<script setup lang="ts">
import AppLayout from './layouts/AppLayout.vue';
import { type BreadcrumbItem } from './types';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    title: {
        type: String,
        default: 'Dashboard',
    },
    description: {
        type: String,
        default: 'Dashboard',
    },
    breadcrumbs: {
        type: Array as () => BreadcrumbItem[],
        default: () => [],
    },
});
 
</script>

<template>
    <Head :title="title" :description="description" />

    <AppLayout :breadcrumbs="breadcrumbs">
       
    </AppLayout>
</template>
