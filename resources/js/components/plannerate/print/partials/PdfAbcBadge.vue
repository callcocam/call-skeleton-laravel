<template>
    <div
        v-if="classification && isVisible"
        class="absolute -top-1 -right-1 z-50 flex items-center justify-center rounded-full font-black shadow-lg border-2 border-white"
        :class="badgeClasses"
        :style="{ 
            fontSize: `${Math.max(5 * scale, 10)}px`,
            width: `${Math.max(9 * scale, 18)}px`,
            height: `${Math.max(9 * scale, 18)}px`,
        }"
        :title="`Classificação ABC: ${classification}`"
    >
        {{ classification }}
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useAbcClassification } from '@planogram/composables/plannerate/v3/useAbcClassification';

const { isVisible } = useAbcClassification();

interface Props {
    classification?: 'A' | 'B' | 'C';
    scale?: number;
}

const props = withDefaults(defineProps<Props>(), {
    scale: 1,
});

/**
 * Classes CSS baseadas na classificação ABC
 * - A (Verde): Alta performance - produtos premium
 * - B (Amarelo): Média performance - produtos intermediários  
 * - C (Vermelho): Baixa performance - produtos de cauda longa
 */
const badgeClasses = computed(() => {
    switch (props.classification) {
        case 'A':
            return 'bg-green-500 text-white';
        case 'B':
            return 'bg-yellow-500 text-gray-900';
        case 'C':
            return 'bg-red-500 text-white';
        default:
            return 'bg-gray-400 text-white';
    }
});
</script>
