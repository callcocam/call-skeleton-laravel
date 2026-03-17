<script setup lang="ts">
import { ref, watch, computed, onMounted } from 'vue'
import axios from 'axios'
import { Label } from '@/components/ui/label'
import { Select, SelectTrigger, SelectContent, SelectItem } from '@/components/ui/select'

// Adicionando loading spinner component
const LoadingSpinner = {
    template: `
        <div class="flex items-center justify-center space-x-2">
            <div class="w-4 h-4 border-2 border-t-2 border-gray-200 rounded-full animate-spin dark:border-gray-600 border-t-primary"></div>
            <span class="text-sm text-gray-500 dark:text-gray-400">Carregando...</span>
        </div>
    `
}

const props = defineProps<{
    id: string;
    field: {
        name: string;
        label?: string;
        description?: string;
        disabled?: boolean;
        required?: boolean;
        apiUrl: string;
        valueKey?: string;
        labelKey?: string;
        apiConfig?: {
            parentIdParam?: string;
            queryParams?: Record<string, any>;
            [key: string]: any;
        };
        [key: string]: any;
    };
    inputProps?: Record<string, any>;
}>() 
const levels = [
    { key: 'segmento_varejista', label: 'Segmento varejista' },
    { key: 'departamento', label: 'Departamento' },
    { key: 'subdepartamento', label: 'Subdepartamento' },
    { key: 'categoria', label: 'Categoria' },
    { key: 'subcategoria', label: 'Subcategoria' },
    { key: 'segmento', label: 'Segmento' },
    { key: 'subsegmento', label: 'Subsegmento' }
]

const model = defineModel<Record<string, any>>({
    default: () => ({
        segmento_varejista: null,
        departamento: null,
        subdepartamento: null,
        categoria: null,
        subcategoria: null,
        segmento: null,
        subsegmento: null,
    })
})
const valueKey = computed(() => props.field.valueKey || 'id')
const labelKey = computed(() => props.field.labelKey || 'name')
const parentIdParam = computed(() => props.field.apiConfig?.parentIdParam || 'parent_id')
const queryParams = computed(() => props.field.apiConfig?.queryParams || {})

const options = ref<Record<string, any[]>>({})
const loading = ref<Record<string, boolean>>({})
const errors = ref<Record<string, string>>({})

// Inicialize options para todos os níveis com arrays vazios
onMounted(() => {
    levels.forEach(level => {
        options.value[level.key] = []
        loading.value[level.key] = false
        errors.value[level.key] = ''
    })
 
    const loadInitialData = async () => {
        for (let i = 0; i < levels.length; i++) {
            const level = levels[i]
            const hasValue = safeModel.value[level.key] !== null && safeModel.value[level.key] !== undefined

            // Carrega opções para o nível atual se for o primeiro nível,
            // ou se o pai tiver um valor.
            if (i === 0 || (safeModel.value[levels[i - 1].key])) {
                await loadOptions(i)
            }

            // Se o nível atual não tiver valor, não há necessidade de carregar os filhos.
            if (!hasValue) {
                break
            }
        }
    }

    setTimeout(() => {
        loadInitialData()
    }, 1000) // Usar setTimeout para garantir que o DOM esteja pronto
})

// Proteção extra para garantir que model nunca seja null ou sem todas as chaves
watch(model, (val) => {
    // Verificação robusta para evitar erro do operador 'in'
    if (val === null || val === undefined || typeof val !== 'object') {
        model.value = {
            segmento_varejista: null,
            departamento: null,
            subdepartamento: null,
            categoria: null,
            subcategoria: null,
            segmento: null,
            subsegmento: null,
        }
    } else {
        // Garante que todas as chaves existam - com verificação de segurança
        levels.forEach(level => {
            // Verifica se val é um objeto válido antes de usar o operador 'in'
            if (val && typeof val === 'object' && !(level.key in val)) {
                val[level.key] = null
            }
        })
    }
}, { immediate: true })

// Computed seguro para uso no template
const safeModel = computed(() => model.value || {}) 

