import { computed, ref } from 'vue';

export function useAnalysisFilters<T extends Record<string, any>>(
    results: () => T[],
    options: {
        searchFields: (keyof T)[];
        defaultSortKey: keyof T;
        defaultSortDirection?: 'asc' | 'desc';
    },
) {
    const searchQuery = ref('');
    const filterByClass = ref<'all' | 'A' | 'B' | 'C'>('all');

    const sortConfig = ref<{
        key: keyof T;
        direction: 'asc' | 'desc';
    }>({
        key: options.defaultSortKey,
        direction: options.defaultSortDirection || 'desc',
    });

    let isSorting = false;

    const classStats = computed(() => {
        const items = results();
        const total = items.length;
        const classA = items.filter((result: any) => result.classificacao === 'A').length;
        const classB = items.filter((result: any) => result.classificacao === 'B').length;
        const classC = items.filter((result: any) => result.classificacao === 'C').length;

        return {
            total,
            classA,
            classB,
            classC,
        };
    });

    const filteredResults = computed(() => {
        let filtered = [...results()];

        if (searchQuery.value) {
            const query = searchQuery.value.toLowerCase();
            filtered = filtered.filter((item) =>
                options.searchFields.some((field) => {
                    const value = item[field];
                    return value && String(value).toLowerCase().includes(query);
                }),
            );
        }

        if (filterByClass.value !== 'all') {
            filtered = filtered.filter(
                (item: any) => item.classificacao === filterByClass.value,
            );
        }

        filtered.sort((first, second) => {
            const sortKey = sortConfig.value.key as keyof T;
            const firstValue = first[sortKey];
            const secondValue = second[sortKey];

            if (typeof firstValue === 'number' && typeof secondValue === 'number') {
                return sortConfig.value.direction === 'asc'
                    ? firstValue - secondValue
                    : secondValue - firstValue;
            }

            if (typeof firstValue === 'string' && typeof secondValue === 'string') {
                return sortConfig.value.direction === 'asc'
                    ? firstValue.localeCompare(secondValue)
                    : secondValue.localeCompare(firstValue);
            }

            return 0;
        });

        return filtered;
    });

    const handleSort = (key: string, validKeys: (keyof T)[]) => {
        if (isSorting || !key) {
            return;
        }

        isSorting = true;

        if (!validKeys.includes(key as keyof T)) {
            isSorting = false;
            return;
        }

        const typedKey = key as keyof T;
        const currentKey = String(sortConfig.value.key);
        const newKey = String(typedKey);

        let newDirection: 'asc' | 'desc' = 'desc';
        if (currentKey === newKey) {
            newDirection = sortConfig.value.direction === 'asc' ? 'desc' : 'asc';
        }

        sortConfig.value = {
            key: typedKey,
            direction: newDirection,
        };

        setTimeout(() => {
            isSorting = false;
        }, 100);
    };

    const getClassBadgeVariant = (classificacao: 'A' | 'B' | 'C') => {
        switch (classificacao) {
            case 'A':
                return 'default';
            case 'B':
                return 'secondary';
            case 'C':
                return 'outline';
            default:
                return 'outline';
        }
    };

    const getClassRowClass = (classificacao: 'A' | 'B' | 'C', hasAlert?: boolean) => {
        if (hasAlert) {
            return 'bg-yellow-100 dark:bg-yellow-900/50';
        }

        switch (classificacao) {
            case 'A':
                return 'bg-blue-100 dark:bg-blue-900/50';
            case 'B':
                return 'bg-yellow-100 dark:bg-yellow-900/50';
            case 'C':
                return '';
            default:
                return '';
        }
    };

    return {
        searchQuery,
        filterByClass,
        sortConfig,
        classStats,
        filteredResults,
        handleSort,
        getClassBadgeVariant,
        getClassRowClass,
    };
}
