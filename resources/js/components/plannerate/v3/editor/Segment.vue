<template>
    <!-- Segment com drop direto (troca de posições) -->
    <div
        class="relative flex flex-col items-start transition-all duration-200"
        :class="{
            'ring-3 ring-primary ring-offset-2 bg-primary/20 shadow-xl scale-[1.02] animate-pulse z-50': isSegmentSelected,
            'ring-2 ring-amber-500/70 ring-offset-1 bg-amber-100/50 shadow-lg z-40':
                isEanMatch && !isSegmentSelected && !isDropTarget,
            'hover:opacity-90':
                !isSegmentSelected && !isDragging && !isDropTarget,
            'cursor-grabbing opacity-40': isDragging,
            'cursor-grab': !isDragging && !isDropTarget,
            'cursor-pointer': isDropTarget,
            'scale-105 bg-primary/10 shadow-lg ring-4 ring-primary animate-pulse':
                isDropTarget,
        }"
        draggable="true"
        @click="handleSegmentClick"
        @dragstart="handleDragStart"
        @dragend="handleDragEnd"
        @dragover.prevent="handleDragOver"
        @dragleave="handleDragLeave"
        @drop.prevent="handleDrop"
        :data-segment-id="segment.id"
        :data-layer-id="layer?.id"
        data-segment="true"
    >
        <!-- Indicator visual de performance A, B, C -->
        <AbcBadge :classification="abcClassification" />
        
        <!-- Indicator visual de estoque alvo -->
        <StockIndicator :segment="segment" :shelf-depth="shelfDepth" @click="handleSegmentClick" />
     
        <!-- Indicator visual de drop -->
        <div
            v-if="isDropTarget"
            class="pointer-events-none absolute inset-0 z-50 flex items-center justify-center rounded bg-primary/20 backdrop-blur-sm"
        >
            <div class="rounded-full bg-primary p-2 shadow-lg">
                <svg
                    class="size-6 text-primary-foreground"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="3"
                        d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"
                    />
                </svg>
            </div>
        </div>

        <div
            v-for="(_, index) in getQuantity"
            :key="`segment-layer-${index}`"
            class="flex flex-col"
        >
            <div v-if="layer">
                <LayerRenderer
                    :layer="layer"
                    :segment="segment"
                    :scale="props.scale"
                    :is-selected="isLayerSelected"
                />
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import {
    draggingSegmentShelfId,
    eanSearchQuery,
} from '@planogram/composables/plannerate/v3/editor/useGondolaState';
import { usePlanogramEditor } from '@planogram/composables/plannerate/v3/usePlanogramEditor';
import { usePlanogramSelection } from '@planogram/composables/plannerate/v3/usePlanogramSelection';
import { useAbcClassification } from '@planogram/composables/plannerate/v3/useAbcClassification';
import { Layer, Segment } from '@planogram/types/planogram';
import { computed, ref } from 'vue';
import LayerRenderer from './Layer.vue';
import AbcBadge from './AbcBadge.vue';
import StockIndicator from './StockIndicator.vue';

interface Props {
    segment: Segment;
    scale: number;
    isFirstInShelf?: boolean;
    isLastInShelf?: boolean;
    shelfDepth?: number;
}

const props = defineProps<Props>();
const layer = computed<Layer | undefined>(() => props.segment.layer);
const selection = usePlanogramSelection();
const editor = usePlanogramEditor();
const { getClassification } = useAbcClassification();

const getQuantity = computed(() => props.segment.quantity || 1);

// Busca classificação ABC do produto pelo EAN
const abcClassification = computed(() => {
    const ean = layer.value?.product?.ean;
    return getClassification(ean);
});

const isEanMatch = computed(() => {
    const query = eanSearchQuery.value.trim();
    const productEan = String(layer.value?.product?.ean ?? '').trim();

    if (!query || !productEan) {
        return false;
    }

    return productEan.includes(query);
});

const isSegmentSelected = computed(() => {
    return selection.isSegmentSelected(props.segment);
});

const isLayerSelected = computed(() => {
    return layer.value ? selection.isLayerSelected(layer.value) : false;
});

// Estado de dragging e drop
const isDragging = ref(false);
const isDropTarget = ref(false);

function handleSegmentClick(event: MouseEvent) {
    event.stopPropagation(); 
    selection.selectItem('segment', props.segment.id, props.segment);
}

function handleDragStart(event: DragEvent) {
    event.stopPropagation();
    isDragging.value = true;

    // Armazena o shelf_id globalmente para que outras shelves possam verificar
    draggingSegmentShelfId.value = props.segment.shelf_id || null;

    if (event.dataTransfer) {
        // Define o tipo de operação: copy se Ctrl estiver pressionado, senão move
        event.dataTransfer.effectAllowed =
            event.ctrlKey || event.metaKey ? 'copy' : 'move';

        // Define os dados do segmento
        event.dataTransfer.setData(
            'application/x-segment-id',
            props.segment.id,
        );
        event.dataTransfer.setData(
            'application/x-segment-shelf-id',
            props.segment.shelf_id || '',
        );
        event.dataTransfer.setData('text/plain', `Segment ${props.segment.id}`);

        // Armazena se é cópia ou movimento
        event.dataTransfer.setData(
            'application/x-is-copy',
            (event.ctrlKey || event.metaKey).toString(),
        );

        // Define uma imagem de arrastar customizada
        const dragImage = event.currentTarget as HTMLElement;
        if (dragImage) {
            event.dataTransfer.setDragImage(dragImage, 20, 20);
        }
    }
}

function handleDragEnd() {
    isDragging.value = false;
    // Limpa o shelf_id global
    draggingSegmentShelfId.value = null;
}

// Handler para dragover - aceita segments da mesma shelf
function handleDragOver(event: DragEvent) {
    if (!event.dataTransfer) return;

    // Só aceita segments (não produtos) da mesma shelf usando o estado global
    if (event.dataTransfer.types.includes('application/x-segment-id')) {
        // Verifica se é da mesma shelf usando o estado global
        if (draggingSegmentShelfId.value === props.segment.shelf_id) {
            event.dataTransfer.dropEffect = 'move';
            isDropTarget.value = true;
        }
    }
}

function handleDragLeave(event: DragEvent) {
    // Só limpa se realmente saiu do elemento (não de um filho)
    const rect = (event.currentTarget as HTMLElement).getBoundingClientRect();
    const x = event.clientX;
    const y = event.clientY;

    if (x < rect.left || x >= rect.right || y < rect.top || y >= rect.bottom) {
        isDropTarget.value = false;
    }
}

// Handler para drop - troca de posições
function handleDrop(event: DragEvent) {
    if (!event.dataTransfer) return;

    const draggedSegmentId = event.dataTransfer.getData(
        'application/x-segment-id',
    );

    if (draggedSegmentId && draggedSegmentId !== props.segment.id) {
        // Troca posições usando o editor (registra no histórico)
        editor.swapSegmentPositions(draggedSegmentId, props.segment.id);
    }

    isDropTarget.value = false;
}
</script>
