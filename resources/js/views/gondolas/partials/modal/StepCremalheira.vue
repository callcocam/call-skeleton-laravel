<template>
    <div class="space-y-4">
        <div class="mb-4 flex items-center">
            <div class="rounded-full bg-gray-100 p-2 dark:bg-gray-700">
                <GripVerticalIcon class="h-5 w-5 dark:text-gray-200" />
            </div>
            <h3 class="ml-2 text-lg font-medium dark:text-gray-100">Configurar Cremalheira</h3>
        </div>

        <!-- Dimensões da Cremalheira -->
        <div class="space-y-2">
            <h4 class="text-sm font-medium dark:text-gray-200">Dimensões da Cremalheira</h4>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="space-y-2">
                    <Label for="cremalheira_width" class="dark:text-gray-200">Largura da Cremalheira (cm)</Label>
                    <Input id="cremalheira_width" type="number" v-model="formLocal.cremalheira_width" min="1" @change="updateForm" class="dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100" />
                    <p class="text-xs text-gray-500 dark:text-gray-400">Largura da coluna vertical (cremalheira)</p>
                </div>
            </div>
        </div>

        <!-- Configuração dos Furos -->
        <div class="space-y-2">
            <h4 class="text-sm font-medium dark:text-gray-200">Configuração dos Furos</h4>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="space-y-2">
                    <Label for="hole_height" class="dark:text-gray-200">Altura do Furo (cm)</Label>
                    <Input id="hole_height" type="number" v-model="formLocal.hole_height" min="1" @change="updateForm" class="dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100" />
                </div>

                <div class="space-y-2">
                    <Label for="hole_width" class="dark:text-gray-200">Largura do Furo (cm)</Label>
                    <Input id="hole_width" type="number" v-model="formLocal.hole_width" min="1" @change="updateForm" class="dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100" />
                </div>

                <div class="space-y-2">
                    <Label for="hole_spacing" class="dark:text-gray-200">Espaçamento entre Furos (cm)</Label>
                    <Input id="hole_spacing" type="number" v-model="formLocal.hole_spacing" min="1" @change="updateForm" class="dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100" />
                    <p class="text-xs text-gray-500 dark:text-gray-400">Distância vertical entre furos</p>
                </div>
            </div>
        </div>

        <!-- Visualização da Cremalheira -->
        <div class="mt-5 rounded-lg border bg-gray-50 p-4 dark:bg-gray-800 dark:border-gray-700">
            <div class="flex justify-center">
                <div class="relative">
                    <!-- Cremalheira (representação visual) -->
                    <div
                        class="relative bg-gray-400 dark:bg-gray-500"
                        :style="{
                            width: `${formLocal.cremalheira_width * 2}px`,
                            height: '200px',
                        }"
                    >
                        <!-- Furos representados como círculos -->
                        <div
                            v-for="i in Math.floor(200 / (formLocal.hole_height * 2 + formLocal.hole_spacing * 2))"
                            :key="i"
                            class="absolute left-1/2 -translate-x-1/2 transform rounded-full border border-gray-300 bg-white dark:border-gray-600 dark:bg-gray-300"
                            :style="{
                                width: `${formLocal.hole_width * 2}px`,
                                height: `${formLocal.hole_height * 2}px`,
                                top: `${i * (formLocal.hole_height * 2 + formLocal.hole_spacing * 2)}px`,
                            }"
                        ></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-blue-100 bg-blue-50 p-4 dark:bg-blue-900/20 dark:border-blue-800">
            <p class="text-sm text-blue-800 dark:text-blue-300">
                <span class="font-medium">Dica:</span> A cremalheira é a estrutura vertical com furos onde as prateleiras são encaixadas. O
                espaçamento entre os furos determina as posições possíveis para as prateleiras.
            </p>
        </div>
    </div>
</template>

<script setup>
import { Input } from '../../../../components/ui/input';
import { Label } from '../../../../components/ui/label';
import { GripVerticalIcon } from 'lucide-vue-next';
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

// Inicializar valores padrão para a cremalheira se não existirem
onMounted(() => {
    if (!formLocal.cremalheira_width) {
        formLocal.cremalheira_width = 4; // Valor padrão conforme migration
    }

    if (!formLocal.hole_height) {
        formLocal.hole_height = 2; // Valor padrão conforme migration
    }

    if (!formLocal.hole_width) {
        formLocal.hole_width = 2; // Valor padrão conforme migration
    }

    if (!formLocal.hole_spacing) {
        formLocal.hole_spacing = 2; // Valor padrão conforme migration
    }

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

// Função para emitir os dados atualizados ao componente pai
const updateForm = () => {
    emit('update:form', { ...formLocal });
};
</script>
