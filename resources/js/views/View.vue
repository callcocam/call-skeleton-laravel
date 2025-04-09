<template>
    <div class="px-10">
        <Header v-if="record" :planogram="record" />
        <div>
            <div class="flex h-full w-full gap-6 overflow-hidden">
                <!-- Barra lateral esquerda com componente Products separado -->
                <Products />
                <!-- Área central rolável (vertical e horizontal) -->
                <div class="flex h-full w-full flex-col gap-6 overflow-x-auto overflow-y-auto">
                    <Gondolas v-if="gondolas?.length" />
                    <CreateGondola v-else-if="!gondolas?.length" />
                </div>

                <div
                    class="sticky top-0 flex h-screen w-64 flex-shrink-0 flex-col overflow-hidden rounded-lg border bg-gray-50 dark:border-gray-700 dark:bg-gray-800"
                >
                    <div class="border-b border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-800">
                        <h3 class="text-center text-lg font-medium text-gray-800 dark:text-gray-100">Propriedades</h3>
                    </div>
                    <div class="flex-1 p-3">
                        <div v-if="selectedProducts.length" class="rounded-md bg-white p-3 shadow-sm dark:bg-gray-700">
                            <p class="text-gray-800 dark:text-gray-200">{{ selectedProducts.length }} produto(s) selecionado(s)</p>
                            <div v-for="product in selectedProducts" :key="product.id" class="flex items-center gap-2">
                                <img :src="product.image_url" alt="" class="h-16 w-16 rounded-md border object-cover dark:border-gray-600" />
                                <div class="flex flex-col">
                                    <h4 class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ product.name }}</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">SKU: {{ product.sku }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Largura: {{ product.width }}</p>
                                </div>
                            </div>
                        </div>
                        <div v-else class="flex h-full items-center justify-center p-4 text-center text-gray-400 dark:text-gray-500">
                            Selecione um produto para ver suas propriedades
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { apiService } from '../services';
import { useEditorStore } from '../store/editor';
import Gondolas from './gondolas/Gondolas.vue';
import CreateGondola from './parials/CreateGondola.vue';
import Header from './parials/Header.vue';
import Products from './parials/product/Products.vue';

const route = useRoute();
const router = useRouter();
const id = ref<string>(route.params.id as string);
const isLoading = ref<boolean>(false);

const editorStore = useEditorStore(); 

const record = ref<any>(null); // Substitua 'any' pelo tipo correto, se possível
const gondolas = ref<any[]>([]); // Substitua 'any' pelo tipo correto, se possível
const selectedProducts = ref<any[]>([]); // Substitua 'any' pelo tipo correto, se possível

const get = async () => {
    const response = await apiService.get('plannerate/'.concat(id.value)); 
    record.value = response.data;
    console.log('record', record.value);
    gondolas.value = response.data.gondolas;
    editorStore.setGondolas(response.data.gondolas);
    if (response) {
    }
};

onMounted(async () => {
    isLoading.value = true;
    await get();
    isLoading.value = false;

    if (!isLoading.value) {
        if (!route.params.gondolaId) {
            // Se não houver gondolaId na rota, redireciona para a primeira gôndola
            if (gondolas.value.length > 0) {
                const firstGondola = gondolas.value[0];
                if (firstGondola) {
                    await router.push({
                        name: 'gondola.view',
                        params: { gondolaId: firstGondola.id },
                    });
                }
            }
        }
    }
});
</script>