function loadOptions(levelIdx: number) {
    return new Promise<void>((resolve) => {
        if (levelIdx < 0 || levelIdx >= levels.length) {
            resolve()
            return
        }

        const level = levels[levelIdx]

        // Marca como carregando
        loading.value[level.key] = true
        errors.value[level.key] = ''

        // Determina o ID do pai (se aplicável)
        let parentId = null

        if (levelIdx > 0) {
            const parentKey = levels[levelIdx - 1].key
            parentId = safeModel.value[parentKey] || null

            // Se não houver um pai selecionado e não for o primeiro nível, limpa as opções e sai
            if (!parentId) {
                options.value[level.key] = []
                loading.value[level.key] = false
                resolve()
                return
            }
        }

        // Constrói os parâmetros da requisição
        const params = {
            ...(levelIdx > 0 ? { [parentIdParam.value]: parentId } : { [parentIdParam.value]: null }),
            ...queryParams.value
        }
        // Faz a requisição
        axios.get(props.inputProps?.apiUrl || props.field.apiUrl, { params })
            .then(res => {
                if (Array.isArray(res.data)) {
                    options.value[level.key] = res.data
                    if (res.data.length === 0) {
                        errors.value[level.key] = 'Nenhuma opção disponível'
                    }
                } else {
                    options.value[level.key] = []
                    errors.value[level.key] = 'Formato de resposta inválido'
                }
            })
            .catch(error => {
                console.error(`Erro ao carregar opções para ${level.label}:`, error)
                options.value[level.key] = []
                errors.value[level.key] = 'Erro ao carregar opções'
            })
            .finally(() => {
                loading.value[level.key] = false
                resolve()
            })
    })
}

// Observa mudanças em cada nível e atualiza os níveis dependentes
levels.forEach((level, index) => {
    if (index < levels.length - 1) { // Não precisamos observar o último nível
        watch(() => safeModel.value[level.key], (newValue, oldValue) => { 
            // Só executa se o valor realmente mudou e não é o carregamento inicial (oldValue não é undefined)
            if (newValue !== oldValue && oldValue !== undefined) {
                // Limpa valores de todos os níveis abaixo
                for (let i = index + 1; i < levels.length; i++) {
                    const childKey = levels[i].key
                    if (model.value) {
                        model.value[childKey] = null
                    }
                    // Limpa também as opções dos níveis filhos para evitar exibir dados antigos
                    options.value[childKey] = []
                }
                // Recarrega as opções para o próximo nível, se o novo valor não for nulo
                if (newValue) {
                    loadOptions(index + 1)
                }
            }
        })
    }
})

const breadcrumbPath = computed(() => {
    return levels
        .map(level => {
            const selectedValue = safeModel.value[level.key]
            if (!selectedValue) { return null }

            const levelOptions = options.value[level.key] || []
            const selectedOption = levelOptions.find(opt => opt[valueKey.value] == selectedValue)

            return selectedOption ? selectedOption[labelKey.value] : null
        })
        .filter(Boolean) // Remove os nulos
        .join(' / ')
})

const getChildrenLabel = (key: string) => {
    return loading.value[key]
        ? 'Carregando...'
        : (options.value[key]?.find(opt => opt[valueKey.value] ===
            safeModel.value[key])?.[labelKey.value] || 'Selecione...')
}

// Registro global do componente para uso no template
import { defineComponent } from 'vue' 

const loadingSpinnerComponent = defineComponent(LoadingSpinner)
</script>

