<template>
    <div class="space-y-4">
        <div class="mb-4 flex items-center">
            <div class="rounded-full bg-gray-100 p-2 dark:bg-gray-700">
                <LayoutGridIcon class="h-5 w-5 dark:text-gray-200" />
            </div>
            <h3 class="ml-2 text-lg font-medium dark:text-gray-100">Configurar Módulos</h3>
        </div>

        <!-- Configuração dos Módulos -->
        <div class="space-y-2">
            <h4 class="text-sm font-medium dark:text-gray-200">Configurações de Módulos</h4>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="space-y-2">
                    <Label for="num_modulos" class="dark:text-gray-200">Número de Módulos</Label>
                    <Input id="num_modulos" type="number" v-model="formLocal.num_modulos" min="1" @change="updateForm" class="dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100" />
                    <p class="text-xs text-gray-500 dark:text-gray-400">Quantidade de módulos na gôndola</p>
                </div>
                <div class="space-y-2">
                    <Label for="height" class="dark:text-gray-200">Altura dos Módulos (cm)</Label>
                    <Input id="height" type="number" v-model="formLocal.height" min="1" @change="updateForm" class="dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100" />
                    <p class="text-xs text-gray-500 dark:text-gray-400">Altura de cada módulo da seção</p>
                </div>
                <div class="space-y-2">
                    <Label for="width" class="dark:text-gray-200">Largura dos Módulos (cm)</Label>
                    <Input id="width" type="number" v-model="formLocal.width" min="1" @change="updateForm" class="dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100" />
                    <p class="text-xs text-gray-500 dark:text-gray-400">Largura de cada módulo da seção</p>
                </div>
            </div>
        </div> 
        <!-- Visualização dos Módulos -->
        <div class="mt-5 flex justify-center rounded-lg border bg-gray-50 p-4 dark:bg-gray-800 dark:border-gray-700">
            <div class="flex space-x-4">
                <div v-for="modulo in parseInt(formLocal.num_modulos)" :key="modulo" class="w-16 border border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600">
                    <div class="flex h-40 items-center justify-center text-xs text-gray-400 dark:text-gray-300">Módulo {{ modulo }}</div>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-blue-100 bg-blue-50 p-4 dark:bg-blue-900/20 dark:border-blue-800">
            <p class="text-sm text-blue-800 dark:text-blue-300">
                <span class="font-medium">Dica:</span> A configuração de módulos define quantas divisões verticais a gôndola terá. Cada módulo pode
                ter suas próprias prateleiras.
            </p>
        </div>
    </div>
</template>

<script setup>
import { Input } from '../../../../components/ui/input';
import { Label } from '../../../../components/ui/label';
import { LayoutGridIcon } from 'lucide-vue-next';
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

// Gerar código para a seção se não existir
const gerarCodigoSecao = () => {
    const prefixo = 'SEC';
    const random = Math.floor(Math.random() * 10000)
        .toString()
        .padStart(4, '0');
    return `${prefixo}-${random}`;
};

// Inicializar valores padrão
onMounted(() => {
    if (!formLocal.section_code) {
        formLocal.section_code = gerarCodigoSecao();
        updateForm();
    }
});

// Observar mudanças nas props e atualizar o formulário local
watch(
    () => props.formData,
    (newVal) => {
        Object.assign(formLocal, newVal);
    },
    { deep: true },
);

// Atualizar o código da seção manualmente
const updateSectionCode = () => {
    if (!formLocal.section_code) {
        formLocal.section_code = gerarCodigoSecao();
    }
    updateForm();
};

// Função para emitir os dados atualizados ao componente pai
const updateForm = () => {
    emit('update:form', { ...formLocal });
};
</script>
