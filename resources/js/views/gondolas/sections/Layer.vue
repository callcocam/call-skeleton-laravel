<template>
    <div class="layer group flex justify-between" :style="layerStyle">
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

const props = defineProps<{
    layer: Layer;
    segment: Segment;
    scaleFactor: number;
}>();

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
    };
});
</script>
