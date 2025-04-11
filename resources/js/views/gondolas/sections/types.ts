
/**
 * Interfaces para tipagem do componente
 */

// Interface para o produto
interface Product {
    id: string | number;
    name: string;
    image: string;
    height: number;
    [key: string]: any; // Para propriedades adicionais do produto
}

// Interface para a camada que representa o produto no segmento
interface Layer {
    product_id?: string | number;
    product_name?: string;
    product_image?: string;
    product: Product;
    height: number;
    spacing: number;
    quantity: number;
    status: string;
    [key: string]: any; // Para propriedades adicionais da camada
}

// Interface para um segmento individual
interface Segment {
    id: string;
    width: number;
    ordering: number;
    quantity: number;
    spacing: number;
    position: number;
    preserveState: boolean;
    status: string;
    layer: Layer;
    [key: string]: any; // Para propriedades adicionais do segmento
}

// Interface para uma prateleira
interface Shelf {
    id: string | number;
    shelf_height: number;
    shelf_position: number;
    quantity: number;
    spacing: number;
    segments: Segment[];
    [key: string]: any; // Para propriedades adicionais da prateleira
}

export type { Product, Layer, Segment, Shelf };