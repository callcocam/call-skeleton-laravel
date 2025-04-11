import { defineStore } from 'pinia';
// Remove direct axios import if no longer needed elsewhere, or keep if used for other things
// import axios from 'axios'; 
import { apiService } from '../services'; // Import your apiService

// Interface para representar um produto
export interface Product {
    id: string;
    name: string;
    description: string;
    price: number; // Assuming price is still relevant
    image?: string;
    width: number; // Width of the product in base units (e.g., mm)
    height: number; // Height of the product in base units (e.g., mm)
    category_id?: string;
    created_at?: string;
    updated_at?: string;
}

// Interface para dados contextuais de um produto (quantidade, espaçamento)
export interface ProductContext {
    quantity: number;
    spacing: number; // Spacing *after* this product in the layer/context
}

// Interface para o estado da store
export interface ProductState {
    products: Product[]; // Lista de todos os produtos disponíveis
    selectedProductIds: Set<string>; // IDs dos produtos selecionados
    productContextData: Map<string, ProductContext>; // Dados contextuais por ID de produto
    loading: boolean; // Indicador de carregamento para chamadas API
    error: string | null; // Armazena mensagens de erro
}

export const useProductStore = defineStore('product', {
    state: (): ProductState => ({
        products: [],
        selectedProductIds: new Set(),
        productContextData: new Map(),
        loading: false,
        error: null,
    }),

    getters: {
        /**
         * Retorna a lista completa de produtos.
         */
        getProducts: (state: ProductState): Product[] => state.products,

        /**
         * Retorna o Set de IDs dos produtos selecionados.
         */
        getSelectedProductIds: (state: ProductState): Set<string> => state.selectedProductIds,

        /**
         * Retorna um array com os objetos Product completos selecionados.
         */
        getSelectedProducts: (state: ProductState): Product[] => {
            const selectedIds = state.selectedProductIds;
            return state.products.filter(product => selectedIds.has(product.id));
        },

        /**
         * Retorna os dados contextuais (quantidade, espaçamento) para um produto específico.
         * @returns (productId: string) => ProductContext | undefined
         */
        getProductContext: (state: ProductState) => {
            return (productId: string): ProductContext | undefined => {
                return state.productContextData.get(productId);
            };
        },

        /**
         * Retorna o estado de carregamento.
         */
        isLoading: (state: ProductState): boolean => state.loading,

        /**
         * Retorna a mensagem de erro.
         */
        getError: (state: ProductState): string | null => state.error,
    },

    actions: {
        /**
         * Busca a lista de produtos da API e atualiza o estado.
         */
        async fetchProducts() {
            this.loading = true;
            this.error = null;
            try {
                // Use apiService instead of axios
                // Assuming apiService.get returns the data directly or in a 'data' property
                const response = await apiService.get<{ data: Product[] }>('/products'); // Use the correct endpoint path
                // Adjust the line below based on the actual structure returned by apiService.get
                this.products = response.data; 
            } catch (error: any) {
                // Adjust error handling based on how apiService throws/returns errors
                this.error = error.response?.data?.message || error.message || 'Failed to fetch products';
                console.error('Error fetching products:', error);
                this.products = []; // Limpa produtos em caso de erro
            } finally {
                this.loading = false;
            }
        },

        /**
         * Adiciona um produto à seleção.
         * @param productId ID do produto a selecionar.
         */
        selectProduct(productId: string) {
            this.selectedProductIds.add(productId);
        },

        /**
         * Remove um produto da seleção.
         * @param productId ID do produto a deselecionar.
         */
        deselectProduct(productId: string) {
            this.selectedProductIds.delete(productId);
        },

        /**
         * Alterna a seleção de um produto (seleciona se não estiver, deseleciona se estiver).
         * @param productId ID do produto a alternar.
         */
        toggleProductSelection(productId: string) {
            if (this.selectedProductIds.has(productId)) {
                this.selectedProductIds.delete(productId);
            } else {
                this.selectedProductIds.add(productId);
            }
        },

        /**
         * Limpa toda a seleção de produtos.
         */
        clearSelection() {
            this.selectedProductIds.clear();
        },

        /**
         * Define ou atualiza os dados contextuais (quantidade, espaçamento) para um produto.
         * Se o produto já tiver dados, os novos valores serão mesclados.
         * @param productId ID do produto.
         * @param context Partial<ProductContext> Novos dados contextuais (pode fornecer só quantity ou só spacing).
         */
        setProductContextData(productId: string, context: Partial<ProductContext>) {
            const existingContext = this.productContextData.get(productId) || { quantity: 1, spacing: 0 }; // Default values
            const newContext = { ...existingContext, ...context };
            
            // Garante que quantidade seja pelo menos 1
            if (newContext.quantity < 1) {
                newContext.quantity = 1;
            }
            // Garante que espaçamento não seja negativo (ou ajuste conforme necessário)
            if (newContext.spacing < 0) {
                newContext.spacing = 0;
            }

            this.productContextData.set(productId, newContext);
        },

        /**
         * [Placeholder] Envia os dados contextuais (quantidade, espaçamento) para o backend.
         * A implementação real precisará coletar os dados relevantes do productContextData
         * e enviá-los para a API apropriada usando apiService.
         */
        async syncWithBackend() {
            console.warn('syncWithBackend action called - Placeholder implementation using apiService');
            this.loading = true;
            this.error = null;
            try {
                const dataToSend = Object.fromEntries(this.productContextData);
                console.log('Data to send (placeholder):', dataToSend);

                // Use apiService for the backend call
                // const response = await apiService.post('/products/context', dataToSend); // Or apiService.put, etc.
                // Simulação de chamada API
                await new Promise(resolve => setTimeout(resolve, 1000)); 
                console.log('Simulated API call successful (using apiService pattern)');

            } catch (error: any) {
                 // Adjust error handling based on how apiService throws/returns errors
                this.error = error.response?.data?.message || error.message || 'Failed to sync with backend';
                console.error('Error syncing with backend:', error);
            } finally {
                this.loading = false;
            }
        },
    }
});
