<template>
    <div class="space-y-4">
        <div class="mb-4 flex items-center">
            <div class="rounded-full bg-gray-100 p-2 dark:bg-gray-700">
                <RulerIcon class="h-5 w-5 dark:text-gray-200" />
            </div>
            <h3 class="ml-2 text-lg font-medium dark:text-gray-100">Configurar prateleiras e Gancheiras:</h3>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- Controles à esquerda -->
            <div class="space-y-4">
                <!-- Dimensões das Prateleiras -->
                <div class="space-y-2">
                    <h4 class="text-sm font-medium dark:text-gray-200">Dimensões e Especificações:</h4>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="shelf_height" class="dark:text-gray-200">Espessura (cm)</Label>
                            <Input id="shelf_height" type="number" v-model="formLocal.shelf_height" min="1" @change="updateForm" class="dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100" />
                        </div>

                        <div class="space-y-2">
                            <Label for="shelf_width" class="dark:text-gray-200">Largura (cm)</Label>
                            <Input id="shelf_width" type="number" v-model="formLocal.shelf_width" min="1" @change="updateForm" class="dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100" />
                        </div>

                        <div class="space-y-2">
                            <Label for="shelf_depth" class="dark:text-gray-200">Profundidade (cm)</Label>
                            <Input id="shelf_depth" type="number" v-model="formLocal.shelf_depth" min="1" @change="updateForm" class="dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100" />
                        </div>

                        <div class="space-y-2">
                            <Label for="num_shelves" class="dark:text-gray-200">N° de prateleiras</Label>
                            <Input id="num_shelves" type="number" v-model="formLocal.num_shelves" min="1" @change="updateForm" class="dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100" />
                        </div>
                    </div>
                </div>

                <!-- Tipo de Produto -->
                <div class="space-y-2">
                    <Label class="dark:text-gray-200">Tipo de Produto</Label>
                    <div class="grid grid-cols-2 gap-2">
                        <Button
                            :variant="formLocal.product_type === 'normal' ? 'default' : 'outline'"
                            @click="setProductType('normal')"
                            class="justify-center dark:text-gray-100 dark:border-gray-600"
                            :class="{'dark:bg-primary dark:text-white': formLocal.product_type === 'normal', 'dark:bg-gray-700 dark:hover:bg-gray-600': formLocal.product_type !== 'normal'}"
                        >
                            Normal
                        </Button>
                        <Button
                            :variant="formLocal.product_type === 'penduravel' ? 'default' : 'outline'"
                            @click="setProductType('penduravel')"
                            class="justify-center dark:text-gray-100 dark:border-gray-600"
                            :class="{'dark:bg-primary dark:text-white': formLocal.product_type === 'penduravel', 'dark:bg-gray-700 dark:hover:bg-gray-600': formLocal.product_type !== 'penduravel'}"
                        >
                            Pendurável
                        </Button>
                    </div>
                </div>

                <!-- Dica -->
                <div class="rounded-lg border border-blue-100 bg-blue-50 p-4 dark:bg-blue-900/20 dark:border-blue-800">
                    <p class="text-sm text-blue-800 dark:text-blue-300">
                        <span class="font-medium">Dica:</span> As dimensões da gôndola e da seção podem ser diferentes. Certifique-se de que as
                        medidas são compatíveis para um correto encaixe no planograma.
                    </p>
                </div>

                <!-- Informações de cálculo -->
                <div class="space-y-2 rounded-lg border border-gray-200 p-4 dark:border-gray-700 dark:bg-gray-800">
                    <h4 class="text-sm font-medium dark:text-gray-200">Cálculos e Dimensões</h4>
                    <div class="space-y-1 text-sm dark:text-gray-300">
                        <div class="flex justify-between">
                            <span>Altura total:</span>
                            <span>{{ formLocal.height || 180 }}cm</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Espaçamento entre prateleiras:</span>
                            <span>{{ calcularEspacamento() }}cm</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Área total de exposição:</span>
                            <span>{{ calcularAreaExposicao() }}cm²</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pré-visualização à direita -->
            <div class="space-y-4">
                <h4 class="text-sm font-medium dark:text-gray-200">Pré-visualização:</h4>

                <!-- Visualização da gôndola com prateleiras -->
                <div class="relative flex h-[400px] flex-col items-center rounded-lg border bg-gray-50 p-4 dark:bg-gray-800 dark:border-gray-700">
                    <!-- Container para a gôndola proporcional -->
                    <div
                        class="relative h-full border border-gray-400 bg-white shadow-md dark:border-gray-600 dark:bg-gray-700"
                        :style="{
                            width: `${130}px`,
                            maxHeight: '360px',
                        }"
                    >
                        <!-- Base da gôndola -->
                        <div
                            class="absolute bottom-0 left-0 right-0 border-t border-gray-500 bg-gray-300 dark:border-gray-600 dark:bg-gray-600"
                            :style="{
                                height: `${((formLocal.base_height || 17) / (formLocal.height || 180)) * 360}px`,
                            }"
                        ></div>

                        <!-- Colunas laterais (cremalheiras) -->
                        <div
                            class="absolute bottom-0 left-0 top-0 w-1 bg-gray-600 dark:bg-gray-500"
                            :style="{
                                width: `${formLocal.cremalheira_width || 4}px`,
                            }"
                        >
                            <!-- Furos na cremalheira esquerda -->
                            <div
                                v-for="i in Math.floor(
                                    ((formLocal.height || 180) - (formLocal.base_height || 17)) /
                                        ((formLocal.hole_spacing || 2) + (formLocal.hole_height || 2)),
                                )"
                                :key="`left-${i}`"
                                class="absolute left-0 h-1 w-2 bg-white dark:bg-gray-300"
                                :style="{
                                    height: `${formLocal.hole_height || 2}px`,
                                    width: `${formLocal.hole_width || 2}px`,
                                    bottom: `${((formLocal.base_height || 17) / (formLocal.height || 180)) * 360 + ((i * ((formLocal.hole_spacing || 2) + (formLocal.hole_height || 2))) / (formLocal.height || 180)) * 360}px`,
                                }"
                            ></div>
                        </div>

                        <div
                            class="absolute bottom-0 right-0 top-0 w-1 bg-gray-600 dark:bg-gray-500"
                            :style="{
                                width: `${formLocal.cremalheira_width || 4}px`,
                            }"
                        >
                            <!-- Furos na cremalheira direita -->
                            <div
                                v-for="i in Math.floor(
                                    ((formLocal.height || 180) - (formLocal.base_height || 17)) /
                                        ((formLocal.hole_spacing || 2) + (formLocal.hole_height || 2)),
                                )"
                                :key="`right-${i}`"
                                class="absolute right-0 h-1 w-2 bg-white dark:bg-gray-300"
                                :style="{
                                    height: `${formLocal.hole_height || 2}px`,
                                    width: `${formLocal.hole_width || 2}px`,
                                    bottom: `${((formLocal.base_height || 17) / (formLocal.height || 180)) * 360 + ((i * ((formLocal.hole_spacing || 2) + (formLocal.hole_height || 2))) / (formLocal.height || 180)) * 360}px`,
                                }"
                            ></div>
                        </div>

                        <!-- Prateleiras -->
                        <div
                            v-for="i in parseInt(formLocal.num_shelves || 5)"
                            :key="`shelf-${i}`"
                            class="absolute left-0 right-0 border border-gray-400 bg-gray-200 dark:border-gray-600 dark:bg-gray-400"
                            :style="{
                                height: `${((formLocal.shelf_height || 4) / (formLocal.height || 180)) * 360}px`,
                                bottom:
                                    i === 1
                                        ? `${((formLocal.base_height || 17) / (formLocal.height || 180)) * 360}px`
                                        : `${((formLocal.base_height || 17) / (formLocal.height || 180)) * 360 + (i - 1) * calcularEspacamentoPixels()}px`,
                            }"
                        >
                            <!-- Elementos penduráveis se for o tipo pendurável -->
                            <template v-if="formLocal.product_type === 'penduravel' && i > 1">
                                <div
                                    v-for="j in 6"
                                    :key="`hook-${i}-${j}`"
                                    class="absolute bottom-full bg-gray-500 dark:bg-gray-400"
                                    :style="{
                                        width: '2px',
                                        height: '10px',
                                        left: `${j * 20 - 10}px`,
                                    }"
                                >
                                    <div class="absolute top-0 h-1 w-3 bg-gray-500 dark:bg-gray-400" style="left: -3px; transform: rotate(45deg)"></div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Legenda -->
                <div class="grid grid-cols-2 gap-2 text-xs dark:text-gray-300">
                    <div class="flex items-center">
                        <div class="mr-1 h-3 w-3 border border-gray-400 bg-gray-200 dark:border-gray-600 dark:bg-gray-400"></div>
                        <span>Prateleira</span>
                    </div>
                    <div class="flex items-center">
                        <div class="mr-1 h-3 w-3 bg-gray-300 dark:bg-gray-600"></div>
                        <span>Base</span>
                    </div>
                    <div class="flex items-center">
                        <div class="mr-1 h-3 w-3 bg-gray-600 dark:bg-gray-500"></div>
                        <span>Cremalheira</span>
                    </div>
                    <div v-if="formLocal.product_type === 'penduravel'" class="flex items-center">
                        <div class="relative h-3 w-3">
                            <div class="absolute bg-gray-500 dark:bg-gray-400" style="width: 1px; height: 3px"></div>
                        </div>
                        <span class="ml-1">Gancheira</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Button } from '../../../../components/ui/button';
