import type { Gondola } from '@planogram/types/planogram';

export interface GondolaFieldsCamel {
    gondolaName?: string;
    name?: string;
    location?: string;
    side?: string;
    scaleFactor?: number;
    flow?: 'left_to_right' | 'right_to_left';
    status?: 'draft' | 'published';
    alignment?: 'left' | 'right' | 'center' | 'justify';
    numModules?: number;
}

export interface GondolaFieldsSnake {
    name?: string;
    location?: string;
    side?: string;
    scale_factor?: number;
    flow?: 'left_to_right' | 'right_to_left';
    status?: 'draft' | 'published';
    alignment?: 'left' | 'right' | 'center' | 'justify';
    num_modulos?: number;
}

export const DEFAULT_GONDOLA_FIELDS: Required<GondolaFieldsCamel> = {
    gondolaName: '',
    name: '',
    location: 'Corredor 1',
    side: 'A',
    scaleFactor: 3,
    flow: 'left_to_right',
    status: 'draft',
    alignment: 'left',
    numModules: 4,
};

export function generateGondolaCode(): string {
    const prefix = 'GND';
    const date = new Date();
    const year = date.getFullYear().toString().slice(2);
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    const random = Math.floor(Math.random() * 10000)
        .toString()
        .padStart(4, '0');

    return `${prefix}-${year}${month}-${random}`;
}

export function toSnakeCase(fields: GondolaFieldsCamel): GondolaFieldsSnake {
    return {
        name: fields.gondolaName || fields.name,
        location: fields.location,
        side: fields.side,
        scale_factor: fields.scaleFactor,
        flow: fields.flow,
        status: fields.status,
        alignment: fields.alignment,
        num_modulos: fields.numModules,
    };
}

export function toCamelCase(
    fields: GondolaFieldsSnake | Partial<Gondola>,
): GondolaFieldsCamel {
    return {
        gondolaName: fields.name,
        name: fields.name,
        location: fields.location,
        side: fields.side,
        scaleFactor: fields.scale_factor,
        flow: fields.flow as 'left_to_right' | 'right_to_left' | undefined,
        status: fields.status as 'draft' | 'published' | undefined,
        alignment: fields.alignment,
        numModules: fields.num_modulos,
    };
}

export function getInitialGondolaFields(
    existingGondola?: Partial<Gondola> | null,
    gondolaSettings?: Record<string, unknown> | null,
): GondolaFieldsCamel {
    if (existingGondola) {
        return {
            ...DEFAULT_GONDOLA_FIELDS,
            ...toCamelCase(existingGondola),
            gondolaName: existingGondola.name || generateGondolaCode(),
        };
    }

    if (gondolaSettings) {
        return {
            ...DEFAULT_GONDOLA_FIELDS,
            ...toCamelCase(gondolaSettings as GondolaFieldsSnake),
            gondolaName: generateGondolaCode(),
        };
    }

    return {
        ...DEFAULT_GONDOLA_FIELDS,
        gondolaName: generateGondolaCode(),
    };
}

export function validateGondolaFields(
    fields: Partial<GondolaFieldsCamel>,
): boolean {
    const name = fields.gondolaName || fields.name;

    return (
        !!name?.trim() &&
        !!fields.side?.trim() &&
        (fields.scaleFactor ?? 0) >= 1 &&
        (fields.flow === 'left_to_right' || fields.flow === 'right_to_left')
    );
}

export function useGondolaFields(initialData?: Partial<Gondola> | null) {
    const defaultFields = getInitialGondolaFields(initialData);

    return {
        defaults: DEFAULT_GONDOLA_FIELDS,
        initialFields: defaultFields,
        generateCode: generateGondolaCode,
        toSnakeCase,
        toCamelCase,
        validate: validateGondolaFields,
    };
}
