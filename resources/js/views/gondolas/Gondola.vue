<template>
    <div>
        <!-- Estado de Carregamento -->
        <div v-if="isLoading" class="flex h-screen items-center justify-center p-4 text-center text-gray-400 dark:text-gray-500">
            <!-- TODO: Usar um componente de spinner/loading mais robusto -->
            <p>Carregando Gôndola...</p>
        </div>
        <!-- Conteúdo Principal -->
        <div v-else class="flex h-full w-full flex-col gap-6 overflow-hidden">
            <!-- Barra de Informações/Controles (passa a gondola carregada) -->
            <Info :gondola="gondolaData" v-if="gondolaData" />

            <!-- Mensagem se nenhuma gôndola for encontrada/carregada -->
            <div class="flex h-full flex-grow items-center justify-center p-4 text-center text-gray-400 dark:text-gray-500" v-if="!gondolaData">
                <p>Gôndola não encontrada ou ID inválido.</p>
                 <!-- TODO: Adicionar botão para voltar ou selecionar outra gôndola -->
            </div>

            <!-- Container das Seções (apenas se gondolaData existir) -->
            <div v-else class="flex flex-grow flex-col overflow-auto">
                 <!-- Container com capacidade de mover/zoom -->
                 <!-- <MovableContainer> -->
                      <!-- Componente que renderiza as seções -->
                     <Sections :gondola="gondolaData"  :scale-factor="scaleFactor" />
                 <!-- </MovableContainer> -->
            </div>
        </div>
        <!-- Permite que rotas filhas (como o modal de edição) sejam renderizadas -->
        <router-view :key="route.fullPath" />
    </div>
</template>

<script setup lang="ts">
// Imports de Bibliotecas Externas
import { computed, onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';

// Imports Internos
import { apiService } from '../../services';
import { useEditorStore } from '../../store/editor';
import MovableContainer from '../../components/MovableContainer.vue'; // Container com Pan/Zoom
import Info from './partials/Info.vue'; // Barra de informações/controles
import Sections from './sections/Sections.vue'; // Componente que exibe as seções

// Hooks e Stores
const route = useRoute();
const router = useRouter(); // Pode ser usado para navegação programática se necessário
const editorStore = useEditorStore(); // Store para estado global do editor (escala, grid, etc.)
const scaleFactor = computed(()=>editorStore.scaleFactor)
// Estado Reativo
/** ID da gôndola obtido da rota. */
const gondolaId = ref<string>(route.params.gondolaId as string);
/** Indica se os dados da gôndola estão sendo carregados. */
const isLoading = ref<boolean>(false);
/** Armazena os dados da gôndola carregados da API. */
const gondolaData = ref<Record<string, any> | null>(null); // Usar um tipo/interface mais específico se disponível

// Métodos
/** Busca os dados da gôndola da API usando o gondolaId da rota. */
const fetchGondolaData = async () => {
    if (!gondolaId.value) {
        console.error("ID da Gôndola não encontrado na rota.");
        // Poderia redirecionar ou mostrar erro mais claramente
        gondolaData.value = null;
        return;
    }
    isLoading.value = true;
    try {
        // Chama a API para obter dados da gôndola específica
        const response = await apiService.get(`gondolas/${gondolaId.value}`);
        gondolaData.value = response.data; // Armazena os dados recebidos
        // Opcional: Atualizar o store com a gôndola carregada, se necessário para outros componentes
        // editorStore.setCurrentGondola(response.data);
    } catch (error) {
        console.error("Erro ao buscar dados da gôndola:", error);
        gondolaData.value = null; // Limpa os dados em caso de erro
        // TODO: Mostrar mensagem de erro para o usuário (ex: toast)
    } finally {
        isLoading.value = false; // Garante que o loading termine
    }
};

// Hook de Ciclo de Vida
/** Ao montar o componente, busca os dados da gôndola. */
onMounted(() => {
    fetchGondolaData();
});

// TODO: Adicionar watcher para route.params.gondolaId se o ID puder mudar sem desmontar o componente
// watch(() => route.params.gondolaId, (newId) => { ... fetchGondolaData(); ... });

</script>

<style scoped>
/* Adicionar altura mínima ou flex-grow para garantir que o container ocupe espaço */
.flex-grow {
    flex-grow: 1;
}
</style>
