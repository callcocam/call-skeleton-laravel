<template>
    <div class="space-y-4">
        <!-- Informações Básicas -->
        <div class="mb-4 flex items-center">
            <div class="rounded-full bg-gray-100 p-2 dark:bg-gray-700">
                <InfoIcon class="h-5 w-5 dark:text-gray-200" />
            </div>
            <h3 class="ml-2 text-lg font-medium dark:text-gray-100">Informações Básicas</h3>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="space-y-2">
                <Label for="name" class="dark:text-gray-200">Nome da Gôndola *</Label>
                <Input id="name" v-model="formLocal.gondola_name" required @change="updateForm" class="dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100" />
            </div>

            <div class="space-y-2">
                <Label for="location" class="dark:text-gray-200">Localização</Label>
                <Input id="location" v-model="formLocal.location" placeholder="Ex: Setor de bebidas" @change="updateForm" class="dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 dark:placeholder-gray-400" />
                <p class="text-xs text-gray-500 dark:text-gray-400">Corredor onde a gôndola está localizada</p>
            </div>

            <div class="space-y-2">
                <Label for="side" class="dark:text-gray-200">Lado do corredor</Label>
                <Input id="side" v-model="formLocal.side" placeholder="Ex: A, B ou 1, 2" @change="updateForm" class="dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 dark:placeholder-gray-400" />
                <p class="text-xs text-gray-500 dark:text-gray-400">Identificação do lado do corredor</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="space-y-2">
                <Label for="scale_factor" class="dark:text-gray-200">Fator de Escala</Label>
                <Input id="scale_factor" type="number" v-model="formLocal.scale_factor" min="1" @change="updateForm" class="dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100" />
                <p class="text-xs text-gray-500 dark:text-gray-400">Fator para escalar o modelo visual da gôndola</p>
            </div>

            <div class="space-y-2 md:col-span-2">
                <Label class="dark:text-gray-200">Posição Fluxo</Label>
                <div class="grid grid-cols-2 gap-2">
                    <Button
                        :variant="formLocal.flow === 'left_to_right' ? 'default' : 'outline'"
                        @click="setFlow('left_to_right')"
                        class="justify-center dark:text-gray-100 dark:border-gray-600"
                        :class="{'dark:bg-primary dark:text-white': formLocal.flow === 'left_to_right', 'dark:bg-gray-700 dark:hover:bg-gray-600': formLocal.flow !== 'left_to_right'}"
                    >
                        Esquerda para direita
                    </Button>
                    <Button
                        :variant="formLocal.flow === 'right_to_left' ? 'default' : 'outline'"
                        @click="setFlow('right_to_left')"
                        class="justify-center dark:text-gray-100 dark:border-gray-600"
                        :class="{'dark:bg-primary dark:text-white': formLocal.flow === 'right_to_left', 'dark:bg-gray-700 dark:hover:bg-gray-600': formLocal.flow !== 'right_to_left'}"
                    >
                        Direita para esquerda
                    </Button>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Define o sentido de fluxo da gôndola</p>
            </div>
        </div>

        <div class="space-y-2">
            <Label for="status" class="dark:text-gray-200">Status</Label>
            <Select v-model="formLocal.status" @update:modelValue="updateForm">
                <SelectTrigger class="dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                    <SelectValue placeholder="Selecione o status" class="dark:text-gray-300" />
                </SelectTrigger>
                <SelectContent class="dark:bg-gray-800 dark:border-gray-700">
                    <SelectGroup>
                        <SelectLabel class="dark:text-gray-300">Status</SelectLabel>
                        <SelectItem value="published" class="dark:text-gray-200 dark:hover:bg-gray-700 dark:focus:bg-gray-700">Publicado</SelectItem>
                        <SelectItem value="draft" class="dark:text-gray-200 dark:hover:bg-gray-700 dark:focus:bg-gray-700">Rascunho</SelectItem>
                    </SelectGroup>
                </SelectContent>
            </Select>
        </div>
    </div>
</template>

<script setup>
import { Button } from '../../../../components/ui/button';
import { Input } from '../../../../components/ui/input';
import { Label } from '../../../../components/ui/label';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import { InfoIcon } from 'lucide-vue-next';
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

// Função para gerar um código aleatório formatado para o nome da gôndola
const gerarCodigoGondola = () => {
    const prefixo = 'GND';
    const data = new Date();
    const ano = data.getFullYear().toString().slice(2); // Últimos 2 dígitos do ano
    const mes = (data.getMonth() + 1).toString().padStart(2, '0');
    const random = Math.floor(Math.random() * 10000)
        .toString()
        .padStart(4, '0');

    return `${prefixo}-${ano}${mes}-${random}`;
};

// Inicializar o nome da gôndola se estiver vazio
onMounted(() => {
    if (!formLocal.gondola_name) {
        formLocal.gondola_name = gerarCodigoGondola();
        updateForm();
    }

    // Inicializar flow se ainda não estiver definido
    if (!formLocal.flow) {
        formLocal.flow = 'left_to_right';
    }
});

// Observar mudanças nas props e atualizar o formulário local
watch(
    () => props.formData,
    (newVal) => {
        Object.assign(formLocal, newVal);

        // Se o nome ainda estiver vazio após atualização, gere um novo código
        if (!formLocal.gondola_name) {
            formLocal.gondola_name = gerarCodigoGondola();
            updateForm();
        }
    },
    { deep: true },
);

// Definir o fluxo da gôndola
const setFlow = (flow) => {
    formLocal.flow = flow;
    updateForm();
};

// Função para emitir os dados atualizados ao componente pai
const updateForm = () => {
    emit('update:form', { ...formLocal });
};
</script>
