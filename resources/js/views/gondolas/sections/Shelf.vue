<template>
    <div
        class="shelf-container shelf relative flex items-end justify-around border-y border-gray-400 bg-gray-700 text-gray-50 dark:bg-gray-800"
        :style="shelfStyle"
    >
        <!-- TODO: Renderizar Segmentos/Produtos aqui -->
        <Segment v-for="segment in segments" :key="segment.segment_id" :shelf="shelf" :segment="segment" :scale-factor="scaleFactor" />
        <div
            class="flex h-full w-full items-center justify-center"
            @dragover.prevent="handleDragOver"
            @drop.prevent="handleDrop"
            @dragleave="handleDragLeave"
        >
            <span class="text-xs text-gray-100 dark:text-gray-700">Shelf (Pos: {{ shelf.shelf_position.toFixed(1) }}cm)</span>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, defineEmits, defineProps } from 'vue';
import Segment from './Segment.vue';
import { Shelf } from './types';

// Definir Props
const props = defineProps<{
    shelf: Shelf;
    scaleFactor: number;
    sectionWidth: number;
    sectionHeight: number;
    baseHeight: number;
    rackWidth: number; // Nova prop para a largura da cremalheira
}>();

// Definir Emits
const emit = defineEmits(['drop-product']); // Para quando um produto é solto na prateleira

// --- Computeds para Estilos --- 
const segments = computed(() => props.shelf.segments);
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

const handleDragOver = (event: DragEvent) => {
    // Permite que itens sejam soltos aqui
    event.preventDefault();
    if (event.dataTransfer) {
        event.dataTransfer.dropEffect = 'copy'; // Ou 'move' se for o caso
    }
    // TODO: Adicionar feedback visual (ex: mudar borda)
    if (event.currentTarget) {
        (event.currentTarget as HTMLElement).classList.add('drag-over');
    }
};

const handleDragLeave = (event: DragEvent) => {
    // Remove o feedback visual quando o item não está mais sobre a prateleira
    if (event.currentTarget) {
        (event.currentTarget as HTMLElement).classList.remove('drag-over');
    }
};

const handleDrop = (event: DragEvent) => {
    event.preventDefault();
    if (event.dataTransfer) {
        const productData = event.dataTransfer.getData('text/product');
        if (productData) {
            try {
                const product = JSON.parse(productData);
                // Emitir evento para o componente pai (Section) lidar com a adição
                emit('drop-product', product, props.shelf, { x: event.offsetX, y: event.offsetY });
            } catch (e) {
                console.error('Erro ao processar dados do produto solto:', e);
            }
            // TODO: Remover feedback visual
            if (event.currentTarget) {
                (event.currentTarget as HTMLElement).classList.remove('drag-over');
            }
        }
    }
};
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
    padding: 30px 0 0 0;
    /* Adicionar um efeito de escala */
    transform: scale(1.02);
}
</style>
