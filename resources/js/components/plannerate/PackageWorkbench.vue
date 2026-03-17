<script setup lang="ts">
import { computed } from 'vue';
import { useArrayNavigation } from '@planogram/composables/plannerate/useArrayNavigation';
import { useShelfAreaCalculation } from '@planogram/composables/plannerate/useShelfAreaCalculation';
import { useGondolaFields } from '@planogram/composables/plannerate/v3/useGondolaFields';
import {
    calculateUsableHeight,
    useSectionFields,
} from '@planogram/composables/plannerate/v3/useSectionFields';
import {
    calculateTotalDisplayArea,
    useShelfFields,
} from '@planogram/composables/plannerate/v3/useShelfFields';
import { shouldShowDeleteConfirm } from '@planogram/composables/plannerate/v3/usePlanogramUtils';

interface Props {
    branch: string;
    legacyStrategy: string;
    nextMilestone: string;
}

defineProps<Props>();

const gondolaFields = useGondolaFields();
const sectionFields = useSectionFields();
const shelfFields = useShelfFields();
const { calculateShelfArea } = useShelfAreaCalculation();

const migratedModules = [
    'types/planogram.ts',
    'useGondolaFields',
    'useSectionFields',
    'useShelfFields',
    'usePlanogramUtils',
    'useArrayNavigation',
    'useShelfAreaCalculation',
    'analysis/useAnalysisFilters',
];

const navigation = useArrayNavigation(migratedModules);

const packageSnapshot = computed(() => {
    const gondola = gondolaFields.initialFields;
    const section = sectionFields.initialFields;
    const shelf = shelfFields.initialFields;
    const shelfArea = calculateShelfArea({
        shelf: {
            id: 'sandbox-shelf',
            shelf_width: shelf.shelfWidth ?? 100,
            shelf_height: shelf.shelfHeight ?? 4,
            shelf_depth: shelf.shelfDepth ?? 40,
            shelf_position: 60,
            ordering: 1,
        },
        scale: gondola.scaleFactor ?? 3,
    });

    return {
        gondolaCode: gondola.gondolaName,
        moduleName: section.name,
        usableHeight: calculateUsableHeight(
            section.height ?? 0,
            section.baseHeight ?? 0,
        ),
        displayArea: calculateTotalDisplayArea(
            shelf.shelfWidth ?? 0,
            shelf.shelfDepth ?? 0,
            shelf.numShelves ?? 0,
            gondola.numModules ?? 1,
        ),
        shelfAreaHeight: shelfArea.areaHeightCm,
        deleteConfirmVisible: shouldShowDeleteConfirm('section'),
        firstMigratedItem: navigation.getFirst(),
        lastMigratedItem: navigation.getLast(),
    };
});
</script>

<template>
    <section class="grid gap-6">
        <div class="rounded-2xl border border-border bg-card p-6 shadow-sm">
            <p class="text-sm font-medium uppercase tracking-[0.2em] text-muted-foreground">
                Planogram Package
            </p>
            <h1 class="mt-2 text-3xl font-semibold text-foreground">
                Base da migracao pronta para evolucao paralela
            </h1>
            <p class="mt-3 max-w-3xl text-sm leading-6 text-muted-foreground">
                Esta pagina usa assets carregados diretamente do pacote
                <strong class="text-foreground"> laravel-raptor-planogram</strong>,
                enquanto o editor legado segue preservado no app para comparacao durante toda a migracao.
            </p>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <article class="rounded-2xl border border-border bg-background/80 p-5">
                <p class="text-xs font-medium uppercase tracking-[0.18em] text-muted-foreground">Branch</p>
                <p class="mt-2 text-base font-semibold text-foreground">{{ branch }}</p>
            </article>

            <article class="rounded-2xl border border-border bg-background/80 p-5">
                <p class="text-xs font-medium uppercase tracking-[0.18em] text-muted-foreground">Estrategia</p>
                <p class="mt-2 text-sm leading-6 text-foreground">{{ legacyStrategy }}</p>
            </article>

            <article class="rounded-2xl border border-border bg-background/80 p-5">
                <p class="text-xs font-medium uppercase tracking-[0.18em] text-muted-foreground">Proximo marco</p>
                <p class="mt-2 text-sm leading-6 text-foreground">{{ nextMilestone }}</p>
            </article>
        </div>

        <div class="rounded-2xl border border-dashed border-border bg-muted/40 p-6">
            <p class="text-sm font-medium text-foreground">O que esta validado nesta etapa</p>
            <ul class="mt-3 grid gap-2 text-sm text-muted-foreground">
                <li>O app resolve paginas Inertia que vivem dentro do pacote.</li>
                <li>O Vite observa e recompila Vue/TS do pacote local.</li>
                <li>Existe uma rota isolada para testar a nova versao sem tocar no legado.</li>
                <li>Os composables base de campos e utilitarios ja executam a partir do pacote.</li>
            </ul>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <article class="rounded-2xl border border-border bg-card p-6 shadow-sm">
                <p class="text-sm font-medium text-foreground">Snapshot gerado pelos composables do pacote</p>
                <dl class="mt-4 grid gap-3 text-sm">
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-muted-foreground">Codigo inicial da gondola</dt>
                        <dd class="font-medium text-foreground">{{ packageSnapshot.gondolaCode }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-muted-foreground">Nome inicial do modulo</dt>
                        <dd class="font-medium text-foreground">{{ packageSnapshot.moduleName }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-muted-foreground">Altura util calculada</dt>
                        <dd class="font-medium text-foreground">{{ packageSnapshot.usableHeight }} cm</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-muted-foreground">Area total estimada</dt>
                        <dd class="font-medium text-foreground">{{ packageSnapshot.displayArea }} cm2</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-muted-foreground">Area clicavel da prateleira</dt>
                        <dd class="font-medium text-foreground">{{ packageSnapshot.shelfAreaHeight }} cm</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-muted-foreground">Confirmacao de exclusao</dt>
                        <dd class="font-medium text-foreground">
                            {{ packageSnapshot.deleteConfirmVisible ? 'Ativa' : 'Suprimida' }}
                        </dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-muted-foreground">Primeiro item migrado</dt>
                        <dd class="font-medium text-foreground">{{ packageSnapshot.firstMigratedItem }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-muted-foreground">Ultimo item migrado</dt>
                        <dd class="font-medium text-foreground">{{ packageSnapshot.lastMigratedItem }}</dd>
                    </div>
                </dl>
            </article>

            <article class="rounded-2xl border border-border bg-card p-6 shadow-sm">
                <p class="text-sm font-medium text-foreground">Primeiro lote migrado</p>
                <ul class="mt-4 grid gap-2 text-sm text-muted-foreground">
                    <li><span class="text-foreground">types/planogram.ts</span> centralizado no pacote</li>
                    <li><span class="text-foreground">useGondolaFields</span> migrado e pronto para reaproveitamento</li>
                    <li><span class="text-foreground">useSectionFields</span> migrado com calculo de altura util</li>
                    <li><span class="text-foreground">useShelfFields</span> migrado com calculo de area total</li>
                    <li><span class="text-foreground">usePlanogramUtils</span> migrado para o controle local de confirmacao</li>
                    <li><span class="text-foreground">useArrayNavigation</span> migrado para utilitarios de fluxo e selecao</li>
                    <li><span class="text-foreground">useShelfAreaCalculation</span> migrado com calculo de area clicavel</li>
                    <li><span class="text-foreground">useAnalysisFilters</span> migrado para listas de analise</li>
                </ul>
            </article>
        </div>
    </section>
</template>
