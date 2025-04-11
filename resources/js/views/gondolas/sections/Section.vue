<template>
    <div :style="sectionStyle">
        <!-- Conteúdo da Seção (Prateleiras) -->
        <Shelf
            v-for="shelf in section.shelves"
            :key="shelf.id"
            :shelf="shelf"
            :scale-factor="scaleFactor"
            :section-width="props.section.width"
            :section-height="props.section.height"
            :base-height="baseHeight"
            :rack-width="section.rackWidth || section.cremalheira_width || 4"
            @drop-product="handleProductDropOnShelf"
        />
    </div>
</template>

<script setup lang="ts">
import { computed, defineEmits, defineProps, ref } from 'vue';
import { apiService } from '../../../services';
import { useGondolaStore } from '../../../store/gondola';
import { useToast } from './../../../components/ui/toast';
import Shelf from './Shelf.vue'; // Importar o componente Shelf
import { Product, Segment, Shelf as ShelfType } from './types';

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

// Definir Emits (se a Section precisar emitir eventos para cima)
const emit = defineEmits(['update:segments']); // Exemplo: se precisar emitir atualizações de segmentos
const gondolaStore = useGondolaStore(); // Instanciar o gondola store
// Services
const { toast } = useToast();
// --- Computeds para Estilos ---
const draggingSection = ref(false);
// Altura da base em pixels
const baseHeight = computed(() => {
    const baseHeightCm = props.section.base_height || 0;
    if (baseHeightCm <= 0) return 0;
    return baseHeightCm * props.scaleFactor;
});

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

// Estilo da base (altura - posicionada fora do div principal por causa do margin-bottom)
const baseStyle = computed(() => ({
    height: (props.section?.baseHeight || props.section?.base_height || 17) * props.scaleFactor + 'px',
    // Faz a base ficar abaixo do container principal
    bottom: `-${(props.section?.baseHeight || props.section?.base_height || 17) * props.scaleFactor}px`,
}));

// --- Lógica de Eventos ---

/**
 * Lida com o evento drop-product emitido por um componente Shelf.
 * @param {object} eventData - Dados do evento { product, shelfId, dropPosition }.
 */
const handleProductDropOnShelf = (product: Product, shelf: ShelfType, dropPosition: any) => {
    console.log('Produto solto na prateleira:', shelf);
    // TODO: Implementar lógica para criar/adicionar o segmento do produto
    // - Calcular a posição X relativa dentro da prateleira baseado em eventData.dropPosition.x
    // - Chamar API para criar o segmento
    // - Atualizar o estado local/emitir evento para atualizar a UI
    // Exemplo de chamada API (pseudo-código):
    // gondolaStore.updateShelf(shelf, {});
    const newSegment: Segment = {
        gondolaId: gondolaStore.currentGondola.id,
        id: `segment-${Date.now()}-${shelf.segments?.length}`,
        width: parseInt(props.section.width.toString()),
        ordering: (shelf.segments.length || 0) + 1,
        quantity: 1,
        spacing: 0,
        position: 0,
        preserveState: false,
        status: 'published',
        // Cria layer com informações do produto
        layer: {
            product_id: product.id,
            product_name: product.name,
            product_image: product.image,
            product: product,
            height: product.height,
            spacing: 0,
            quantity: 1,
            status: 'published',
        },
    };
    // Adiciona o novo segmento à prateleira
    try {
        apiService
            .post(`shelves/${shelf.id}/segments`, {
                segment: newSegment,
            })
            .then((response) => {
                // Atualizar o estado local ou emitir um evento para atualizar a UI
                // Atualizar a lista de segmentos na prateleira correspondente em props.section.shelves
                gondolaStore.updateShelf(response.data.id, response.data);
                // Ou emitir um evento para o componente pai recarregar os dados

                toast({
                    title: 'Success',
                    description: response.message,
                    variant: 'default',
                });
                // emit('update:segments', { shelfId: eventData.shelfId, newSegment: response.data });
            });
    } catch (error) {
        console.error('Erro ao adicionar segmento à prateleira:', error);
        toast({
            title: 'Error',
            description: 'Failed to add segment to shelf.',
            variant: 'destructive',
        });
    }
};

// Função auxiliar (exemplo)
/*
const calculatePositionX = (dropX: number): number => {
    // Converter a posição X do drop (em pixels) para a unidade de medida do backend (ex: cm)
    const shelfPixelWidth = (props.section.width - (props.section.rackWidth || 4) * 2) * props.scaleFactor;
    const shelfCmWidth = props.section.width - (props.section.rackWidth || 4) * 2;
    return (dropX / shelfPixelWidth) * shelfCmWidth;
}
*/
</script>

<style scoped>
/* Adiciona um z-index para garantir que a base fique atrás do conteúdo */
.section-container > .absolute.bottom-0 {
    z-index: -1;
}
</style>
