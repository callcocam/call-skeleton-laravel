import type { Section } from '@planogram/types/planogram';

export interface SectionFieldsCamel {
    name?: string;
    height?: number;
    width?: number;
    baseHeight?: number;
    baseWidth?: number;
    baseDepth?: number;
    rackWidth?: number;
    holeHeight?: number;
    holeWidth?: number;
    holeSpacing?: number;
    numShelves?: number;
}

export interface SectionFieldsSnake {
    name?: string;
    height?: number;
    width?: number;
    base_height?: number;
    base_width?: number;
    base_depth?: number;
    cremalheira_width?: number | string;
    hole_height?: number;
    hole_width?: number;
    hole_spacing?: number;
    num_shelves?: number;
}

export const DEFAULT_SECTION_FIELDS: Required<SectionFieldsCamel> = {
    name: '',
    height: 200,
    width: 100,
    baseHeight: 20,
    baseWidth: 100,
    baseDepth: 50,
    rackWidth: 4,
    holeHeight: 3,
    holeWidth: 2,
    holeSpacing: 2,
    numShelves: 4,
};

export function toSnakeCase(fields: SectionFieldsCamel): SectionFieldsSnake {
    return {
        name: fields.name,
        height: fields.height,
        width: fields.width,
        base_height: fields.baseHeight,
        base_width: fields.baseWidth,
        base_depth: fields.baseDepth,
        cremalheira_width: fields.rackWidth,
        hole_height: fields.holeHeight,
        hole_width: fields.holeWidth,
        hole_spacing: fields.holeSpacing,
        num_shelves: fields.numShelves,
    };
}

export function toCamelCase(
    fields: SectionFieldsSnake | Partial<Section>,
): SectionFieldsCamel {
    return {
        name: fields.name,
        height: fields.height,
        width: fields.width,
        baseHeight: fields.base_height,
        baseWidth: fields.base_width,
        baseDepth: fields.base_depth,
        rackWidth:
            typeof fields.cremalheira_width === 'number'
                ? fields.cremalheira_width
                : parseFloat(fields.cremalheira_width as string) ||
                  DEFAULT_SECTION_FIELDS.rackWidth,
        holeHeight: fields.hole_height,
        holeWidth: fields.hole_width,
        holeSpacing: fields.hole_spacing,
        numShelves: fields.num_shelves,
    };
}

export function getInitialSectionFields(
    existingSection?: Partial<Section> | null,
    lastSection?: Partial<Section> | null,
    gondolaHeight?: number,
    sectionIndex?: number,
): SectionFieldsCamel {
    if (existingSection) {
        return {
            ...DEFAULT_SECTION_FIELDS,
            ...toCamelCase(existingSection),
        };
    }

    if (lastSection) {
        const lastFields = toCamelCase(lastSection);

        return {
            ...DEFAULT_SECTION_FIELDS,
            ...lastFields,
            name: `Módulo ${(sectionIndex ?? 0) + 1}`,
            height:
                gondolaHeight ??
                lastFields.height ??
                DEFAULT_SECTION_FIELDS.height,
        };
    }

    return {
        ...DEFAULT_SECTION_FIELDS,
        name: `Módulo ${(sectionIndex ?? 0) + 1}`,
        height: gondolaHeight ?? DEFAULT_SECTION_FIELDS.height,
    };
}

export function validateSectionFields(
    fields: Partial<SectionFieldsCamel>,
): boolean {
    return (
        (fields.height ?? 0) >= 1 &&
        (fields.width ?? 0) >= 1 &&
        (fields.baseHeight ?? 0) >= 1 &&
        (fields.baseWidth ?? 0) >= 1 &&
        (fields.baseDepth ?? 0) >= 1 &&
        (fields.rackWidth ?? 0) >= 1 &&
        (fields.holeHeight ?? 0) >= 1 &&
        (fields.holeWidth ?? 0) >= 0.1 &&
        (fields.holeSpacing ?? 0) >= 0.1
    );
}

export function calculateUsableHeight(
    height: number,
    baseHeight: number,
): number {
    return Math.max(0, height - baseHeight);
}

export function useSectionFields(
    initialData?: Partial<Section> | null,
    lastSection?: Partial<Section> | null,
    gondolaHeight?: number,
    sectionIndex?: number,
) {
    const defaultFields = getInitialSectionFields(
        initialData,
        lastSection,
        gondolaHeight,
        sectionIndex,
    );

    return {
        defaults: DEFAULT_SECTION_FIELDS,
        initialFields: defaultFields,
        toSnakeCase,
        toCamelCase,
        validate: validateSectionFields,
        calculateUsableHeight,
    };
}