<template>
    <fieldset class="mercadologico-selector border border-input rounded-md px-4 pb-2 dark:border-gray-700 relative">
        <!-- Loading Overlay -->
        <div v-if="Object.values(loading).some(isLoading => isLoading)" 
            class="absolute inset-0 bg-white/50 dark:bg-black/50 z-10 flex items-center justify-center rounded-md">
            <loadingSpinnerComponent />
        </div>
        
        <legend class="text-lg font-semibold text-gray-800 dark:text-gray-200">Classificação Mercadológica</legend>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Selecione a hierarquia de classificação do produto</p>
        <div v-if="breadcrumbPath"
            class="text-sm p-2 mb-4 bg-gray-50 dark:bg-white/10 rounded-lg border border-dashed border-gray-300 dark:border-gray-700">
            <span class="font-medium text-gray-600 dark:text-gray-400">Caminho selecionado:</span>
            <span class="ml-2 font-semibold tracking-wider text-gray-800 dark:text-gray-200">{{ breadcrumbPath }}</span>
        </div>
        <!-- // Segmento varejista
        // Departamento
        // Subdepartamento
        // Categoria
        // Subcategoria (não obrigatório)
        // Segmento (não obrigatório)
        // Subsegmento (não obrigatório) -->
        <div :id="props.id" class="space-y-4 my-2">
            <div class="grid grid-cols-4 gap-4">
                <!-- Segmento varejista -->
                <div class="flex flex-col gap-2 col-span-4">
                    <Label :for="`${props.id}-segmento_varejista`" class="text-sm font-medium text-gray-700 dark:text-gray-300">Segmento varejista </Label>
                    <Select v-model="safeModel.segmento_varejista"
                        :disabled="props.field.disabled || loading['segmento_varejista']">
                        <SelectTrigger class="border border-gray-300 dark:border-gray-600 rounded px-2 py-1 w-full dark:bg-input/30">
                            <SelectValue :placeholder="getChildrenLabel('segmento_varejista')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="null">Selecione...</SelectItem>
                            <SelectItem v-for="option in options['segmento_varejista']" :key="option[valueKey]"
                                :value="option[valueKey]">
                                {{ option[labelKey] }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <span v-if="loading['segmento_varejista']"
                        class="text-xs text-gray-500 dark:text-gray-400 ml-2">Carregando...</span>
                    <span v-if="errors['segmento_varejista']" class="text-xs text-red-500 dark:text-red-400 ml-2">{{
                        errors['segmento_varejista'] }}</span>
                </div>
                <!-- Departamento -->
                <div class="flex flex-col gap-2 col-span-2">
                    <Label :for="`${props.id}-departamento`" class="text-sm font-medium text-gray-700 dark:text-gray-300">Departamento</Label>
                    <Select v-model="safeModel.departamento"
                        :disabled="props.field.disabled || loading['departamento'] || !safeModel.segmento_varejista">
                        <SelectTrigger class="border border-gray-300 dark:border-gray-600 rounded px-2 py-1 w-full dark:bg-input/30">
                            <SelectValue :placeholder="getChildrenLabel('departamento')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="null">Selecione...</SelectItem>
                            <SelectItem v-for="option in options['departamento']" :key="option[valueKey]"
                                :value="option[valueKey]">
                                {{ option[labelKey] }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <span v-if="loading['departamento']"
                        class="text-xs text-gray-500 dark:text-gray-400 ml-2">Carregando...</span>
                    <span v-if="errors['departamento']" class="text-xs text-red-500 dark:text-red-400 ml-2">{{
                        errors['departamento'] }}</span>
                </div>
                <!-- Subdepartamento -->
                <div class="flex flex-col gap-2 col-span-2">
                    <Label :for="`${props.id}-subdepartamento`" class="text-sm font-medium text-gray-700 dark:text-gray-300">Subdepartamento</Label>
                    <Select v-model="safeModel.subdepartamento"
                        :disabled="props.field.disabled || loading['subdepartamento'] || !safeModel.departamento">
                        <SelectTrigger class="border border-gray-300 dark:border-gray-600 rounded px-2 py-1 w-full dark:bg-input/30">
                            <SelectValue :placeholder="getChildrenLabel('subdepartamento')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="null">Selecione...</SelectItem>
                            <SelectItem v-for="option in options['subdepartamento']" :key="option[valueKey]"
                                :value="option[valueKey]">
                                {{ option[labelKey] }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <span v-if="loading['subdepartamento']"
                        class="text-xs text-gray-500 dark:text-gray-400 ml-2">Carregando...</span>
                    <span v-if="errors['subdepartamento']" class="text-xs text-red-500 dark:text-red-400 ml-2">{{
                        errors['subdepartamento'] }}</span>
                </div>
                <!-- Categoria -->
                <div class="flex flex-col gap-2 col-span-2">
                    <Label :for="`${props.id}-categoria`" class="text-sm font-medium text-gray-700 dark:text-gray-300">Categoria</Label>
                    <Select v-model="safeModel.categoria"
                        :disabled="props.field.disabled || loading['categoria'] || !safeModel.subdepartamento">
                        <SelectTrigger class="border border-gray-300 dark:border-gray-600 rounded px-2 py-1 w-full dark:bg-input/30">
                            <SelectValue :placeholder="getChildrenLabel('categoria')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="null">Selecione...</SelectItem>
                            <SelectItem v-for="option in options['categoria']" :key="option[valueKey]"
                                :value="option[valueKey]">
                                {{ option[labelKey] }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <span v-if="loading['categoria']"
                        class="text-xs text-gray-500 dark:text-gray-400 ml-2">Carregando...</span>
                    <span v-if="errors['categoria']" class="text-xs text-red-500 dark:text-red-400 ml-2">{{
                        errors['categoria'] }}</span>
                </div>
                <!-- Subcategoria -->
                <div class="flex flex-col gap-2 col-span-2">
                    <Label :for="`${props.id}-subcategoria`" class="text-sm font-medium text-gray-700 dark:text-gray-300">Subcategoria</Label>
                    <Select v-model="safeModel.subcategoria"
                        :disabled="props.field.disabled || loading['subcategoria'] || !safeModel.categoria">
                        <SelectTrigger class="border border-gray-300 dark:border-gray-600 rounded px-2 py-1 w-full dark:bg-input/30">
                            <SelectValue :placeholder="getChildrenLabel('subcategoria')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="null">Selecione...</SelectItem>
                            <SelectItem v-for="option in options['subcategoria']" :key="option[valueKey]"
                                :value="option[valueKey]">
                                {{ option[labelKey] }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <span v-if="loading['subcategoria']"
                        class="text-xs text-gray-500 dark:text-gray-400 ml-2">Carregando...</span>
                    <span v-if="errors['subcategoria']" class="text-xs text-red-500 dark:text-red-400 ml-2">{{
                        errors['subcategoria'] }}</span>
                </div>
                <!-- Segmento -->
                <div class="flex flex-col gap-2 col-span-2">
                    <Label :for="`${props.id}-segmento`" class="text-sm font-medium text-gray-700 dark:text-gray-300">Segmento</Label>
                    <Select v-model="safeModel.segmento"
                        :disabled="props.field.disabled || loading['segmento'] || !safeModel.subcategoria">
                        <SelectTrigger class="border border-gray-300 dark:border-gray-600 rounded px-2 py-1 w-full dark:bg-input/30">
                            <SelectValue :placeholder="getChildrenLabel('segmento')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="null">Selecione...</SelectItem>
                            <SelectItem v-for="option in options['segmento']" :key="option[valueKey]"
                                :value="option[valueKey]">
                                {{ option[labelKey] }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <span v-if="loading['segmento']"
                        class="text-xs text-gray-500 dark:text-gray-400 ml-2">Carregando...</span>
                    <span v-if="errors['segmento']" class="text-xs text-red-500 dark:text-red-400 ml-2">{{
                        errors['segmento'] }}</span>
                </div>
                <!-- Subsegmento -->
                <div class="flex flex-col gap-2 col-span-2">
                    <Label :for="`${props.id}-subsegmento`" class="text-sm font-medium text-gray-700 dark:text-gray-300">Subsegmento</Label>
                    <Select v-model="safeModel.subsegmento"
                        :disabled="props.field.disabled || loading['subsegmento'] || !safeModel.segmento">
                        <SelectTrigger class="border border-gray-300 dark:border-gray-600 rounded px-2 py-1 w-full dark:bg-input/30">
                            <SelectValue :placeholder="getChildrenLabel('subsegmento')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="null">Selecione...</SelectItem>
                            <SelectItem v-for="option in options['subsegmento']" :key="option[valueKey]"
                                :value="option[valueKey]">
                                {{ option[labelKey] }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <span v-if="loading['subsegmento']"
                        class="text-xs text-gray-500 dark:text-gray-400 ml-2">Carregando...</span>
                    <span v-if="errors['subsegmento']" class="text-xs text-red-500 dark:text-red-400 ml-2">{{
                        errors['subsegmento'] }}</span>
                </div>
            </div>
        </div>
    </fieldset>
</template>