<template>
    <div 
        class="shelf-controls"
        @mouseenter="isHovering = true"
        @mouseleave="isHovering = false"
    >
        <!-- Área central para movimento vertical da prateleira -->
        <div 
            class="absolute inset-0 z-10 flex h-full w-full cursor-ns-resize items-center justify-center" 
            @mousedown="handleVerticalDragStart"
        ></div>

        <!-- Botões aparecem apenas quando o mouse está sobre a prateleira -->
        <transition name="fade">
            <!-- Botão para mover horizontalmente a prateleira para a esquerda -->
            <div 
                v-show="isHovering"
                class="absolute left-0 top-0 z-20 h-full w-5 cursor-pointer bg-blue-500 hover:bg-blue-600 flex items-center justify-center" 
                @click="moveHorizontal('left')"
            >
                <ChevronLeftIcon class="h-4 w-4 text-white" />
            </div>
        </transition>
        
        <transition name="fade">
            <!-- Botão para mover horizontalmente a prateleira para a direita -->
            <div 
                v-show="isHovering"
                class="absolute right-0 top-0 z-20 h-full w-5 cursor-pointer bg-blue-500 hover:bg-blue-600 flex items-center justify-center" 
                @click="moveHorizontal('right')"
            >
                <ChevronRightIcon class="h-4 w-4 text-white" />
            </div>
        </transition>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useGondolaStore } from '../../../store/gondola';
import { ChevronLeftIcon, ChevronRightIcon } from 'lucide-vue-next';

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

// Estado para controlar a visibilidade dos botões
const isHovering = ref(false);

// Estados para controle da manipulação
const isDragging = ref(false);
const dragType = ref<'vertical' | 'horizontal-move' | null>(null);

// Valores iniciais para cálculos de movimento
const initialMouseY = ref(0);
const initialShelfY = ref(0);

/**
 * Manipula movimento horizontal da prateleira usando os botões
 * Move a prateleira em incrementos fixos para esquerda ou direita
 */
const moveHorizontal = (direction: 'left' | 'right') => {
    // Obtém a posição atual, ou assume 0 se não definida
    const currentPosition = props.shelf.shelf_x_position || 0;
    
    // Define o incremento de movimento (unidades lógicas)
    const moveIncrement = 10; // Ajuste este valor conforme necessário
    
    // Calcula a nova posição baseada na direção
    let newPosition = currentPosition;
    
    if (direction === 'left') {
        newPosition = Math.max(0, currentPosition - moveIncrement);
    } else { // direction === 'right'
        const maxPosition = props.sectionWidth - (props.shelf.shelf_width || props.sectionWidth);
        newPosition = Math.min(maxPosition, currentPosition + moveIncrement);
    }
    
    // Apenas atualiza se a posição de fato mudou
    if (newPosition !== currentPosition) {
        // Atualiza no store e persiste no servidor
        gondolaStore.updateShelf(props.shelf.id, {
            shelf_x_position: newPosition
        });
        
        console.log(`Prateleira movida para ${direction}: nova posição X = ${newPosition}`);
    }
};

/**
 * Inicia o arrasto vertical (movimento da prateleira para cima/baixo)
 */
const handleVerticalDragStart = (e: MouseEvent) => {
    isDragging.value = true;
    dragType.value = 'vertical';
    
    // Armazena a posição inicial do mouse
    initialMouseY.value = e.clientY;
    
    // Armazena a posição inicial da prateleira
    initialShelfY.value = props.shelf.shelf_position || 0;
    
    // Adiciona os event listeners para movimento e soltura
    document.addEventListener('mousemove', handleMouseMove);
    document.addEventListener('mouseup', handleMouseUp);
    
    // Previne comportamentos padrão indesejados
    e.preventDefault();
    
    console.log('Iniciando arrasto vertical da prateleira');
};

/**
 * Handler para o movimento do mouse durante arrasto
 */
const handleMouseMove = (e: MouseEvent) => {
    if (!isDragging.value) return;
    
    if (dragType.value === 'vertical') {
        handleVerticalMove(e);
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
 * Finaliza o arrasto quando o mouse é solto
 * Persiste as alterações finais e remove os event listeners
 */
const handleMouseUp = () => {
    if (isDragging.value) {
        // Persiste as alterações no servidor
        if (dragType.value === 'vertical') {
            gondolaStore.updateShelf(props.shelf.id, {
                shelf_position: props.shelf.shelf_position,
            });
        }
        
        console.log(`Finalizando arrasto da prateleira`);
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
/* Bordas arredondadas para os botões de navegação */
.absolute.left-0 {
    border-top-right-radius: 4px;
    border-bottom-right-radius: 4px;
}

.absolute.right-0 {
    border-top-left-radius: 4px;
    border-bottom-left-radius: 4px;
}

/* Animação de fade para os botões */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>