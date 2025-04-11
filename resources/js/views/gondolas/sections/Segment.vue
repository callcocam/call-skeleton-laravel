<template>
    <div class="segment border segment drag-segment-handle group relative flex " :style="segmentStyle">
        <Layer
            v-for="(quantity, index) in segmentQuantity"
            :key="index"
            :shelf="shelf"
            :segment="segment"
            :layer="segment.layer"
            :scale-factor="scaleFactor"
        />
    </div>
</template>
<script setup lang="ts">
import { computed, ref } from 'vue';
import Layer from './Layer.vue';
import { Segment, Shelf } from './types';

const props = defineProps<{
    segment: Segment;
    shelf: Shelf;
    scaleFactor: number;
}>();

const segmentSelected = ref(false); // State to track if the segment is selected
/** Segment quantity (number of layers) */
const segmentQuantity = ref(props.segment.quantity);

// Computed para o estilo do segmento
// ----------------------------------------------------
// Computed Properties
// ----------------------------------------------------
/**
 * Calculate segment style based on properties and selection state
 */
const segmentStyle = computed(() => {
    // Calculate segment dimensions
    const layerHeight = (props.segment.layer.product.height * segmentQuantity.value) * props.scaleFactor;
    const layerWidth = (props.segment.layer.product.width * props.segment.layer.quantity) * props.scaleFactor;

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
        border: '2px solid #ccc',
        ...selectedStyle,
    };
});
</script>
