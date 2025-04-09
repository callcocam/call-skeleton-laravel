<template>
    <div
        class="sticky top-0 flex h-screen w-72 flex-shrink-0 flex-col overflow-hidden rounded-lg border bg-gray-50 dark:border-gray-700 dark:bg-gray-800"
    >
        <div class="border-b border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-center text-lg font-medium text-gray-800 dark:text-gray-100">Produtos</h3>

            <!-- Campo de busca com design aprimorado -->
            <div class="relative mt-3">
                <input
                    v-model="filters.search"
                    type="text"
                    placeholder="Buscar produtos..."
                    class="w-full rounded-md border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-primary focus:ring-1 focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400"
                />
                <Search class="absolute right-3 top-2 h-4 w-4 text-gray-400 dark:text-gray-300" />
            </div>

            <!-- Botão de filtros com design aprimorado -->
            <button
                class="mt-2 flex w-full items-center justify-between rounded-md border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                @click="showFilters = !showFilters"
            >
                <div class="flex items-center">
                    <SlidersHorizontal class="mr-2 h-4 w-4 text-gray-500 dark:text-gray-300" />
                    <span class="text-gray-700 dark:text-gray-100">Filtros</span>
                </div>
                <ChevronDown class="h-4 w-4 text-gray-500 dark:text-gray-300" :class="{ 'rotate-180': showFilters }" />
            </button>

            <!-- Painel de filtros colapsável -->
            <div v-if="showFilters" class="mt-2 rounded-md border border-gray-200 bg-white p-3 text-sm dark:border-gray-600 dark:bg-gray-700">
                <div class="mb-2">
                    <label class="mb-1 block text-gray-700 dark:text-gray-200">Categoria</label>
                    <select
                        class="w-full rounded-md border-gray-300 bg-white py-1 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                        v-model="filters.category"
                    >
                        <option value="">Todas as categorias</option>
                        <option v-for="(cat, i) in categories" :key="i" :value="cat.id">{{ cat.name }}</option>
                    </select>
                </div>

                <div class="mb-2">
                    <p class="mb-1 block text-gray-700 dark:text-gray-200">Atributos</p>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                class="mr-1 rounded text-primary dark:border-gray-600 dark:bg-gray-700"
                                v-model="filters.hangable"
                            />
                            <span class="dark:text-gray-200">Pendurável</span>
                        </label>
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                class="mr-1 rounded text-primary dark:border-gray-600 dark:bg-gray-700"
                                v-model="filters.stackable"
                            />
                            <span class="dark:text-gray-200">Empilhável</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button
                        type="button"
                        @click="clearFilters"
                        class="rounded-md bg-gray-100 px-2 py-1 text-xs text-gray-700 hover:bg-gray-200 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500"
                    >
                        Limpar filtros
                    </button>
                </div>
            </div>
        </div>

        <!-- Lista de produtos com design limpo -->
        <div class="flex-1 overflow-y-auto p-2 dark:bg-gray-800">
            <div class="mb-2 px-2 py-1 text-sm text-gray-500 dark:text-gray-400">
                <span>{{ filteredProducts.length }} produtos encontrados</span>
            </div>

            <ul class="space-y-1">
                <li
                    v-for="(product, index) in filteredProducts"
                    :key="product.id || index"
                    class="group cursor-pointer rounded-md bg-white p-2 shadow-sm transition hover:bg-blue-50 dark:bg-gray-700 dark:hover:bg-gray-600"
                    @click="handleProductSelect(product)"
                    draggable="true"
                    @dragstart="handleDragStart($event, product)"
                >
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 overflow-hidden rounded border bg-white p-1 dark:border-gray-600 dark:bg-gray-800">
                            <img :src="product.image_url" :alt="product.name" class="h-12 w-12 object-contain" @error="handleImageError" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-gray-800 dark:text-gray-100">{{ product.name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ product.width }}×{{ product.height }}×{{ product.depth }} cm</p>
                        </div>
                    </div>
                    <div class="mt-1 flex justify-end">
                        <button class="invisible text-xs text-blue-600 group-hover:visible dark:text-blue-400" @click.stop="viewStats(product)">
                            Ver estatísticas
                        </button>
                    </div>
                </li>
            </ul>

            <!-- Loading state -->
            <div v-if="loading" class="flex items-center justify-center py-10">
                <Loader class="h-6 w-6 animate-spin text-primary" />
                <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">Carregando...</span>
            </div>

            <!-- Empty state -->
            <div v-if="!loading && filteredProducts.length === 0" class="flex flex-col items-center justify-center py-10 text-center">
                <Package class="h-10 w-10 text-gray-300 dark:text-gray-600" />
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Nenhum produto encontrado</p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ChevronDown, Loader, Package, Search, SlidersHorizontal } from 'lucide-vue-next';
import { computed, onMounted, ref, watch } from 'vue';
import { apiService } from '../../../services';
import { useEditorStore } from '../../../store/editor';

