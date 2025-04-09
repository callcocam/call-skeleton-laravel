<template>
    <div>
        <div class="mb-4 flex items-center">
            <div class="rounded-full bg-gray-100 p-2 dark:bg-gray-700">
                <CheckIcon class="h-5 w-5 dark:text-gray-200" />
            </div>
            <h3 class="ml-2 text-lg font-medium dark:text-gray-100">Revisão Final</h3>
        </div>

        <div class="space-y-6">
            <!-- Informações Básicas -->
            <div class="space-y-2">
                <h4 class="border-b pb-1 font-medium dark:border-gray-700 dark:text-gray-200">Informações Básicas</h4>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <dl class="space-y-1">
                            <div class="flex">
                                <dt class="w-32 font-medium dark:text-gray-300">Nome:</dt>
                                <dd class="dark:text-gray-200">{{ formData.name }}</dd>
                            </div>
                            <div class="flex">
                                <dt class="w-32 font-medium dark:text-gray-300">Localização:</dt>
                                <dd class="dark:text-gray-200">{{ formData.location }}</dd>
                            </div>
                            <div class="flex">
                                <dt class="w-32 font-medium dark:text-gray-300">Lado:</dt>
                                <dd class="dark:text-gray-200">{{ formData.side || 'Não especificado' }}</dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <dl class="space-y-1">
                            <div class="flex">
                                <dt class="w-32 font-medium dark:text-gray-300">Status:</dt>
                                <dd class="dark:text-gray-200">{{ formData.status === 'published' ? 'Publicado' : 'Rascunho' }}</dd>
                            </div>
                            <div class="flex">
                                <dt class="w-32 font-medium dark:text-gray-300">Fluxo:</dt>
                                <dd class="dark:text-gray-200">{{ formData.flow === 'left_to_right' ? 'Esquerda para direita' : 'Direita para esquerda' }}</dd>
                            </div>
                            <div class="flex">
                                <dt class="w-32 font-medium dark:text-gray-300">Fator de Escala:</dt>
                                <dd class="dark:text-gray-200">{{ formData.scale_factor }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Módulos -->
            <div class="space-y-2">
                <h4 class="border-b pb-1 font-medium dark:border-gray-700 dark:text-gray-200">Módulos</h4>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <dl class="space-y-1">
                            <div class="flex">
                                <dt class="w-32 font-medium dark:text-gray-300">Nº de Módulos:</dt>
                                <dd class="dark:text-gray-200">{{ formData.num_modulos }}</dd>
                            </div>
                            <div class="flex">
                                <dt class="w-32 font-medium dark:text-gray-300">Largura:</dt>
                                <dd class="dark:text-gray-200">{{ formData.section_width }}cm</dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <dl class="space-y-1">
                            <div class="flex">
                                <dt class="w-32 font-medium dark:text-gray-300">Código da Seção:</dt>
                                <dd class="dark:text-gray-200">{{ formData.section_code || 'Automático' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Base -->
            <div class="space-y-2">
                <h4 class="border-b pb-1 font-medium dark:border-gray-700 dark:text-gray-200">Base</h4>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <dl class="space-y-1">
                            <div class="flex">
                                <dt class="w-32 font-medium dark:text-gray-300">Altura:</dt>
                                <dd class="dark:text-gray-200">{{ formData.base_height }}cm</dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <dl class="space-y-1">
                            <div class="flex">
                                <dt class="w-32 font-medium dark:text-gray-300">Largura:</dt>
                                <dd class="dark:text-gray-200">{{ formData.base_width }}cm</dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <dl class="space-y-1">
                            <div class="flex">
                                <dt class="w-32 font-medium dark:text-gray-300">Profundidade:</dt>
                                <dd class="dark:text-gray-200">{{ formData.base_depth }}cm</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Cremalheira -->
            <div class="space-y-2">
                <h4 class="border-b pb-1 font-medium dark:border-gray-700 dark:text-gray-200">Cremalheira</h4>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <dl class="space-y-1">
                            <div class="flex">
                                <dt class="w-48 font-medium dark:text-gray-300">Largura da Cremalheira:</dt>
                                <dd class="dark:text-gray-200">{{ formData.cremalheira_width }}cm</dd>
                            </div>
                            <div class="flex">
                                <dt class="w-48 font-medium dark:text-gray-300">Altura do Furo:</dt>
                                <dd class="dark:text-gray-200">{{ formData.hole_height }}cm</dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <dl class="space-y-1">
                            <div class="flex">
                                <dt class="w-48 font-medium dark:text-gray-300">Largura do Furo:</dt>
                                <dd class="dark:text-gray-200">{{ formData.hole_width }}cm</dd>
                            </div>
                            <div class="flex">
                                <dt class="w-48 font-medium dark:text-gray-300">Espaçamento entre Furos:</dt>
                                <dd class="dark:text-gray-200">{{ formData.hole_spacing }}cm</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Prateleiras -->
            <div class="space-y-2">
                <h4 class="border-b pb-1 font-medium dark:border-gray-700 dark:text-gray-200">Prateleiras</h4>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <dl class="space-y-1">
                            <div class="flex">
                                <dt class="w-32 font-medium dark:text-gray-300">Altura Total:</dt>
                                <dd class="dark:text-gray-200">{{ formData.height }}cm</dd>
                            </div>
                            <div class="flex">
                                <dt class="w-32 font-medium dark:text-gray-300">Quantidade:</dt>
                                <dd class="dark:text-gray-200">{{ formData.shelf_qty }}</dd>
                            </div>
                            <div class="flex">
                                <dt class="w-32 font-medium dark:text-gray-300">Altura:</dt>
                                <dd class="dark:text-gray-200">{{ formData.shelf_height }}cm</dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <dl class="space-y-1">
                            <div class="flex">
                                <dt class="w-32 font-medium dark:text-gray-300">Largura:</dt>
                                <dd class="dark:text-gray-200">{{ formData.shelf_width }}cm</dd>
                            </div>
                            <div class="flex">
                                <dt class="w-32 font-medium dark:text-gray-300">Profundidade:</dt>
                                <dd class="dark:text-gray-200">{{ formData.shelf_depth }}cm</dd>
                            </div>
                            <div class="flex">
                                <dt class="w-32 font-medium dark:text-gray-300">Tipo de Produto:</dt>
                                <dd class="dark:text-gray-200">{{ formData.tipo_produto === 'normal' ? 'Normal' : 'Pendurável' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Visualização -->
            <div class="space-y-2">
                <h4 class="border-b pb-1 font-medium dark:border-gray-700 dark:text-gray-200">Visualização</h4>
                <div class="mt-4 flex justify-center rounded-lg border bg-gray-50 p-4 dark:bg-gray-800 dark:border-gray-700">
                    <div class="flex space-x-4">
                        <div v-for="modulo in parseInt(formData.num_modulos)" :key="modulo" class="w-16 border border-gray-300 bg-white dark:border-gray-600 dark:bg-gray-700">
                            <div
                                v-for="i in parseInt(formData.shelf_qty)"
                                :key="i"
                                class="mx-1 h-6 border-t border-gray-300 dark:border-gray-600"
                                :class="{ 'bg-gray-200 dark:bg-gray-600': i === parseInt(formData.shelf_qty) }"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { CheckIcon } from 'lucide-vue-next';

const props = defineProps({
    formData: {
        type: Object,
        required: true,
    },
});
 
</script>
