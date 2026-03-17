<template>
    <!-- Container: Área total da prateleira (do chão/prateleira anterior até esta) -->
    <div data-shelf-area="true" class="group/shelf absolute hover:bg-primary/10" :class="{
        'bg-primary/10 ring-2 ring-primary': isSelected,
    }" :style="shelfAreaStyle" @click="handleSelectShelf" @dragover.prevent="handleProductDragOver"
        @dragleave="handleProductDragLeave" @drop.prevent="handleProductDrop">
        <!-- Segmentos um do lado do outro (horizontalmente) -->
        <div v-if="segments.length > 0" class="absolute right-0 left-0 flex" :class="[
            alignmentClass,
            isHookType ? 'items-start' : 'items-end'
        ]" style="z-index: 50; pointer-events: none" :style="isHookType
                    ? {
                        top: `${shelfBasePosition + shelfHeight}px`,

                    }
                    : {
                        bottom: `${shelfHeight}px`
                    }
                ">
            <Segment v-for="(segment, index) in segments" :key="segment.id" :segment="segment" :scale="scale"
                :sectionWidth="sectionWidth" :shelf-depth="shelf.shelf_depth" :isFirstInShelf="index === 0"
                :isLastInShelf="index === segments.length - 1" style="pointer-events: auto" />
        </div>

        <!-- Drag Handle para mover a shelf -->

        <!-- Área de Drop Personalizada -->
        <Transition enter-active-class="transition-all duration-200 ease-out" enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100" leave-active-class="transition-all duration-150 ease-in"
            leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
            <div v-if="isDropTarget"
                class="pointer-events-none absolute inset-0 z-[500] flex flex-col items-center justify-center gap-2 rounded-sm border-2 border-dashed border-primary bg-primary/10 backdrop-blur-sm">
                <svg class="size-8 text-primary drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                <span class="text-sm font-semibold text-primary drop-shadow-lg">
                    Solte aqui
                </span>
                <span class="text-xs font-medium text-primary/80 drop-shadow">
                    Prat #{{ shelfDisplayNumber }}
                </span>
                <span class="mt-1 text-[10px] font-light text-primary/70">
                    Ctrl para copiar
                </span>
            </div>
        </Transition>

        <!-- Base da prateleira (superfície física) - ARRASTÁVEL -->
        <div data-shelf="true" draggable="true" :style="{
            height: `${shelfHeight}px`,
            top: `${shelfBasePosition}px`
        }"
            class="absolute right-0 z-[100] left-0 hover:z-[1000] cursor-grab active:cursor-grabbing border-t-2 border-slate-700 bg-slate-800/95 dark:border-slate-600 dark:bg-slate-700/95"
            :class="{
                'opacity-50 ring-2 ring-primary cursor-grabbing': isDraggingShelf,
                'hover:border-slate-600 hover:bg-slate-700/95 hover:ring-1 hover:ring-slate-500':
                    !isDraggingShelf,
            }" @mousedown="handleMouseDown" @dragstart.stop="handleShelfDragStart" @dragend.stop="handleShelfDragEnd"
            @click.stop="handleSelectShelf">
            <!-- Shelf label -->
            <div class="pointer-events-none absolute top-1/2 left-1/2 flex -translate-x-1/2 -translate-y-1/2 items-center justify-center"
                :style="{ fontSize: `${Math.max(8, Math.min(16, 10 * scale / 3))}px`, zIndex: 1 }">
                <span class="px-2 font-medium text-slate-300 flex items-center">
                    Prat #{{ shelfDisplayNumber }}
                </span>
            </div>

            <!-- Indicator visual de arrasto (aparece no hover) -->

        </div>
    </div>
</template>

