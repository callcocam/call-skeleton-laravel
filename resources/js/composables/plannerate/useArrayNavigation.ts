import type { Ref } from 'vue';
import { unref } from 'vue';

export function useArrayNavigation<T>(items: T[] | Ref<T[]>) {
    const getPrevious = (item: T): T | undefined => {
        const itemsArray = unref(items);
        const index = itemsArray.indexOf(item);

        if (index <= 0) {
            return undefined;
        }

        return itemsArray[index - 1];
    };

    const getNext = (item: T): T | undefined => {
        const itemsArray = unref(items);
        const index = itemsArray.indexOf(item);

        if (index === -1 || index >= itemsArray.length - 1) {
            return undefined;
        }

        return itemsArray[index + 1];
    };

    const getFirst = (): T | undefined => {
        const itemsArray = unref(items);

        if (!itemsArray.length) {
            return undefined;
        }

        return itemsArray[0];
    };

    const getLast = (): T | undefined => {
        const itemsArray = unref(items);

        if (!itemsArray.length) {
            return undefined;
        }

        return itemsArray[itemsArray.length - 1];
    };

    const isLast = (item: T): boolean => {
        const itemsArray = unref(items);

        return itemsArray.indexOf(item) === itemsArray.length - 1;
    };

    const isFirst = (item: T): boolean => {
        const itemsArray = unref(items);

        return itemsArray.indexOf(item) === 0;
    };

    const getIndex = (item: T): number => {
        const itemsArray = unref(items);

        return itemsArray.indexOf(item);
    };

    return {
        getPrevious,
        getNext,
        getFirst,
        getLast,
        isLast,
        isFirst,
        getIndex,
    };
}
