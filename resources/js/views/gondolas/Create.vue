<template>
    <Dialog :open="isOpen">
        <DialogContent class="flex max-h-[90vh] w-full max-w-4xl flex-col p-0 dark:border-gray-700 dark:bg-gray-800">
            <!-- Cabeçalho Fixo -->
            <div class="border-b p-4 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <DialogTitle class="text-xl font-semibold dark:text-gray-100">{{ passoTitulos[passoAtual] }}</DialogTitle>
                        <DialogDescription class="dark:text-gray-300">{{ passoDescricoes[passoAtual] }}</DialogDescription>
                    </div>
                </div>

                <!-- Indicador de passos -->
                <div class="mb-2 mt-3 flex items-center">
                    <template v-for="(passo, index) in passoTitulos" :key="index">
                        <div
                            class="flex h-8 w-8 flex-none items-center justify-center rounded-full text-sm font-medium"
                            :class="{
                                'bg-black text-white dark:bg-primary': passoAtual >= index,
                                'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200': passoAtual < index,
                            }"
                        >
                            <CheckIcon v-if="passoAtual > index" class="h-4 w-4" />
                            <span v-else>{{ index + 1 }}</span>
                        </div>
                        <div
                            v-if="index < passoTitulos.length - 1"
                            class="mx-2 h-1 flex-1"
                            :class="{ 'bg-black dark:bg-primary': passoAtual > index, 'bg-gray-300 dark:bg-gray-600': passoAtual <= index }"
                        ></div>
                    </template>
                </div>
            </div>

            <!-- Mensagens de Erro -->
            <div v-if="Object.keys(errors).length > 0" class="border-b border-red-200 bg-red-50 p-4 dark:border-red-900/30 dark:bg-red-900/20">
                <p class="mb-2 font-medium text-red-600 dark:text-red-400">Por favor, corrija os seguintes erros:</p>
                <ul class="list-inside list-disc space-y-1 text-sm text-red-500 dark:text-red-400">
                    <li v-for="(error, key) in errors" :key="key">
                        {{ error }}
                    </li>
                </ul>
            </div>

            <!-- Área de Conteúdo com Rolagem -->
            <div class="flex-1 overflow-y-auto p-4 dark:bg-gray-800">
                <!-- Componentes de cada passo -->
                <StepGondola v-if="passoAtual === 0" :form-data="formData" :errors="errors" @update:form="updateForm" />

                <StepModule v-if="passoAtual === 1" :form-data="formData" :errors="errors" @update:form="updateForm" />

                <StepBase v-if="passoAtual === 2" :form-data="formData" :errors="errors" @update:form="updateForm" />

                <StepCremalheira v-if="passoAtual === 3" :form-data="formData" :errors="errors" @update:form="updateForm" />

                <StepShelves v-if="passoAtual === 4" :form-data="formData" :errors="errors" @update:form="updateForm" />

                <StepReview v-if="passoAtual === 5" :form-data="formData" />
            </div>

            <!-- Rodapé Fixo -->
            <div class="flex justify-between border-t bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                <Button
                    v-if="passoAtual > 0"
                    variant="outline"
                    @click="passoAtual--"
                    class="dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                >
                    <ChevronLeftIcon class="mr-2 h-4 w-4" /> Anterior
                </Button>
                <div v-else>
                    <Button
                        variant="outline"
                        @click="fecharModal"
                        class="dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                        >Cancelar</Button
                    >
                </div>

                <Button v-if="passoAtual < passoTitulos.length - 1" @click="proximoPasso" class="dark:hover:bg-primary-800">
                    Próximo <ChevronRightIcon class="ml-2 h-4 w-4" />
                </Button>
                <Button v-else @click="enviarFormulario" :disabled="enviando" class="dark:hover:bg-primary-800">
                    <SaveIcon v-if="!enviando" class="mr-2 h-4 w-4" />
                    <Loader2Icon v-else class="mr-2 h-4 w-4 animate-spin" />
                    Salvar
                </Button>
            </div>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { CheckIcon, ChevronLeftIcon, ChevronRightIcon, Loader2Icon, SaveIcon } from 'lucide-vue-next';
import { reactive, ref } from 'vue';
import { Button } from './../../components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogTitle } from './../../components/ui/dialog';
import { useToast } from './../../components/ui/toast';

// Importação dos componentes de passos
import { useRoute, useRouter } from 'vue-router';
import { apiService } from '../../services';
import StepBase from './partials/modal/StepBase.vue';
import StepCremalheira from './partials/modal/StepCremalheira.vue';
import StepGondola from './partials/modal/StepGondola.vue';
import StepModule from './partials/modal/StepModule.vue';
import StepReview from './partials/modal/StepReview.vue';
import StepShelves from './partials/modal/StepShelves.vue';

