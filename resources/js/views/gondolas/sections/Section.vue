<template>
    <div :style="sectionStyle">

        <!-- Conteúdo da Seção (Prateleiras, Produtos) -->
        <div class="absolute inset-0 overflow-hidden">
            <!-- TODO: Renderizar prateleiras e produtos aqui -->
            <p class="p-4 text-center text-xs text-gray-400">Section Content (Shelves/Products)</p>
            <!-- Exemplo: <Shelf v-for="shelf in section.shelves" :key="shelf.id" :shelf="shelf" :scale-factor="scaleFactor" /> -->
        </div>

    </div>
</template>

<script setup lang="ts">
import { computed, defineProps, ref } from 'vue';

// Definir Props
const props = defineProps({
    section: {
        type: Object as () => Record<string, any>,
        required: true,
    },
    scaleFactor: {
        type: Number,
        required: true,
        default: 1,
    },
});
const draggingSection = ref(false)
// --- Computeds para Estilos --- 


const sectionStyle = computed(() => {
    return {
        width: `${props.section.width * props.scaleFactor}px`,
        height: `${props.section.height * props.scaleFactor}px`,
        position: 'relative' as const,
        borderWidth: '2px',
        borderStyle: draggingSection.value ? 'dashed' : 'solid',
        borderColor: draggingSection.value ? 'rgba(59, 130, 246, 0.5)' : 'transparent',
        backgroundColor: draggingSection.value ? 'rgba(59, 130, 246, 0.1)' : 'transparent',
        overflow: 'visible' as const,
    };
});


</script>

<style scoped>
/* Estilos específicos se necessário */
</style>