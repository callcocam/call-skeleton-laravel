<script setup lang="ts">
import AppLayout from './layouts/AppLayout.vue';
import { type BreadcrumbItem } from './types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];
</script>

<template>
    <Head title="P" />

    <AppLayout :breadcrumbs="breadcrumbs">
       
    </AppLayout>
</template>
