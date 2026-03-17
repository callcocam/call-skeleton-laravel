import type { Shelf } from '@planogram/types/planogram';

export interface ShelfFieldsCamel {
    shelfHeight?: number;
    height?: number;
    shelfWidth?: number;
    width?: number;
    shelfDepth?: number;
    depth?: number;
    productType?: 'normal' | 'hook';
    numShelves?: number;
}

export interface ShelfFieldsSnake {
    shelf_height?: number;
    height?: number;
    shelf_width?: number;
    width?: number;
    shelf_depth?: number;
    depth?: number;
    product_type?: 'normal' | 'hook';
    num_shelves?: number;
}

export const DEFAULT_SHELF_FIELDS: Required<ShelfFieldsCamel> = {
    shelfHeight: 4,
    height: 4,
    shelfWidth: 100,
    width: 100,
    shelfDepth: 40,
    depth: 40,
    productType: 'normal',
    numShelves: 4,
};

export function toSnakeCase(fields: ShelfFieldsCamel): ShelfFieldsSnake {
    return {
        shelf_height: fields.shelfHeight ?? fields.height,
        height: fields.shelfHeight ?? fields.height,
        shelf_width: fields.shelfWidth ?? fields.width,
        width: fields.shelfWidth ?? fields.width,
        shelf_depth: fields.shelfDepth ?? fields.depth,
        depth: fields.shelfDepth ?? fields.depth,
        product_type: fields.productType,
        num_shelves: fields.numShelves,
    };
}

export function toCamelCase(
    fields: ShelfFieldsSnake | Partial<Shelf>,
): ShelfFieldsCamel {
    const shelf = fields as Partial<Shelf>;
    const snake = fields as ShelfFieldsSnake;

    const shelfHeight =
        shelf.shelf_height ?? snake.shelf_height ?? snake.height;
    const shelfWidth = shelf.shelf_width ?? snake.shelf_width ?? snake.width;
    const shelfDepth = shelf.shelf_depth ?? snake.shelf_depth ?? snake.depth;

    return {
        shelfHeight: shelfHeight ?? 0,
        height: shelfHeight ?? 0,
        shelfWidth: shelfWidth ?? 0,
        width: shelfWidth ?? 0,
        shelfDepth: shelfDepth ?? 0,
        depth: shelfDepth ?? 0,
        productType: (shelf.product_type ?? snake.product_type) as
            | 'normal'
            | 'hook'
            | undefined,
        numShelves: snake.num_shelves,
    };
}

export function getInitialShelfFields(
    existingShelf?: Partial<Shelf> | null,
    lastShelf?: Partial<Shelf> | null,
): ShelfFieldsCamel {
    if (existingShelf) {
        return {
            ...DEFAULT_SHELF_FIELDS,
            ...toCamelCase(existingShelf),
        };
    }

    if (lastShelf) {
        return {
            ...DEFAULT_SHELF_FIELDS,
            ...toCamelCase(lastShelf),
        };
    }

    return {
        ...DEFAULT_SHELF_FIELDS,
    };
}

export function validateShelfFields(
    fields: Partial<ShelfFieldsCamel>,
): boolean {
    const height = fields.shelfHeight ?? fields.height ?? 0;
    const width = fields.shelfWidth ?? fields.width ?? 0;
    const depth = fields.shelfDepth ?? fields.depth ?? 0;
    const numShelves = fields.numShelves ?? 0;

    return (
        height >= 1 &&
        width >= 1 &&
        depth >= 1 &&
        numShelves >= 0 &&
        (fields.productType === 'normal' || fields.productType === 'hook')
    );
}

export function calculateShelfSpacing(
    usableHeight: number,
    shelfHeight: number,
    numShelves: number,
): number {
    if (numShelves === 0) {
        return 0;
    }

    if (numShelves === 1) {
        return Math.max(0, usableHeight);
    }

    const totalShelfHeight = numShelves * shelfHeight;
    const remainingHeight = usableHeight - totalShelfHeight;

    if (remainingHeight <= 0) {
        return 0;
    }

    return remainingHeight / (numShelves - 1);
}

export function calculateTotalDisplayArea(
    shelfWidth: number,
    shelfDepth: number,
    numShelves: number,
    numModules: number = 1,
): number {
    const areaPerShelf = shelfWidth * shelfDepth;

    return numModules * numShelves * areaPerShelf;
}

export function useShelfFields(
    initialData?: Partial<Shelf> | null,
    lastShelf?: Partial<Shelf> | null,
) {
    const defaultFields = getInitialShelfFields(initialData, lastShelf);

    return {
        defaults: DEFAULT_SHELF_FIELDS,
        initialFields: defaultFields,
        toSnakeCase,
        toCamelCase,
        validate: validateShelfFields,
        calculateShelfSpacing,
        calculateTotalDisplayArea,
    };
}