import { Input } from '../../../../components/ui/input';
import { Label } from '../../../../components/ui/label';
import { RulerIcon } from 'lucide-vue-next';
import { onMounted, reactive, watch } from 'vue';

const props = defineProps({
    formData: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['update:form']);

// Cópia local do formulário para manipulação
const formLocal = reactive({ ...props.formData });

// Iniciar valores padrão
onMounted(() => {
    // Garantir que num_shelves existe e tem valor padrão
    if (!formLocal.num_shelves) {
        formLocal.num_shelves = formLocal.shelf_qty || 5;
    }

    // Garantir que o tipo de produto existe
    if (!formLocal.product_type) {
        formLocal.product_type = formLocal.tipo_produto || 'normal';
    }

    // Sincronizar shelf_qty e num_shelves
    formLocal.shelf_qty = formLocal.num_shelves;

    // Sincronizar tipo_produto e product_type
    formLocal.tipo_produto = formLocal.product_type;

    updateForm();
});

// Observar mudanças nas props e atualizar o formulário local
watch(
    () => props.formData,
    (newVal) => {
        Object.assign(formLocal, newVal);
    },
    { deep: true },
);

// Observar mudanças em num_shelves e manter shelf_qty sincronizado
watch(
    () => formLocal.num_shelves,
    (newVal) => {
        formLocal.shelf_qty = newVal;
        updateForm();
    },
);

// Observar mudanças em product_type e manter tipo_produto sincronizado
watch(
    () => formLocal.product_type,
    (newVal) => {
        formLocal.tipo_produto = newVal;
        updateForm();
    },
);

// Função para emitir os dados atualizados ao componente pai
const updateForm = () => {
    emit('update:form', { ...formLocal });
};

// Função para definir o tipo de produto
const setProductType = (type) => {
    formLocal.product_type = type;
    formLocal.tipo_produto = type;
    updateForm();
};

// Função para calcular o espaçamento entre prateleiras em centímetros
const calcularEspacamento = () => {
    const alturaTotal = formLocal.height || 180;
    const alturaBase = formLocal.base_height || 17;
    const alturaUtil = alturaTotal - alturaBase;
    const numPrateleiras = parseInt(formLocal.num_shelves || 5);
    const espacamento = numPrateleiras > 1 ? (alturaUtil / (numPrateleiras - 1)).toFixed(1) : 0;
    return espacamento;
};

// Função para calcular o espaçamento em pixels para a visualização
const calcularEspacamentoPixels = () => {
    const alturaTotal = formLocal.height || 180;
    const alturaExibicao = 360; // Altura em pixels da visualização
    const espacamentoCm = parseFloat(calcularEspacamento());
    return (espacamentoCm / alturaTotal) * alturaExibicao;
};

// Função para calcular a área total de exposição
const calcularAreaExposicao = () => {
    const largura = formLocal.shelf_width || 4;
    const profundidade = formLocal.shelf_depth || 40;
    const numPrateleiras = parseInt(formLocal.num_shelves || 5);
    const areaTotal = largura * profundidade * numPrateleiras;
    return areaTotal.toFixed(0);
};
</script>
