<template>
    <div class="flex flex-col  md:flex-row">
        <div class="mt-28 flex px-10 md:flex-row">
            <draggable  v-model="sortableSections" item-key="id" handle=".drag-handle" @end="onDragEnd" class="flex md:flex-row">
                <template #item="{ element: section, index }">
                    <div :key="section.id">
                        <div class="flex items-center">
                            <Cremalheira :section="section" :scale-factor="scaleFactor" @delete-section="deleteSection">
                                <template #actions>
                                    <Button
                                        size="sm"
                                        class="drag-handle h-6 w-6 cursor-move p-0 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                                        variant="secondary"
                                    >
                                        <MoveIcon class="h-3 w-3" />
                                    </Button>
                                </template>
                            </Cremalheira>
                            <Section
                                :section="section"
                                :scale-factor="scaleFactor"
                                :selected-category="selectedCategory"
                                @move-shelf-to-section="handleMoveShelfToSection"
                                @segment-select="$emit('segment-select', $event)"
                                @update-shelves="handleMoveSegmentToSection"
                                @update:quantity="updateSegmentQuantity"
                                @update:segments="handleMoveSegmentToSection"
                                @delete-section="deleteSection"
                            />
                        </div>
                    </div>
                </template>
            </draggable>

            <div v-if="lastSectionData" class="flex items-center">
                <Cremalheira :section="lastSectionData" :scale-factor="scaleFactor" :is-last-section="true" :key="`rack-end-${lastSectionData.id}`"/>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { MoveIcon } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import Cremalheira from './Cremalheira.vue';
import Section from './Section.vue';
// @ts-ignore
import { Button } from '@/components/ui/button';
// @ts-ignore
import { round } from 'lodash';
// import {VueDraggableNext } from 'vue-draggable-next'
import draggable from 'vuedraggable';
import { useEditorStore } from '../../../store/editor';

interface Category {
    id: string | number;
    name: string;
}

const props = defineProps({
    gondola: {
        type: Object,
        required: true,
    },
    selectedCategory: {
        type: Object as () => Category | null,
        default: null,
    },
});

const emit = defineEmits(['sections-reordered', 'shelves-updated', 'move-shelf-to-section', 'segment-select']);

const editorStore = useEditorStore();
const scaleFactor = computed(() => {
    return editorStore.scaleFactor;
});

const sortableSections = ref([...(props.gondola.sections || [])]);
watch(() => props.gondola.sections, (newSections) => {
    sortableSections.value = [...(newSections || [])];
}, { deep: true });

const lastSectionData = computed(() => {
    const sections = sortableSections.value;
    return sections.length > 0 ? sections[sections.length - 1] : null;
});

const onDragEnd = () => {
    const orderedIds = sortableSections.value.map(s => s.id);
    emit('sections-reordered', sortableSections.value, props.gondola.id);
    // TODO: Chamar API para salvar a nova ordem das seções
    // apiService.post(`gondolas/${props.gondola.id}/sections/reorder`, { section_ids: orderedIds });
};

const deleteSection = (section: any) => {
     
    sortableSections.value = sortableSections.value.filter((s: any) => s.id !== section.id);
};

const handleMoveShelfToSection = (shelf: any, sectionId: number) => {
    router.put(
        // @ts-ignore
        route('planogram.shelves.update-section', shelf.id),
        {
            section_id: sectionId,
            new_position: round(shelf.shelf_position),
        },
        {
            preserveState: false,
            preserveScroll: true,
            onSuccess: () => {
                // Handle success if needed
            },
            onError: () => {
                // Handle error if needed
            },
            onFinish: () => {
                // Reset the state if needed
            },
        },
    );
};

const handleMoveSegmentToSection = (segment: any, sectionId: number) => {
    router.put(
        // @ts-ignore
        route('planogram.segments.reorder', segment.shelfId),
        segment,
        {
            preserveState: false,
            preserveScroll: true,
            onSuccess: () => {
                // Handle success if needed
            },
            onError: () => {
                // Handle error if needed
            },
            onFinish: () => {
                // Reset the state if needed
            },
        },
    );
};

const updateSegmentQuantity = (segment: any) => {
    router.put(
        // @ts-ignore
        route('planogram.segments.update', segment.segmentId),
        segment.data,
        {
            preserveState: false,
            preserveScroll: true,
            onSuccess: () => {
                // Handle success if
            },
            onError: () => {
                // Handle error if needed
            },
            onFinish: () => {
                // Reset the state if needed
            },
        },
    );
};
</script>

<style scoped>
/* Estilos para o modo escuro específicos do componente Sections, se necessário */
@media (prefers-color-scheme: dark) {
    .drag-handle {
        /* Ajustes adicionais para o ícone de arrastar no modo escuro, se necessário */
        filter: brightness(1.1);
    }
}
</style>
