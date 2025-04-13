<template>
    <div class="segment drag-segment-handle group relative flex items-center justify-center" :style="segmentStyle">
        <Layer
            v-for="(quantity, index) in segmentQuantity"
            :key="index"
            :shelf="shelf"
            :segment="segment"
            :layer="segment.layer"
            :scale-factor="scaleFactor"
            @increase="onIncreaseQuantity"
            @decrease="onDecreaseQuantity"
            @spacingIncrease="onSpacingIncrease"
            @spacingDecrease="onSpacingDecrease"
        />
    </div>
</template>
<script setup lang="ts">
import { computed, ref } from 'vue';
import { useGondolaStore } from '../../../store/gondola'; // Corrected relative path
import { useProductStore } from '../../../store/product'; // Corrected relative path
import Layer from './Layer.vue';
import { Layer as LayerType, Segment, Shelf } from './types';

const props = defineProps<{
    segment: Segment;
    shelf: Shelf;
    scaleFactor: number;
}>();

const segmentSelected = ref(false); // State to track if the segment is selected
/** Segment quantity (number of layers) */
const segmentQuantity = ref(props.segment.quantity);

const productStore = useProductStore(); // Instance of the product store
const gondolaStore = useGondolaStore(); // Instance of the gondola store

// Computed para o estilo do segmento
// ----------------------------------------------------
// Computed Properties
// ----------------------------------------------------
/**
 * Calculate segment style based on properties and selection state
 */
const segmentStyle = computed(() => {
    // Calculate segment dimensions
    const layerHeight = props.segment.layer.product.height * segmentQuantity.value * props.scaleFactor;
    const layerWidth = props.segment.layer.product.width * props.segment.layer.quantity * props.scaleFactor;

    // Conditional style when segment is selected
    const selectedStyle = segmentSelected.value
        ? {
              border: '2px solid blue',
              boxShadow: '0 0 5px rgba(0, 0, 255, 0.5)',
              outline: 'none',
          }
        : {};
    // Return complete style object
    return {
        height: `${layerHeight}px`,
        width: `${layerWidth}px`,
        marginBottom: `${props.shelf.shelf_height * props.scaleFactor}px`,
        ...selectedStyle,
    };
});

// Function to increase quantity
const onIncreaseQuantity = (layer: LayerType) => {
    productStore.updateLayerQuantity(layer, layer.quantity);

    // Update the segment quantity in the gondola store
    const segment = {
        ...props.segment,
        layer: {
            ...layer,
            quantity: layer.quantity + 1,
        },
    };
    gondolaStore.updateShelf(
        props.shelf.id,
        {
            segment,
        },
        false,
    );
};
// Function to decrease quantity
const onDecreaseQuantity = (layer: LayerType) => {
    productStore.updateLayerQuantity(layer, layer.quantity);
};
// Function to increase spacing
const onSpacingIncrease = (layer: LayerType) => {
    productStore.updateLayerSpacing(layer, layer.spacing);
};
// Function to decrease spacing
const onSpacingDecrease = (layer: LayerType) => {
    productStore.updateLayerSpacing(layer, layer.spacing);
};
</script>
