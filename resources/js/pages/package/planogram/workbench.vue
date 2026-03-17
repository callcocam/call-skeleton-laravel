<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import PackageWorkbench from '@planogram/components/plannerate/PackageWorkbench.vue';

interface Props {
    title: string;
    branch: string;
    legacyStrategy: string;
    nextMilestone: string;
}

defineProps<Props>();
</script>

<template>
    <div class="min-h-screen bg-background px-6 py-10 text-foreground md:px-10">
        <Head :title="title" />

        <div class="mx-auto flex w-full max-w-6xl flex-col gap-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-sm text-muted-foreground">Sandbox da migracao</p>
                    <h1 class="text-2xl font-semibold">{{ title }}</h1>
                </div>
                <a
                    href="/flow-test"
                    class="inline-flex items-center rounded-full border border-border px-4 py-2 text-sm font-medium text-foreground transition hover:bg-muted"
                >
                    Ver pagina de referencia
                </a>
            </div>

            <PackageWorkbench
                :branch="branch"
                :legacy-strategy="legacyStrategy"
                :next-milestone="nextMilestone"
            />
        </div>
    </div>
</template>
