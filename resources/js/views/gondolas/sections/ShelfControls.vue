<template>
    <div class="shelf-controls">
        <!-- Área central para movimento vertical da prateleira -->
        <div class="absolute inset-0 z-10 flex h-full w-full cursor-ns-resize items-center justify-center" @mousedown="handleVerticalDragStart"></div>

        <!-- Área lateral esquerda para movimento horizontal da prateleira -->
        <div class="absolute left-0 top-0 z-20 h-full w-5 cursor-move" @mousedown="handleHorizontalMoveStart"></div>

        <!-- Área lateral direita para redimensionamento horizontal da prateleira -->
        <div class="absolute right-0 top-0 z-20 h-full w-5 cursor-ew-resize" @mousedown="handleHorizontalResizeStart"></div>

        <!-- Canto inferior direito para redimensionamento em ambas dimensões -->
        <div class="absolute bottom-0 right-0 z-30 h-5 w-5 cursor-nwse-resize" @mousedown="handleFullResizeStart"></div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useGondolaStore } from '../../../store/gondola';

/**
 * Props do componente
 * @property {Object} shelf - Objeto da prateleira que será manipulada
 * @property {Number} scaleFactor - Fator de escala usado para converter entre unidades lógicas e pixels
 * @property {Number} sectionWidth - Largura da seção em unidades lógicas
 * @property {Number} sectionHeight - Altura da seção em unidades lógicas
 * @property {HTMLElement|null} shelfElement - Referência ao elemento DOM da prateleira
 */
const props = defineProps({
    shelf: {
        type: Object,
        required: true,
    },
    scaleFactor: {
        type: Number,
        required: true,
    },
    sectionWidth: {
        type: Number,
        required: true,
    },
    sectionHeight: {
        type: Number,
        required: true,
    },
    baseHeight: {
        type: Number,
        default: 0,
    },
    minWidth: {
        type: Number,
        default: 20,
    },
    minHeight: {
        type: Number,
        default: 5,
    },
    shelfElement: {
        type: [HTMLElement, null],
        default: null,
    },
});

// Store para interagir com o estado global das gôndolas
const gondolaStore = useGondolaStore();

// Estados para controle da manipulação
const isDragging = ref(false);
const dragType = ref<'vertical' | 'horizontal-move' | 'horizontal-resize' | 'full-resize' | null>(null);

// Valores iniciais para cálculos de movimento/redimensionamento
const initialMouseX = ref(0);
const initialMouseY = ref(0);
const initialShelfX = ref(0);
const initialShelfY = ref(0);
const initialShelfWidth = ref(0);
const initialShelfHeight = ref(0);

// --- Handlers para iniciar os diferentes tipos de arrasto ---

/**
 * Inicia o arrasto vertical (movimento da prateleira para cima/baixo)
 */
const handleVerticalDragStart = (e: MouseEvent) => {
    startDrag(e, 'vertical');
};

/**
 * Inicia o arrasto horizontal (movimento da prateleira para esquerda/direita)
 */
const handleHorizontalMoveStart = (e: MouseEvent) => {
    startDrag(e, 'horizontal-move');
    e.stopPropagation(); // Evita propagação para o handler vertical
};

/**
 * Inicia o redimensionamento horizontal da prateleira
 */
const handleHorizontalResizeStart = (e: MouseEvent) => {
    startDrag(e, 'horizontal-resize');
    e.stopPropagation(); // Evita propagação para o handler vertical
};

/**
 * Inicia o redimensionamento em ambas as dimensões
 */
const handleFullResizeStart = (e: MouseEvent) => {
    startDrag(e, 'full-resize');
    e.stopPropagation(); // Evita propagação para outros handlers
};

/**
 * Função comum para iniciar qualquer tipo de arrasto
 * Configura os estados iniciais e adiciona os event listeners
 */
const startDrag = (e: MouseEvent, type: 'vertical' | 'horizontal-move' | 'horizontal-resize' | 'full-resize') => {
    isDragging.value = true;
    dragType.value = type;

    // Armazena as posições iniciais do mouse
    initialMouseX.value = e.clientX;
    initialMouseY.value = e.clientY;

    // Armazena os valores iniciais da prateleira
    initialShelfX.value = props.shelf.shelf_x_position || 0;
    initialShelfY.value = props.shelf.shelf_position || 0;
    initialShelfWidth.value = props.shelf.shelf_width || props.sectionWidth;
    initialShelfHeight.value = props.shelf.shelf_height || 0;

    // Adiciona os event listeners para movimento e soltura
    document.addEventListener('mousemove', handleMouseMove);
    document.addEventListener('mouseup', handleMouseUp);

    // Previne comportamentos padrão indesejados
    e.preventDefault();

    console.log(`Iniciando ${type} da prateleira`);
};

/**
 * Handler global para todos os tipos de movimento do mouse
 * Direciona para a função específica com base no tipo de arrasto
 */
