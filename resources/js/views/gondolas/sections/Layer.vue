<template>
    <div
        class="layer group flex cursor-pointer justify-between border"
        :style="layerStyle"
        :class="{ 'layer--selected': isSelected }"
        @click="handleLayerClick"
    >
        <Product
            v-for="(quantity, index) in layerQuantity"
            :key="index"
            :product="layer.product"
            :scale-factor="scaleFactor"
            :product-spacing="layerSpacing"
        />
    </div>
</template>
<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { useProductStore } from '../../../store/product'; // Corrected relative path
import Product from './Product.vue';
import { Layer, Segment } from './types';

const props = defineProps<{
    layer: Layer;
    segment: Segment;
    scaleFactor: number;
}>();

const emit = defineEmits<{
    (e: 'increase', layer: Layer): void;
    (e: 'decrease', layer: Layer): void;
    (e: 'spacingIncrease', layer: Layer): void;
    (e: 'spacingDecrease', layer: Layer): void;
}>();

const productStore = useProductStore();

const layerSpacing = ref(props.layer.spacing);
const layerQuantity = ref(props.layer.quantity || 1);
const debounceTimer = ref<ReturnType<typeof setTimeout> | null>(null);
const segmentSelected = ref(false);

const layerStyle = computed(() => {
    const topPosition = props.layer.layer_position * props.scaleFactor;
    const layerHeight = props.layer.product.height;
    const layerWidth = props.layer.quantity * props.layer.product.width;
    return {
        position: 'absolute' as const,
        left: '0px',
        width: `${layerWidth * props.scaleFactor}px`,
        height: `${layerHeight * props.scaleFactor}px`,
        top: `${topPosition}px`,
        zIndex: '2',
        // Add a default border or background for visual clarity
    };
});

// Computed property to check if this layer's product is selected
const isSelected = computed(() => {
    if (!props.layer.product?.id) return false;
    // Ensure ID is treated as string for the Set comparison
    return productStore.selectedProductIds.has(String(props.layer.product.id));
});

// Click handler function
const handleLayerClick = (event: MouseEvent) => {
    const productId = props.layer.product?.id;
    if (!productId) {
        console.error('Layer clicked, but product ID is missing.');
        return;
    }

    const isCtrlOrMetaPressed = event.ctrlKey || event.metaKey;
    const productIdAsString = String(productId); // Convert ID to string once

    if (isCtrlOrMetaPressed) {
        // Toggle selection for this product (adds if not present, removes if present)
        segmentSelected.value = !segmentSelected.value; // Toggle the segment selection
        productStore.toggleProductSelection(productIdAsString);
    } else {
        // Check current selection state for the clicked product
        const isCurrentlySelected = productStore.selectedProductIds.has(productIdAsString);
        const selectionSize = productStore.selectedProductIds.size;

        if (isCurrentlySelected && selectionSize === 1) {
            // Clicked on the item that was already the only selected item -> Deselect it
            productStore.clearSelection();
            segmentSelected.value = false; // Set the segment as selected
        } else {
            // Clicked on an unselected item, or on one of multiple selected items
            // -> Clear previous selection and select only this one
            productStore.clearSelection();
            productStore.selectProduct(productIdAsString);
        }
    }
};

// Function to increase quantity
const onIncreaseQuantity = async () => {
    layerSpacing.value = props.layer.spacing;
    if (productStore.selectedProductIds.size > 1) {
        return;
    }
    emit('increase', {
        ...props.layer,
        quantity: (layerQuantity.value += 1),
    });
};
// Function to decrease quantity
const onDecreaseQuantity = async () => {
    if (productStore.selectedProductIds.size > 1) {
        return;
    }
    if (layerQuantity.value > 1) {
        layerSpacing.value = props.layer.spacing;
        emit('decrease', {
            ...props.layer,
            quantity: (layerQuantity.value -= 1),
        });
    }
};
// Function to increase spacing
const onSpacingIncrease = async () => {
   if(productStore.selectedProductIds.size > 1) {
       return;
    }
    emit('spacingIncrease', {
        ...props.layer,
        spacing: layerSpacing.value++,
    });
};
// Function to decrease spacing
const onSpacingDecrease = async () => {
   if(productStore.selectedProductIds.size > 1) {
       return;
    }
    if (layerSpacing.value > 0) {
        emit('spacingDecrease', {
            ...props.layer,
            spacing: layerSpacing.value--,
        });
    }
};

// ----------------------------------------------------
// Lifecycle hooks
// ----------------------------------------------------
// Registra o ouvinte de eventos quando o componente é montado
onMounted(() => {
    // Adiciona listener de teclado para o documento inteiro
    document.addEventListener('keydown', async (event) => {
        if (isSelected.value) {
            if (event.key === 'ArrowRight') {
                event.preventDefault();
                await onIncreaseQuantity();
            } else if (event.key === 'ArrowLeft') {
                event.preventDefault();
                await onDecreaseQuantity();
            }
            //Verifica se a tecla pressionada é a tecla de espaço
            else if (event.key === ' ') {
                event.preventDefault();
                // Chama a função de aumentar a quantidade
                await onSpacingIncrease();
            }
            //Verifica se a tecla pressionada é a tecla de espaço
            else if (event.key === 'Backspace') {
                event.preventDefault();
                // Chama a função de diminuir a quantidade
                await onSpacingDecrease();
            }
        }
    });
});

// Remove o ouvinte de eventos quando o componente é desmontado
onUnmounted(() => {
    if (debounceTimer.value) clearTimeout(debounceTimer.value);
});
</script>

<style scoped>
.layer--selected {
    /* Add styles for selected layer */
    border: 2px solid blue;
    box-shadow: 0 0 5px rgba(0, 0, 255, 0.5);
    /* Ensure the border doesn't affect layout drastically */
    box-sizing: border-box;
}
</style>
