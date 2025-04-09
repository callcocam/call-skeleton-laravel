<template>
    <div>
        <div v-if="isLoading" class="flex h-full items-center justify-center p-4 text-center text-gray-400 dark:text-gray-500">
            <p>Carregando...</p>
        </div>
        <div v-else class="flex h-full w-full flex-col gap-6 overflow-x-auto overflow-y-auto">
            <Info :gondola="gondola" v-if="gondola" />
            <div class="flex h-full items-center justify-center p-4 text-center text-gray-400 dark:text-gray-500" v-if="!gondola">
                <p>Selecione uma gôndola para ver suas propriedades</p>
            </div>
            <div v-else class="flex flex-col gap-4">
                <h2 class="text-2xl font-bold tracking-tight dark:text-gray-100">{{ gondola.name }}</h2>
                <p class="text-sm text-muted-foreground dark:text-gray-400">ID: {{ gondola.id }} | Criado em: {{ gondola.created_at }}</p>
                <p v-for="section in gondola?.sections" :key="section.id">
                    <span class="font-semibold text-gray-800 dark:text-gray-100">{{ section.name }}</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400"> - {{ section.description }}</span>
                </p>
            </div>
        </div>
        <router-view :key="route.fullPath" />
    </div>
</template>
<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useEditorStore } from '../../store/editor';

import { apiService } from '../../services';
import Info from './partials/Info.vue';

const route = useRoute();
const router = useRouter();
const id = ref<string>(route.params.gondolaId as string);

const isLoading = ref<boolean>(false);
const editorStore = useEditorStore();

const gondola = ref<any>(null); // Substitua 'any' pelo tipo correto, se possível

const get = async () => {
    const response = await apiService.get('gondolas/'.concat(id.value)); 
    gondola.value = response.data;
};
onMounted(async () => {
    isLoading.value = true;
    await get();
    isLoading.value = false;
});
</script>
