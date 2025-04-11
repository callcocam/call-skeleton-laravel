<template>
    <div class="shelf relative flex items-end justify-around border-y border-gray-400 bg-gray-700 text-gray-50 dark:bg-gray-800" :style="shelfStyle">
        <!-- TODO: Renderizar Segmentos/Produtos aqui -->
        <draggable
            v-model="sortableSegments"
            item-key="id"
            handle=".drag-segment-handle"
            class="relative flex w-full items-end justify-around"
            :style="segmentsContainerStyle"
        >
            <template #item="{ element: segment }">
                <Segment :key="segment.id" :shelf="shelf" :segment="segment" :scale-factor="scaleFactor" />
            </template>
        </draggable>
        <div class="absolute inset-0 bottom-0 z-0 flex h-full w-full items-center justify-center">
            <ShelfContent :shelf="shelf" @drop-product="(product: Product, shelf: Shelf, dropPosition: any) => $emit('drop-product',product, shelf,  dropPosition)" />
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, defineEmits, defineProps, ref, watch } from 'vue';
import draggable from 'vuedraggable';
import { useGondolaStore } from '../../../store/gondola';
import Segment from './Segment.vue';
import ShelfContent from './ShelfContent.vue';
import { Segment as SegmentType, Shelf, Product} from './types';

// Definir Props
const props = defineProps<{
    shelf: Shelf;
    scaleFactor: number;
    sectionWidth: number;
    sectionHeight: number;
    baseHeight: number;
    rackWidth: number; // Nova prop para a largura da cremalheira
}>();
const dragShelfActive = ref(false); // Estado para rastrear se a prateleira está sendo arrastada
const shelftext = ref(`Shelf (Pos: ${props.shelf.shelf_position.toFixed(1)}cm)`); // Texto da prateleira
// Definir Emits
const emit = defineEmits(['drop-product']); // Para quando um produto é solto na prateleira
watch(dragShelfActive, (newValue) => {
    if (newValue) {
        // Adicionar lógica para quando a prateleira está sendo arrastada
        console.log('Prateleira arrastada');
        shelftext.value = `Arrastando Prateleira (Pos: ${props.shelf.shelf_position.toFixed(1)}cm)`;
    } else {
        // Adicionar lógica para quando a prateleira não está mais sendo arrastada
        console.log('Prateleira não arrastada');
        shelftext.value = `Shelf (Pos: ${props.shelf.shelf_position.toFixed(1)}cm)`;
    }
});
const gondolaStore = useGondolaStore(); // Instanciar o gondola store
// --- Computeds para Estilos ---
const shelfStyle = computed(() => {
    // Convertemos a posição da prateleira para pixels usando o fator de escala
    const topPosition = props.shelf.shelf_position * props.scaleFactor;

    // Retornamos o estilo final com tipagem correta (as CSSProperties)
    return {
        position: 'absolute' as const, // Use 'as const' para tipar corretamente
        left: '-4px',
        width: `${props.sectionWidth * props.scaleFactor + 4}px`,
        height: `${props.shelf.shelf_height * props.scaleFactor}px`,
        top: `${topPosition}px`,
        zIndex: '1',
    };
});

// --- Lógica de Drag and Drop (para produtos) ---

/**
 * Referência local aos segmentos para o draggable
 * Aplica ordenamento e garante IDs para todos os segmentos
 */
const sortableSegments = computed<SegmentType[]>({
    get() {
        // Garantir que todos os segmentos tenham IDs
        return props.shelf.segments;
    },
    set(newSegments: SegmentType[]) {
        // Garantir que a ordenação está atualizada antes de emitir o evento
        const reorderedSegments = newSegments.map((segment, index) => ({
            ...segment,
            ordering: index + 1,
        }));
        // Emitir evento para o componente pai (Section) lidar com a atualização 

        gondolaStore.updateShelf(props.shelf.id, {
            segments: reorderedSegments,
        });
    },
});
/**
 * Computed property para estilo do container de segmentos
 * Define a altura baseada na altura da prateleira
 */
const segmentsContainerStyle = computed(() => {
    return {
        height: `${props.shelf.shelf_height * props.scaleFactor}px`,
    };
});
</script>

<style scoped>
.shelf-container {
    /* Adicionar transições se houver feedback visual no dragover */
    transition: border-color 0.2s ease-in-out;
}

/* Estilo para feedback visual ao arrastar sobre */
.shelf-container.drag-over {
    border-color: theme('colors.blue.500');
    /* background-color: theme('colors.blue.50 / 50%'); */
}

.drag-over {
    background-color: rgba(59, 130, 246, 0.1);
    border-color: rgba(59, 130, 246, 0.5);
    border-width: 2px;
    border-style: dashed;
    border-radius: 4px;
    /* Adicionar sombra se necessário */
    box-shadow: 0 0 10px rgba(59, 130, 246, 0.5);
    /* Adicionar transição suave */
    transition:
        border-color 0.2s ease-in-out,
        background-color 0.2s ease-in-out;
    /* Aumentar a area de drop */
    padding: 0 0 30px 0;
    /* Adicionar um efeito de escala */
}
</style>
