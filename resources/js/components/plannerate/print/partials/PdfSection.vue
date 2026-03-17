<script setup lang="ts">
import { computed } from 'vue'
import PdfRack from './PdfRack.vue'
import PdfShelf from './PdfShelf.vue'
import { useArrayNavigation } from '@planogram/composables/plannerate/useArrayNavigation'
import type { Section } from '@planogram/types/planogram'

interface Props {
    index?: number
    layoutDirection?: 'column' | 'row'
    section: Section
    scaleFactor: number
    alignment: string
    extraHeight?: number
}

const props = defineProps<Props>()

const rackWidth = computed(() => (props.section.cremalheira_width  ?? 0) * props.scaleFactor)
const sectionWidth = computed(() => props.section.width * props.scaleFactor)
const sectionHeight = computed(() => (props.section.height + (props.extraHeight ?? 0)) * props.scaleFactor)

// Em modo row, apenas o primeiro módulo tem cremalheira esquerda
const showLeftRack = computed(() => {
    if (props.layoutDirection === 'column') {
        return true
    } else {
        return (props.index ?? 0) === 0
    }
})
const totalWidth = computed(() => sectionWidth.value + rackWidth.value * (showLeftRack.value ? 2 : 1))
const leftOffset = computed(() => (showLeftRack.value ? rackWidth.value : 0))

// Helper para navegação nas shelves
const { getPrevious: previousShelf } = useArrayNavigation(props.section.shelves || [])
</script>

<template>
    <div :data-module-section="section.id" :data-section-id="section.id" :data-module-order="section.ordering" 
        class="relative bg-white"
        :class="props.layoutDirection === 'row' ? 'mt-0' : 'mt-12'"
        :style="{
            width: `${totalWidth}px`,
            height: `${sectionHeight}px`,
            marginLeft: props.layoutDirection === 'row' ? `${leftOffset}px` : '0',
        }">
        <!-- Rack Esquerda (apenas no primeiro módulo em modo row, ou sempre em column) -->
        <PdfRack
            :width="rackWidth" 
            side="left" 
            :section="section"
            :scale="scaleFactor"
            :extra-height="extraHeight"
            v-if="showLeftRack"
        />
           
        <!-- Prateleiras -->
        <PdfShelf 
            v-for="shelf in section.shelves" 
            :key="shelf.id" 
            :shelf="shelf" 
            :section="section" 
            :section-width="sectionWidth"
            :scale-factor="scaleFactor" 
            :cremalheira-width="leftOffset" 
            :alignment="alignment" 
            :extra-height="extraHeight"
            :previous-shelf="previousShelf(shelf)"
        />

        <!-- Rack Direita (sempre visível) -->
        <PdfRack
            :width="rackWidth" 
            side="right"
            :section="section"
            :scale="scaleFactor"
            :extra-height="extraHeight"
        />

        <!-- Label do módulo -->
        <div class="absolute bottom-0 left-0 flex w-full items-center justify-center">
            <div class="text-xs text-slate-500">Módulo #{{ section.ordering }}</div>
        </div>
    </div>
</template>