<script setup lang="ts">
import {
    draggingShelfId,
    draggingShelfOffset,
    draggingShelfSectionId,
} from '@planogram/composables/plannerate/v3/editor/useGondolaState';
import { useShelfDragDrop } from '@planogram/composables/plannerate/v3/editor/useShelfDragDrop';
import { usePlanogramEditor } from '@planogram/composables/plannerate/v3/usePlanogramEditor';
import { usePlanogramSelection } from '@planogram/composables/plannerate/v3/usePlanogramSelection';
import { useShelfAreaCalculation } from '@planogram/composables/plannerate/useShelfAreaCalculation';
import { calculateHolePositions } from '@planogram/composables/plannerate/v3/useSectionHoles';
import { DEFAULT_SECTION_FIELDS } from '@planogram/composables/plannerate/v3/useSectionFields';
import type { Section, Shelf as ShelfType } from '@planogram/types/planogram';
import { computed, onBeforeUnmount, ref } from 'vue';
import Segment from './Segment.vue';

interface Props {
    shelf: ShelfType;
    section: Section;
    scale?: number;
    holes?: any[];
    sectionWidth?: number;
    sectionHeight?: number;
    rackWidth?: number;
    previousShelf?: ShelfType;
    nextShelf?: ShelfType;
    firstShelf?: ShelfType;
    lastShelf?: ShelfType;
    isLast?: boolean;
}

const props = defineProps<Props>();

const scale = computed(() => props.scale || 3);

const shelfId = computed(() => props.shelf.id);

const selection = usePlanogramSelection();
const editor = usePlanogramEditor();
const { calculateShelfArea } = useShelfAreaCalculation();

// Usa composable para drag & drop de produtos/segments apenas
const {
    isDropTarget,
    handleDragOver: handleProductDragOver,
    handleDragLeave: handleProductDragLeave,
    handleDrop: handleProductDrop,
} = useShelfDragDrop(shelfId.value);

// ===== Drag da Shelf =====
const DRAG_THRESHOLD = 2;
const isDraggingShelf = ref(false);
const canDrag = ref(false);
const mouseDownPos = ref<{ x: number; y: number } | null>(null);
const initialShelfPosition = ref<number | null>(null);

const cleanup = () => {
    document.removeEventListener('mousemove', handleGlobalMouseMove);
    document.removeEventListener('mouseup', handleGlobalMouseUp);
    mouseDownPos.value = null;
    canDrag.value = false;
    initialShelfPosition.value = null;
};

function handleGlobalMouseMove(event: MouseEvent) {
    if (!mouseDownPos.value) return;
    const dx = event.clientX - mouseDownPos.value.x;
    const dy = event.clientY - mouseDownPos.value.y;
    if (Math.sqrt(dx * dx + dy * dy) >= DRAG_THRESHOLD) canDrag.value = true;
}

function handleGlobalMouseUp() {
    cleanup();
}

function handleMouseDown(event: MouseEvent) {
    if (event.button !== 0) return;
    mouseDownPos.value = { x: event.clientX, y: event.clientY };
    canDrag.value = false;
    // Captura posição inicial para histórico
    initialShelfPosition.value = props.shelf.shelf_position;
    document.addEventListener('mousemove', handleGlobalMouseMove);
    document.addEventListener('mouseup', handleGlobalMouseUp);
}

function handleShelfDragStart(event: DragEvent) {
    if (!canDrag.value) {
        event.preventDefault();
        return;
    }

    isDraggingShelf.value = true;
    draggingShelfId.value = props.shelf.id;
    draggingShelfSectionId.value = props.section.id;

    // Garante que temos a posição inicial
    if (initialShelfPosition.value === null) {
        initialShelfPosition.value = props.shelf.shelf_position;
    }

    const target = event.currentTarget as HTMLElement;
    const rect = target.getBoundingClientRect();
    draggingShelfOffset.value = event.clientY - rect.top;

    if (event.dataTransfer) {
        event.dataTransfer.effectAllowed = 'move';
        event.dataTransfer.setData('shelfId', props.shelf.id);
        event.dataTransfer.setData('sectionId', props.section.id);
        event.dataTransfer.setDragImage(target, event.offsetX, event.offsetY);
    }
}

function handleShelfDragEnd() {
    cleanup();

    // A detecção de colisão agora é feita em Section.vue no handleSectionDrop
    // antes de aplicar a posição final

    isDraggingShelf.value = false;
    draggingShelfId.value = null;
    draggingShelfSectionId.value = null;
    draggingShelfOffset.value = 0;
    initialShelfPosition.value = null;
}