const props = defineProps({
    open: {
        type: Boolean,
        default: true,
    },
});

const route = useRoute();
const router = useRouter();
const planogramId = ref(route.params.id);

const emit = defineEmits(['close', 'gondola-added', 'update:open']);
const { toast } = useToast();

const isOpen = ref(props.open);
const enviando = ref(false);
const passoAtual = ref(0);
const errors = ref({});

// Títulos e descrições para cada passo
const passoTitulos = ['Informações Básicas', 'Módulos', 'Base', 'Cremalheira', 'Prateleiras', 'Revisão'];

const passoDescricoes = [
    'Preencha as informações básicas da gôndola',
    'Configure os módulos da gôndola',
    'Configure as dimensões da base',
    'Configure a cremalheira e os furos',
    'Configure as prateleiras e gancheiras',
    'Revise todas as informações antes de salvar',
];

// Formulário unificado com todos os campos necessários
const formData = reactive({
    // Informações básicas (Passo 1)
    planogram_id: planogramId.value,
    gondola_name: '', // Será preenchido com código gerado automaticamente
    location: 'Centro',
    side: 'A',
    flow: 'left_to_right',
    scale_factor: 3,
    status: 'published',

    // Módulos (Passo 2)
    num_modulos: 4,
    width: 130,
    height: 180,
    section_code: '',

    // Base (Passo 3)
    base_height: 17,
    base_width: 130,
    base_depth: 40,

    // Cremalheira (Passo 4)
    cremalheira_width: 4,
    hole_height: 3,
    hole_width: 2,
    hole_spacing: 2,

    // Prateleiras (Passo 5)
    shelf_width: 4,
    shelf_height: 4,
    shelf_depth: 40,
    num_shelves: 4,
    product_type: 'normal',
});

// Função para atualizar dados do formulário
const updateForm = (newData) => {
    Object.assign(formData, newData);
};

// Função para fechar o modal
const fecharModal = () => {
    router.push({ name: 'plannerate.view', params: { id: planogramId.value } });
};

// Função para avançar para o próximo passo com validação
const proximoPasso = () => {
    passoAtual.value++;
};

// Função para enviar o formulário usando axios
const enviarFormulario = async () => {
    enviando.value = true;
    errors.value = {};

    // Preparar os dados para envio em formato compatível com o backend
    const dadosEnvio = {
        // Dados do planograma
        planogram_id: formData.planogram_id,

        // Dados da gôndola (tabela gondolas)
        name: formData.gondola_name,
        location: formData.location,
        side: formData.side,
        flow: formData.flow,
        scale_factor: formData.scale_factor,
        status: formData.status,

        // Dados da seção (tabela sections)
        section: {
            name: formData.gondola_name + ' - Seção',
            width: formData.width,
            height: formData.height,
            base_height: formData.base_height,
            base_depth: formData.base_depth,
            base_width: formData.base_width,
            cremalheira_width: formData.cremalheira_width,
            hole_height: formData.hole_height,
            hole_width: formData.hole_width,
            hole_spacing: formData.hole_spacing,
            shelf_width: formData.shelf_width,
            shelf_height: formData.shelf_height,
            shelf_depth: formData.shelf_depth,

            // Dados adicionais para criação das prateleiras
            num_shelves: formData.num_shelves,
            num_modulos: formData.num_modulos,
            product_type: formData.product_type,
            settings: {
                product_type: formData.product_type,
            },
        },
    };

    try {
        // Usando PUT com o axios para o mesmo endpoint
        const response = await apiService.post('gondolas', dadosEnvio);

        // Se chegou aqui, deu certo
        toast({
            title: 'Sucesso',
            description: 'Gôndola criada com sucesso!',
            variant: 'default',
        });
 
        fecharModal();
    } catch (error) {
        console.error('Erro ao salvar gôndola:', error);

        // Tratar erros de validação (422)
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors || {};

            // Exibir toast de erro
            toast({
                title: 'Erro de validação',
                description: 'Por favor, corrija os campos destacados.',
                variant: 'destructive',
            });
        } else {
            // Tratar outros erros
            toast({
                title: 'Erro',
                description: error.response?.data?.message || 'Ocorreu um erro ao salvar a gôndola.',
                variant: 'destructive',
            });
        }
    } finally {
        enviando.value = false;
    }
};
</script>

<style scoped>
/* Estilos para a barra de rolagem no modo escuro */
@media (prefers-color-scheme: dark) {
    .overflow-y-auto {
        scrollbar-width: thin;
        scrollbar-color: #4b5563 #1f2937;
    }

    .overflow-y-auto::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .overflow-y-auto::-webkit-scrollbar-track {
        background: #1f2937;
    }

    .overflow-y-auto::-webkit-scrollbar-thumb {
        background-color: #4b5563;
        border-radius: 4px;
    }
}
</style>
