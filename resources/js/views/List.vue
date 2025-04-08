<template>
    <div class="container mx-auto max-w-7xl p-4">
        <!-- Header com título e botão de adicionar -->
        <div class="mb-6 flex flex-col items-center justify-between gap-4 md:flex-row">
            <h1 class="text-3xl font-bold text-primary">Planogramas</h1>
            <Button variant="default" class="w-full md:w-auto" @click="$router.push({ name: 'plannerate.create' })">
                <Plus class="mr-2 h-4 w-4" />
                Adicionar Novo
            </Button>
        </div>

        <Card class="shadow-md">
            <CardHeader class="bg-muted/30">
                <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                    <div>
                        <CardTitle>Lista de Planogramas</CardTitle>
                        <CardDescription>Gerencie seus Planogramas aqui</CardDescription>
                    </div>
                    <Button variant="outline" size="sm" class="flex items-center gap-2" @click="showFilters = !showFilters">
                        <FilterIcon class="h-4 w-4" />
                        {{ showFilters ? 'Ocultar Filtros' : 'Mostrar Filtros' }}
                    </Button>
                </div>
            </CardHeader>

            <!-- Filtros colapsáveis -->
            <div v-if="showFilters" class="border-b border-border/40 bg-muted/10 p-4">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div class="space-y-2">
                        <Label for="search">Pesquisar</Label>
                        <Input id="search" placeholder="Nome ou ID" v-model="filters.search" />
                    </div>
                    <div class="space-y-2">
                        <Label for="value-range">Faixa de Valor</Label>
                        <div class="flex items-center space-x-2">
                            <Input id="value-min" placeholder="Min" type="number" v-model="filters.minValue" class="w-full" />
                            <span>-</span>
                            <Input id="value-max" placeholder="Max" type="number" v-model="filters.maxValue" class="w-full" />
                        </div>
                    </div>
                    <div class="flex items-end space-x-2">
                        <Button variant="secondary" class="flex-1" @click="applyFilters">
                            <SearchIcon class="mr-2 h-4 w-4" />
                            Filtrar
                        </Button>
                        <Button variant="ghost" @click="resetFilters">
                            <RefreshCw class="h-4 w-4" />
                        </Button>
                    </div>
                </div>
            </div>

            <!-- Indicadores de filtros ativos -->
            <div v-if="hasActiveFilters" class="flex flex-wrap gap-2 border-b border-border/40 bg-muted/5 px-4 py-2">
                <Badge variant="outline" class="flex items-center gap-1" v-if="filters.search">
                    Pesquisa: {{ filters.search }}
                    <XIcon class="h-3 w-3 cursor-pointer" @click="filters.search = ''" />
                </Badge>
                <Badge variant="outline" class="flex items-center gap-1" v-if="filters.minValue || filters.maxValue">
                    Valor: {{ filters.minValue || '0' }} - {{ filters.maxValue || 'Máx' }}
                    <XIcon class="h-3 w-3 cursor-pointer" @click="resetValueFilter" />
                </Badge>
            </div>

            <CardContent class="p-0">
                <div class="relative overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow class="hover:bg-muted/5">
                                <TableHead class="w-20">ID</TableHead>
                                <TableHead class="cursor-pointer" @click="sortBy('name')">
                                    Nome
                                    <ChevronUp v-if="sortConfig.key === 'name' && sortConfig.direction === 'asc'" class="inline h-4 w-4" />
                                    <ChevronDown v-if="sortConfig.key === 'name' && sortConfig.direction === 'desc'" class="inline h-4 w-4" />
                                </TableHead>
                                <TableHead class="cursor-pointer" @click="sortBy('value')">
                                    Valor
                                    <ChevronUp v-if="sortConfig.key === 'value' && sortConfig.direction === 'asc'" class="inline h-4 w-4" />
                                    <ChevronDown v-if="sortConfig.key === 'value' && sortConfig.direction === 'desc'" class="inline h-4 w-4" />
                                </TableHead>
                                <TableHead class="w-24 text-right">Ações</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="item in filteredItems" :key="item.id" class="transition-colors hover:bg-muted/10">
                                <TableCell class="font-medium">{{ item.id }}</TableCell>
                                <TableCell>{{ item.name }}</TableCell>
                                <TableCell>{{ formatCurrency(item.value) }}</TableCell>
                                <TableCell>
                                    <div class="flex justify-end gap-1">
                                        <Button variant="ghost" size="icon" class="h-8 w-8 text-muted-foreground hover:text-foreground">
                                            <EyeIcon class="h-4 w-4" />
                                        </Button>
                                        <Button variant="ghost" size="icon" class="h-8 w-8 text-muted-foreground hover:text-foreground">
                                            <Pencil class="h-4 w-4" />
                                        </Button>
                                        <Button variant="ghost" size="icon" class="h-8 w-8 text-muted-foreground hover:text-destructive">
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>

                            <TableRow v-if="filteredItems.length === 0">
                                <TableCell colspan="4" class="h-24 text-center">
                                    <div class="flex flex-col items-center justify-center text-muted-foreground">
                                        <FileX class="mb-2 h-8 w-8" />
                                        <p>Nenhum planograma encontrado</p>
                                        <Button variant="link" @click="resetFilters" v-if="hasActiveFilters"> Limpar filtros </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </CardContent>

            <div class="flex items-center justify-between border-t border-border/40 p-4">
                <div class="text-sm text-muted-foreground">
                    Mostrando <span class="font-medium">{{ filteredItems.length }}</span> de
                    <span class="font-medium">{{ items.length }}</span> planogramas
                </div>
                <div class="flex items-center space-x-2">
                    <Button variant="outline" size="sm" :disabled="currentPage === 1">
                        <ChevronLeft class="h-4 w-4" />
                    </Button>
                    <Button variant="outline" size="sm" :disabled="currentPage >= totalPages">
                        <ChevronRight class="h-4 w-4" />
                    </Button>
                </div>
            </div>
        </Card>
    </div>