onBeforeUnmount(cleanup);

// ===== Computeds de Dimensões =====
const shelfWidth = computed(() => props.sectionWidth);

const shelfHeight = computed(() => props.shelf.shelf_height * scale.value);

// ===== Estilo da ÁREA TOTAL (do chão/anterior até esta prateleira) =====
const shelfAreaStyle = computed(() => {
    // Força reatividade: acessa as posições como dependências explícitas
    // (não são usadas diretamente, mas garantem que o computed recalcule)
    void props.shelf.shelf_position;
    void props.previousShelf?.shelf_position;
    void props.previousShelf?.shelf_height;

    const { areaStartCm, areaHeightCm } = calculateShelfArea({
        shelf: props.shelf,
        previousShelf: props.previousShelf,
        scale: scale.value,
    });

    return {
        top: `${areaStartCm * scale.value}px`,
        width: `${shelfWidth.value}px`,
        height: `${areaHeightCm * scale.value}px`,
        left: `${props.rackWidth}px`,
        right: `-${props.rackWidth}px`,
    };
});

// Posição da base da prateleira dentro da área (relativa ao topo da área)
const shelfBasePosition = computed(() => {
    const { areaStartCm } = calculateShelfArea({
        shelf: props.shelf,
        previousShelf: props.previousShelf,
        scale: scale.value,
    });

    // Calcula as posições dos furos para snap
    const holePositions = calculateHolePositions(props.section);

    if (holePositions.length === 0) {
        const offsetFromAreaStart = props.shelf.shelf_position - areaStartCm;
        return offsetFromAreaStart * scale.value;
    }

    // Dimensões do furo
    const holeHeight = (props.section.hole_height ?? DEFAULT_SECTION_FIELDS.holeHeight);
    const shelfHeightCm = props.shelf.shelf_height;

    // Posição atual da shelf
    const shelfPositionCm = props.shelf.shelf_position;

    // Encontra o furo mais próximo
    let closestHoleIdx = 0;
    let minDistance = Math.abs(shelfPositionCm - holePositions[0]);

    for (let i = 0; i < holePositions.length; i++) {
        const distance = Math.abs(shelfPositionCm - holePositions[i]);
        if (distance < minDistance) {
            minDistance = distance;
            closestHoleIdx = i;
        }
    }

    // Posição do furo mais próximo
    const closestHolePos = holePositions[closestHoleIdx];

    // Centraliza a prateleira dentro do furo
    // A prateleira deve estar no centro do furo: posição_furo + (altura_furo - altura_prateleira) / 2
    const centeredPosition = closestHolePos + (holeHeight - shelfHeightCm) / 2;

    // Offset do início da área
    const offsetFromAreaStart = centeredPosition - areaStartCm;
    return offsetFromAreaStart * scale.value;
});

// ===== Seleção & Segmentos =====
const isSelected = computed(() => selection.isShelfSelected(props.shelf));
const segments = computed(() =>
    props.shelf.segments?.filter(s => !s.deleted_at) || []
);

// Detecta se é tipo hook (gancheira) - produtos pendurados
const isHookType = computed(() => props.shelf.product_type === 'hook');

const shelfDisplayNumber = computed(() => {
    if (!props.section?.shelves) return 1;
    const sorted = [...props.section.shelves]
        .filter(s => !s.deleted_at)
        .sort((a, b) => (b.shelf_position || 0) - (a.shelf_position || 0));
    return Math.max(1, sorted.findIndex(s => s.id === props.shelf.id) + 1);
});

const alignmentClass = computed(() => {
    const align = editor.currentGondola.value?.alignment ?? 'justify';
    const map: Record<string, string> = {
        left: 'justify-start',
        right: 'justify-end',
        center: 'justify-center',
        justify: 'justify-between',
    };
    return map[align] || 'justify-start';
});

function handleSelectShelf(event: MouseEvent) {
    event.stopPropagation();
    selection.selectItem('shelf', props.shelf.id, props.shelf, {
        section: props.section,
        lastShelf: props.lastShelf,
        firstShelf: props.firstShelf,
    });
}
</script>
