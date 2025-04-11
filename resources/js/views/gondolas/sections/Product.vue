<template> 
    <div class="product" :style="productStyle" ref="productRef" ></div>
</template>
<script setup lang="ts">
import { computed, ref } from 'vue';
import { Product } from './types';
const props = defineProps<{
    product: Product;
    scaleFactor: number;
    productSpacing: number;
}>();
  
const productRef = ref(null) as any;
const productStyle = computed(() => {  
    return {
        width: `${props.product.width * props.scaleFactor}px`,
        height: `${props.product.height * props.scaleFactor}px`,
        zIndex: '2',
        backgroundImage: `url(${props.product.image_url})`,
        backgroundSize: 'contain',
        backgroundRepeat: 'no-repeat',
        backgroundPosition: 'center', 
        margin: `0 ${props.productSpacing * props.scaleFactor}px`,
    };
}); 
</script>