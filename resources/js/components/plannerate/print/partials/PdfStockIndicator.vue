<template>
    <div
        v-if="stockStatus && stockStatus !== 'unknown' && isVisible"
        class="absolute inset-0 z-20 flex items-center justify-center rounded pointer-events-none"
        :class="{
            'border-3 border-red-600 bg-red-300/70': stockStatus === 'increase',
            'border-3 border-yellow-500 bg-yellow-300/70': stockStatus === 'decrease',
            'border-3 border-green-600 bg-green-300/70': stockStatus === 'ok',
        }"
        :style="{ borderWidth: `${Math.max(2 * scale, 2)}px` }"
    >
        <!-- Ícone centralizado simplificado -->
        <div
            class="rounded-full bg-white shadow-lg"
            :class="{
                'border-2 border-red-600': stockStatus === 'increase',
                'border-2 border-yellow-500': stockStatus === 'decrease',
                'border-2 border-green-600': stockStatus === 'ok',
            }"
            :style="{ padding: `${Math.max(3 * scale, 3)}px` }"
        >
            <!-- Seta para cima (aumentar espaço) -->
            <svg
                v-if="stockStatus === 'increase'"
                class="text-red-600"
                :style="{ width: `${Math.max(12 * scale, 12)}px`, height: `${Math.max(12 * scale, 12)}px` }"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
            
            <!-- Seta para baixo (diminuir espaço) -->
            <svg
                v-if="stockStatus === 'decrease'"
                class="text-yellow-600"
                :style="{ width: `${Math.max(12 * scale, 12)}px`, height: `${Math.max(12 * scale, 12)}px` }"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
            </svg>
            
            <!-- Check (espaço adequado) -->
            <svg
                v-if="stockStatus === 'ok'"
                class="text-green-600"
                :style="{ width: `${Math.max(12 * scale, 12)}px`, height: `${Math.max(12 * scale, 12)}px` }"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useTargetStockAnalysis } from '@planogram/composables/plannerate/v3/useTargetStockAnalysis';
import type { Segment } from '@planogram/types/planogram';

interface Props {
    segment: Segment;
    shelfDepth?: number;
    scale?: number;
}

const props = withDefaults(defineProps<Props>(), {
    scale: 1,
});

const {
    getTargetStockData,
    calculateSegmentCapacity,
    getStockStatus,
    DEFAULT_TOLERANCE,
    isVisible,
} = useTargetStockAnalysis();

// Busca dados de target stock pelo EAN do produto
const stockInfo = computed(() => {
    const ean = props.segment?.layer?.product?.ean;
    if (!ean) return null;
    return getTargetStockData(ean);
});

// Quantidade de frentes (segment quantity)
const segmentQuantity = computed(() => props.segment?.quantity ?? 0);

// Quantidade de produtos por frente (layer quantity)
const layerQuantity = computed(() => props.segment?.layer?.quantity ?? 0);

// Profundidade do produto
const productDepth = computed(() => props.segment?.layer?.product?.depth ?? 0);

// Capacidade total deste segment
const segmentCapacity = computed(() => {
    return calculateSegmentCapacity(
        segmentQuantity.value,
        layerQuantity.value,
        productDepth.value,
        props.shelfDepth ?? 0
    );
});

// Status do estoque (increase, decrease, ok, unknown)
const stockStatus = computed(() => {
    if (!stockInfo.value) return 'unknown';
    
    return getStockStatus(
        segmentCapacity.value,
        stockInfo.value.estoque_alvo,
        DEFAULT_TOLERANCE
    );
});
</script>
