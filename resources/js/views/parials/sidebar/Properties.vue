<template>
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
</template>
<script setup lang="ts">
import { ref } from 'vue';
const selectedProducts = ref<any[]>([]); // Substitua 'any' pelo tipo correto, se possível
</script>
