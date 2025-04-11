<template>
    <div 
        class="layer group flex justify-between cursor-pointer"
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
import { computed, ref } from 'vue';
import Product from './Product.vue';
import { Layer, Segment } from './types';
import { useProductStore } from '../../../store/product'; // Corrected relative path

const props = defineProps<{
    layer: Layer;
    segment: Segment;
    scaleFactor: number;
}>();

const productStore = useProductStore();

const layerSpacing = ref(props.layer.spacing);
const layerQuantity = ref(props.layer.quantity || 1);

const layerStyle = computed(() => {
    const topPosition = props.layer.layer_position * props.scaleFactor;
    return {
        position: 'absolute' as const,
        left: '0px',
        width: `${props.layer.layer_width * props.scaleFactor}px`,
        height: `${props.layer.layer_height * props.scaleFactor}px`,
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
        productStore.toggleProductSelection(productIdAsString);
    } else {
        // Check current selection state for the clicked product
        const isCurrentlySelected = productStore.selectedProductIds.has(productIdAsString);
        const selectionSize = productStore.selectedProductIds.size;

        if (isCurrentlySelected && selectionSize === 1) {
            // Clicked on the item that was already the only selected item -> Deselect it
            productStore.clearSelection();
        } else {
            // Clicked on an unselected item, or on one of multiple selected items
            // -> Clear previous selection and select only this one
            productStore.clearSelection();
            productStore.selectProduct(productIdAsString);
        }
    }
};

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