interface Product {
    id: number;
    name: string;
    image_url?: string;
    width: number;
    height: number;
    depth: number;
}
interface Category {
    id: number;
    name: string;
}

const props = defineProps({
    categories: {
        type: Array as () => Category[],
        default: [],
    },
});

const editorStore = useEditorStore();

const gondolaId = computed(() => editorStore.gondolaId);

const emit = defineEmits(['select-product', 'drag-start', 'view-stats']);

// Estado
const showFilters = ref(false);
const loading = ref(false);
const filters = ref({
    search: '',
    category: null,
    hangable: false,
    stackable: false,
    flammable: false,
    perishable: false,
});
const gondolas = computed(() => editorStore.gondolas);
// Vamos pegar todos os produtos que estão na gondola
const notInGondola = ref([] as string[]);

// Observa os filtros para refazer a busca
watch(
    filters,
    async () => {
        await fetchProducts();
    },
    { deep: true },
);

// Produtos filtrados
const filteredProducts = ref([] as Product[]);

// Manipuladores de eventos
function handleProductSelect(product) {
    emit('select-product', product);
}

function handleDragStart(event, product) {
    event.dataTransfer.setData('text/product', JSON.stringify(product));
    event.dataTransfer.effectAllowed = 'copy';
    emit('drag-start', event, product);
}

function viewStats(product) {
    emit('view-stats', product);
}

function handleImageError(e) {
    // Substitui imagens quebradas por um placeholder
    e.target.src = '/images/placeholder.jpg';
}

// Busca produtos da API
async function fetchProducts() {
    try {
        loading.value = true;

        const gondola = gondolas.value.find((g) => g.id === gondolaId.value);
        if (gondola) {
            if (gondola?.sections) {
                gondola.sections.forEach((section) => {
                    section.shelves.forEach((shelf) => {
                        shelf.segments.forEach((segment) => {
                            notInGondola.value.push(segment.layer.product.id);
                        });
                    });
                });
            }
        }

        // Se houver implementação real da API
        // @ts-ignore
        const response = await apiService.get('products', {
            params: {
                notInGondola: notInGondola.value,
                search: filters.value.search,
                category: filters.value.category,
                hangable: filters.value.hangable,
                stackable: filters.value.stackable,
                flammable: filters.value.flammable,
                perishable: filters.value.perishable,
            },
        });
        filteredProducts.value = response;
    } catch (error) {
        console.error('Erro ao carregar produtos:', error);
        filteredProducts.value = [];
    } finally {
        loading.value = false;
    }
}

/**
 * Limpa todos os filtros
 */
function clearFilters() {
    filters.value = {
        hangable: false,
        stackable: false,
        flammable: false,
        perishable: false,
        search: '',
        category: null,
    };
    showFilters.value = false;
}
// Inicializa o componente
onMounted(async () => {
    // Carrega categorias e produtos ao montar o componente
    await fetchProducts();
});
</script>

<style scoped>
/* Estilo para garantir que a área central possa rolar enquanto as laterais ficam fixas */
.overflow-y-auto {
    max-height: 100vh;
    scrollbar-width: thin;
    scrollbar-color: #e2e8f0 #f8fafc;
}

.overflow-x-auto {
    scrollbar-width: thin;
    scrollbar-color: #e2e8f0 #f8fafc;
}

.overflow-y-auto::-webkit-scrollbar,
.overflow-x-auto::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

.overflow-y-auto::-webkit-scrollbar-track,
.overflow-x-auto::-webkit-scrollbar-track {
    background: #f8fafc;
}

.overflow-y-auto::-webkit-scrollbar-thumb,
.overflow-x-auto::-webkit-scrollbar-thumb {
    background-color: #e2e8f0;
    border-radius: 4px;
}

/* Estilos para o modo escuro da barra de rolagem */
@media (prefers-color-scheme: dark) {
    .overflow-y-auto {
        scrollbar-color: #4b5563 #1f2937;
    }

    .overflow-x-auto {
        scrollbar-color: #4b5563 #1f2937;
    }

    .overflow-y-auto::-webkit-scrollbar-track,
    .overflow-x-auto::-webkit-scrollbar-track {
        background: #1f2937;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb,
    .overflow-x-auto::-webkit-scrollbar-thumb {
        background-color: #4b5563;
    }
}
</style>
