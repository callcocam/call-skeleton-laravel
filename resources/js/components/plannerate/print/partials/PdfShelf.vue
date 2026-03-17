<script setup lang="ts">
import PdfSegment from './PdfSegment.vue'
import type { Section, Shelf } from '@planogram/types/planogram'
import { useShelfAreaCalculation } from '@planogram/composables/plannerate/useShelfAreaCalculation'
import { calculateHolePositions } from '@planogram/composables/plannerate/v3/useSectionHoles'
import { DEFAULT_SECTION_FIELDS } from '@planogram/composables/plannerate/v3/useSectionFields'
import { computed } from 'vue'

interface Props {
  shelf: Shelf
  section: Section
  sectionWidth: number
  scaleFactor: number
  rackWidth: number
  alignment: string
  extraHeight?: number
  previousShelf?: Shelf
}

const props = defineProps<Props>()

const { calculateShelfArea } = useShelfAreaCalculation()

const shelfArea = computed(() => {
  return calculateShelfArea({
    shelf: props.shelf,
    previousShelf: props.previousShelf,
    scale: props.scaleFactor,
  })
})

const shelfBasePosition = computed(() => {
  const { areaStartCm } = calculateShelfArea({
    shelf: props.shelf,
    previousShelf: props.previousShelf,
    scale: props.scaleFactor,
  })

  const holePositions = calculateHolePositions(props.section)

  if (holePositions.length === 0) {
    const offsetFromAreaStart = props.shelf.shelf_position - areaStartCm
    return offsetFromAreaStart * props.scaleFactor
  }

  const holeHeight = props.section.hole_height ?? DEFAULT_SECTION_FIELDS.holeHeight
  const shelfHeightCm = props.shelf.shelf_height
  const shelfPositionCm = props.shelf.shelf_position

  let closestHoleIdx = 0
  let minDistance = Math.abs(shelfPositionCm - holePositions[0])

  for (let i = 0; i < holePositions.length; i++) {
    const distance = Math.abs(shelfPositionCm - holePositions[i])
    if (distance < minDistance) {
      minDistance = distance
      closestHoleIdx = i
    }
  }

  const closestHolePos = holePositions[closestHoleIdx]
  const centeredPosition = closestHolePos + (holeHeight - shelfHeightCm) / 2
  const offsetFromAreaStart = centeredPosition - areaStartCm
  return offsetFromAreaStart * props.scaleFactor
})

// Detecta se é tipo hook (gancheira) - produtos pendurados
const isHookType = computed(() => props.shelf.product_type === 'hook')

const justifyContent = props.alignment === 'left' ? 'flex-start' :
                       props.alignment === 'right' ? 'flex-end' :
                       props.alignment === 'center' ? 'center' :
                       props.alignment === 'justify' ? 'space-between' :
                       'flex-start'
</script>

<template>
  <div class="absolute z-[50] "
    :style="{
      top: `${(shelfArea.areaStartCm + (props.extraHeight ?? 0)) * scaleFactor}px`,
      left: `${rackWidth}px`,
      right: `${rackWidth}px`,
      width: `${sectionWidth}px`,
      height: `${shelfArea.areaHeightCm * scaleFactor}px`,
    }">
    <!-- Container dos segmentos -->
    <div class="absolute right-0 left-0 flex gap-0 z-[1] pointer-events-none"
      :class="isHookType ? 'items-start' : 'items-end'"
      :style="
        isHookType
          ? {
              top: `${shelfBasePosition + (shelf.shelf_height * scaleFactor)}px`,
              paddingTop: `${shelf.shelf_height * scaleFactor}px`,
              justifyContent,
            }
          : {
              bottom: 0,
              paddingBottom: `${shelf.shelf_height * scaleFactor}px`,
              justifyContent,
            }
      ">
      <PdfSegment
        v-for="segment in shelf.segments"
        :key="segment.id"
        :segment="segment"
        :scale-factor="scaleFactor"
        :shelf-depth="shelf.shelf_depth"
      />
    </div>

    <!-- Barra da prateleira -->
    <div class="absolute right-0 left-0 z-[1] border-t-2 border-slate-700 bg-slate-800/95 text-slate-300 flex items-center justify-center"
      :style="{
        top: `${shelfBasePosition}px`,
        height: `${shelf.shelf_height * scaleFactor}px`,
      }">
      <span class="font-medium" :style="{ fontSize: `${5 * scaleFactor}px` }">
        Prat #{{ shelf.ordering  }}
      </span>
    </div>
  </div>
</template>