</template>

<script setup lang="ts">
import {
    ChevronDown,
    ChevronLeft,
    ChevronRight,
    ChevronUp,
    Eye as EyeIcon,
    FileX,
    Filter as FilterIcon,
    Pencil,
    Plus,
    RefreshCw,
    Search as SearchIcon,
    Trash2,
    X as XIcon,
} from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';
import { Badge } from '../components/ui/badge';
import { Button } from '../components/ui/button';
import { Input } from '../components/ui/input';
import { Label } from '../components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '../components/ui/table';
import { apiService } from '../services';

// Estado e referências
const items = ref([] as Array<{ id: number; name: string; value: number }>);
const showFilters = ref(false);
const currentPage = ref(1);
const itemsPerPage = ref(10);
const isLoading = ref(false);

// Filtros
const filters = ref({
    search: '',
    minValue: undefined as number | undefined,
    maxValue: undefined as number | undefined,
});

// Ordenação
const sortConfig = ref({
    key: 'id',
    direction: 'asc',
});

// Verificar se há filtros ativos
const hasActiveFilters = computed(() => {
    return !!filters.value.search || filters.value.minValue !== undefined || filters.value.maxValue !== undefined;
});

// Itens filtrados
const filteredItems = computed(() => {
    let result = [...items.value];

    // Aplicar filtros
    if (filters.value.search) {
        const searchLower = filters.value.search.toLowerCase();
        result = result.filter((item) => item.id.toString().includes(filters.value.search) || item.name.toLowerCase().includes(searchLower));
    }

    if (filters.value.minValue !== undefined) {
        result = result.filter((item) => item.value >= (filters.value.minValue || 0));
    }

    if (filters.value.maxValue !== undefined) {
        result = result.filter((item) => item.value <= (filters.value.maxValue as number));
    }

    // Aplicar ordenação
    result.sort((a, b) => {
        const factor = sortConfig.value.direction === 'asc' ? 1 : -1;
        const key = sortConfig.value.key as keyof typeof a;

        if (typeof a[key] === 'string') {
            return factor * (a[key] as string).localeCompare(b[key] as string);
        } else {
            return factor * ((a[key] as number) - (b[key] as number));
        }
    });

    return result;
});

// Total de páginas para paginação
const totalPages = computed(() => {
    return Math.ceil(filteredItems.value.length / itemsPerPage.value);
});

// Formatar valor como moeda
const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(value);
};

// Funções
const getData = async () => {
    isLoading.value = true;
    try {
        const response = await apiService.get('/plannerate');
        console.log(response.data);
        const { data } = response.data;
        items.value = data || [];
    } catch (error) {
        console.error('Erro ao carregar planogramas:', error);
        // Implementar notificação de erro aqui
    } finally {
        isLoading.value = false;
    }
};

const sortBy = (key: string) => {
    if (sortConfig.value.key === key) {
        sortConfig.value.direction = sortConfig.value.direction === 'asc' ? 'desc' : 'asc';
    } else {
        sortConfig.value.key = key;
        sortConfig.value.direction = 'asc';
    }
};

const applyFilters = () => {
    currentPage.value = 1;
    // Aplicar filtros (já feito pelo computed)
};

const resetFilters = () => {
    filters.value = {
        search: '',
        minValue: undefined,
        maxValue: undefined,
    };
    currentPage.value = 1;
};

const resetValueFilter = () => {
    filters.value.minValue = undefined;
    filters.value.maxValue = undefined;
};

// Inicialização
onMounted(async () => {
    await getData();
});
</script>