const handleMouseMove = (e: MouseEvent) => {
    if (!isDragging.value) return;

    switch (dragType.value) {
        case 'vertical':
            handleVerticalMove(e);
            break;
        case 'horizontal-move':
            handleHorizontalMove(e);
            break;
        case 'horizontal-resize':
            handleHorizontalResize(e);
            break;
        case 'full-resize':
            handleFullResize(e);
            break;
    }
};

/**
 * Lida com o movimento vertical da prateleira
 */
const handleVerticalMove = (e: MouseEvent) => {
    if (!props.shelfElement) return;

    const containerRect = props.shelfElement.parentElement?.getBoundingClientRect();
    if (!containerRect) return;

    // Calcula a posição Y relativa ao container
    const relativeY = e.clientY - containerRect.top;

    // Limites de arrasto - não permitir arrastar para fora do container
    if (relativeY < 0 || relativeY > containerRect.height) return;

    // Verificação adicional para não ultrapassar o limite inferior
    const maxYPosition = props.sectionHeight * props.scaleFactor - props.baseHeight - props.shelf.shelf_height;
    if (relativeY >= maxYPosition) return;

    // Atualiza a posição da prateleira no store
    gondolaStore.updateShelf(
        props.shelf.id,
        {
            shelf_position: relativeY / props.scaleFactor,
        },
        false,
    );
};

/**
 * Lida com o movimento horizontal da prateleira
 */
const handleHorizontalMove = (e: MouseEvent) => {
    // Calcula a diferença de movimento do mouse
    const deltaX = e.clientX - initialMouseX.value;

    // Calcula a nova posição X
    let newX = initialShelfX.value + deltaX / props.scaleFactor;

    // Define limites para a posição X
    const minX = 0;
    const maxX = props.sectionWidth - props.shelf.shelf_width;

    // Aplica os limites
    newX = Math.max(minX, Math.min(newX, maxX));

    // Atualiza a posição X da prateleira no store
    gondolaStore.updateShelf(
        props.shelf.id,
        {
            shelf_x_position: newX,
        },
        false,
    );
};

/**
 * Lida com o redimensionamento horizontal da prateleira
 */
const handleHorizontalResize = (e: MouseEvent) => {
    // Calcula a diferença de movimento do mouse
    const deltaX = e.clientX - initialMouseX.value;

    // Calcula a nova largura
    let newWidth = initialShelfWidth.value + deltaX / props.scaleFactor;

    // Limites para a largura
    const minWidth = props.minWidth;
    const maxWidth = props.sectionWidth - initialShelfX.value;

    // Aplica os limites
    newWidth = Math.max(minWidth, Math.min(newWidth, maxWidth));

    // Atualiza a largura da prateleira no store
    gondolaStore.updateShelf(
        props.shelf.id,
        {
            shelf_width: newWidth,
        },
        false,
    );
};

/**
 * Lida com o redimensionamento em ambas as dimensões
 */
const handleFullResize = (e: MouseEvent) => {
    // Calcula as diferenças de movimento do mouse
    const deltaX = e.clientX - initialMouseX.value;
    const deltaY = e.clientY - initialMouseY.value;

    // Calcula as novas dimensões
    let newWidth = initialShelfWidth.value + deltaX / props.scaleFactor;
    let newHeight = initialShelfHeight.value + deltaY / props.scaleFactor;

    // Limites para a largura
    const minWidth = props.minWidth;
    const maxWidth = props.sectionWidth - initialShelfX.value;

    // Limites para a altura
    const minHeight = props.minHeight;
    const maxHeight = props.sectionHeight - initialShelfY.value;

    // Aplica os limites
    newWidth = Math.max(minWidth, Math.min(newWidth, maxWidth));
    newHeight = Math.max(minHeight, Math.min(newHeight, maxHeight));

    // Atualiza as dimensões da prateleira no store
    gondolaStore.updateShelf(
        props.shelf.id,
        {
            shelf_width: newWidth,
            shelf_height: newHeight,
        },
        false,
    );
};

/**
 * Finaliza o arrasto quando o mouse é solto
 * Persiste as alterações finais e remove os event listeners
 */
const handleMouseUp = () => {
    if (isDragging.value) {
        // Persiste as alterações no servidor
        const updates: Record<string, any> = {};

        switch (dragType.value) {
            case 'vertical':
                updates.shelf_position = props.shelf.shelf_position;
                break;
            case 'horizontal-move':
                updates.shelf_x_position = props.shelf.shelf_x_position;
                break;
            case 'horizontal-resize':
                updates.shelf_width = props.shelf.shelf_width;
                break;
            case 'full-resize':
                updates.shelf_width = props.shelf.shelf_width;
                updates.shelf_height = props.shelf.shelf_height;
                break;
        }

        gondolaStore.updateShelf(props.shelf.id, updates);
        console.log(`Finalizando ${dragType.value} da prateleira`);
    }

    // Reseta os estados
    isDragging.value = false;
    dragType.value = null;

    // Remove os event listeners
    document.removeEventListener('mousemove', handleMouseMove);
    document.removeEventListener('mouseup', handleMouseUp);
};
</script>

<style scoped>
/* Efeito de hover para os controles */
.absolute:hover {
    background-color: rgba(59, 130, 246, 0.1);
}
</style>
