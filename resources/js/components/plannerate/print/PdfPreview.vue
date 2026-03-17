<script setup lang="ts">
import { computed, ref } from 'vue'
import { Button } from '~/components/ui/button'
import PdfSection from './partials/PdfSection.vue'
import Indicator from '../v3/Indicator.vue'
import { usePlanogramEditor } from '@planogram/composables/plannerate/v3/usePlanogramEditor'
import DropdownPerformance from '../v3/DropdownPerformance.vue'
import type { AbcAnalysis, Gondola, Section, StockAnalysis } from '@planogram/types/planogram'
import { usePdfGenerator } from '@planogram/composables/plannerate/usePdfGenerator'
import PdfModuleSelector from './PdfModuleSelector.vue'

interface Props {
    gondola: Pick<Gondola, 'id' | 'name' | 'scale_factor' | 'alignment'>
    sections: Section[]
    analysis?: {
        abc?: AbcAnalysis
        stock?: StockAnalysis
        [key: string]: any
    }
}

const props = defineProps<Props>()
const editor = usePlanogramEditor()
const pdfGenerator = usePdfGenerator()

const isDownloading = ref(false)
const layoutDirection = ref<'column' | 'row'>('row')
const showModuleSelector = ref(false)

async function generatePDF(autoDownload = false, selectedSectionIds?: string[]) {
    const isExportingRef = autoDownload ? isDownloading : pdfGenerator.isGenerating

    try {
        isExportingRef.value = true

        const layoutMode = layoutDirection.value === 'row' ? 'single' : 'multiple'
        const orientation = layoutDirection.value === 'row' ? 'landscape' : 'portrait'
        const filename = `gondola_${props.gondola.name}_${new Date().toISOString().split('T')[0]}.pdf`

        // Se foram selecionados módulos específicos, filtra os elementos
        let specificElements: HTMLElement[] | undefined = undefined
        
        if (selectedSectionIds && selectedSectionIds.length > 0) {
            const allModules = document.querySelectorAll<HTMLElement>('[data-module-section]')
            specificElements = Array.from(allModules).filter(element => {
                const sectionId = element.getAttribute('data-section-id')
                return sectionId && selectedSectionIds.includes(sectionId)
            })
        }

        await pdfGenerator.generatePdf(
            {
                mode: layoutMode,
                selector: '[data-module-section]',
            },
            {
                filename,
                orientation,
                format: 'a4',
                marginTop: layoutMode === 'single' ? 10 : 20,
                marginSides: 10,
                marginBottom: 10,
            },
            autoDownload,
            specificElements
        )
    } catch (error) {
        alert('Erro ao gerar PDF: ' + (error instanceof Error ? error.message : 'Erro desconhecido'))
    } finally {
        isExportingRef.value = false
    }
}

// function handlePreviewPdf() {
//     // Se estiver em modo row (horizontal), gera direto com todos os módulos
//     if (layoutDirection.value === 'row') {
//         generatePDF(false)
//     } else {
//         showModuleSelector.value = true
//     }
// }

function handleDownloadPdf() {
    // Se estiver em modo row (horizontal), gera direto com todos os módulos
    if (layoutDirection.value === 'row') {
        generatePDF(true)
    } else {
        showModuleSelector.value = true
    }
}

async function handleGenerateFromSelector(data: { sectionIds: string[], autoDownload: boolean }) {
    showModuleSelector.value = false
    await generatePDF(data.autoDownload, data.sectionIds)
}

function toggleLayout() {
    layoutDirection.value = layoutDirection.value === 'column' ? 'row' : 'column'
}
// Computed para direção do fluxo
const flowDirection = computed(
    () => editor.currentGondola.value?.flow || 'left_to_right',
);
const isLeftToRight = computed(() => flowDirection.value === 'left_to_right');

// ABC Classification
// const _abcClassification = useAbcClassification();

const extraHeight = 50; // Espaço extra para cremalheiras e labels (aumentado de 50 para 100)
</script>

<template>
    <div class="bg-transparent">
        <!-- Toolbar fixo -->
        <div class="fixed top-0 left-0 right-0 z-[500] bg-white/95 border-b border-slate-200">
            <div class="max-w-screen-xl mx-auto flex h-16 items-center justify-between px-4">
                <div>
                    <h1 class="text-xl font-semibold">{{ gondola.name }}</h1>
                    <p class="text-sm text-slate-500">{{ sections.length }} módulos</p>
                </div>

                <div class="flex gap-2">
                    <DropdownPerformance :gondola="gondola" :analysis="analysis" />
                    <Button @click="toggleLayout" variant="outline" size="sm"
                        :title="layoutDirection === 'column' ? 'Mudar para linha' : 'Mudar para coluna'">
                        <Rows v-if="layoutDirection === 'column'" class="mr-2 h-4 w-4" />
                        <Columns v-else class="mr-2 h-4 w-4" />
                        {{ layoutDirection === 'column' ? 'Em Linha' : 'Em Coluna' }}
                    </Button> 

                    <Button @click="handleDownloadPdf" :disabled="pdfGenerator.isGenerating.value || isDownloading" size="sm">
                        <Loader2 v-if="isDownloading" class="mr-2 h-4 w-4 animate-spin" />
                        <Download v-else class="mr-2 h-4 w-4" />
                        {{ isDownloading ? 'Baixando...' : 'Baixar PDF' }}
                    </Button>
                </div>
            </div>
        </div>
        <div class="mt-16 relative">
            <!-- Indicator de Direção da Gôndola - Discreto -->
            <Indicator :isLeftToRight="isLeftToRight" />
        </div>
        <!-- Conteúdo dos módulos -->
        <div class="pt-24 pb-12 w-full h-full"
            :class="layoutDirection === 'row' ? 'overflow-x-auto' : 'overflow-visible'">
            <div class="flex h-full"
                :class="[
                    layoutDirection === 'column' ? 'flex-col items-center gap-24 w-full' : 'flex-row items-start gap-0 px-6 w-max'
                ]">
                <PdfSection v-for="(section, index) in sections" :key="section.id" :section="section"
                    :scale-factor="gondola.scale_factor || 1" :alignment="gondola.alignment ?? 'default'" :index="index"
                    :layout-direction="layoutDirection" :extra-height="extraHeight" :data-section-id="section.id" />
            </div>
        </div>

        <!-- Modal de seleção de módulos -->
        <PdfModuleSelector
            v-model:open="showModuleSelector"
            :sections="sections"
            @generate="handleGenerateFromSelector"
        />
    </div>
</template>
