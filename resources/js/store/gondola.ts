import { defineStore } from 'pinia';
import { apiService } from '../services';
import { useProductStore } from './product';

interface GondolaState {
    currentGondola: any | null;
    currentSection: any | null;
    currentShelf: any | null;
    currentProduct: any | null;
    notInGondola: any | null;
    productIdsInGondola: string[];
    isLoading: boolean;
    error: string | null;
}

export const useGondolaStore = defineStore('gondola', {
    state: (): GondolaState => ({
        currentGondola: null,
        currentSection: null,
        currentShelf: null,
        currentProduct: null,
        notInGondola: null,
        productIdsInGondola: [],
        isLoading: false,
        error: null
    }),

    getters: {
        getProductIdsInGondola: (state: GondolaState): string[] => state.productIdsInGondola,
    },

    actions: {
        /**
         * Busca uma gôndola específica pelo ID
         * @param gondolaId ID da gôndola a ser carregada
         */
        async fetchGondola(gondolaId: string) {
            if (!gondolaId) return;

            this.isLoading = true;
            this.error = null;

            try {
                // Limpa o estado atual para evitar misturar dados
                this.clearGondola();

                // Busca os dados da API
                const response = await apiService.get(`gondolas/${gondolaId}`);

                // Atualiza o estado com o resultado
                this.currentGondola = response.data;

                this.productsInCurrentGondolaIds();
            } catch (error: any) {
                this.error = error.message || 'Erro ao carregar gôndola';
                console.error('Erro ao carregar gôndola:', error);
            } finally {
                this.isLoading = false;
            }
        },
        /**
         * seta a seção atual
         * @param section seção atual
         */
        setCurrentSection(section: any) {
            this.currentSection = section;
        },
        /**
         * seta a prateleira atual
         * @param shelf prateleira atual
         */
        setCurrentShelf(shelf: any) {
            this.currentShelf = shelf;
        },

        /**
         * Limpa o estado da gôndola atual
         */
        clearGondola() {
            this.currentGondola = null;
            this.error = null;
        },
        productsInCurrentGondolaIds() {
            const gondola = this.currentGondola;
            if (!gondola?.sections) {
                this.productIdsInGondola = [];
                return [];
            }
            
            const productIds = new Set<string>();
            gondola.sections.forEach((section: any) => {
                section.shelves?.forEach((shelf: any) => {
                    shelf.segments?.forEach((segment: any) => {
                        if (segment.layer?.product?.id) {
                            productIds.add(String(segment.layer.product.id));
                        }
                    });
                });
            });
            
            const finalIds = Array.from(productIds);
            this.productIdsInGondola = finalIds;
            
            return finalIds;
        },
        /**
         * addiciona uma prateleira a uma seção
         * @param sectionId ID da seção
         * @param shelf Dados da prateleira a ser adicionada
         * @returns {Promise<void>}
         */
        async addShelfToSection(sectionId: string, shelf: any) {
            if (!this.currentGondola || !sectionId || !shelf) return;

            try {
                // 1. Primeiro, atualizamos o estado localmente para feedback imediato
                const updatedSections = this.currentGondola.sections.map((section: any) => {
                    if (section.id === sectionId) {
                        // Adiciona a nova prateleira à seção
                        return { ...section, shelves: [...(section.shelves || []), shelf] };
                    }
                    return section;
                });

                // Atualiza o estado da gôndola com as seções atualizadas
                this.currentGondola = {
                    ...this.currentGondola,
                    sections: updatedSections
                };
                
                this.productsInCurrentGondolaIds(); // Recalculate used IDs

                // 2. Em seguida, enviamos a nova prateleira para o backend
                // const response = await apiService.post(`sections/${sectionId}/shelves`, shelf);

                // 3. Opcionalmente, você pode atualizar o estado novamente com a resposta do servidor
                // se necessário para garantir consistência

                // return response.data;
            } catch (error: any) {
                console.error('Erro ao adicionar prateleira:', error);
                throw error;
            }
        },
        /**
         * remove uma prateleira de uma seção
         * @param sectionId ID da seção
         * @param shelfId ID da prateleira a ser removida
         */
        async removeShelfFromSection(sectionId: string, shelfId: string) {
            if (!this.currentGondola || !sectionId || !shelfId) return;
            try {
                // 1. Primeiro, atualizamos o estado localmente para feedback imediato
                const updatedSections = this.currentGondola.sections.map((section: any) => {
                    if (section.id === sectionId) {
                        // Filtra as prateleiras para remover a prateleira especificada
                        const updatedShelves = section.shelves.filter((shelf: any) => shelf.id !== shelfId);
                        return { ...section, shelves: updatedShelves };
                    }
                    return section;
                });
                // Atualiza o estado da gôndola com as seções atualizadas
                this.currentGondola = {
                    ...this.currentGondola,
                    sections: updatedSections
                };
                
                this.productsInCurrentGondolaIds(); // Recalculate used IDs

                // 2. Em seguida, enviamos a remoção para o backend
                // const response = await apiService.delete(`shelves/${shelfId}`);
                // 3. Opcionalmente, você pode atualizar o estado novamente com a resposta do servidor
                // se necessário para garantir consistência
                // return response.data;
            } catch (error: any) {
                console.error('Erro ao remover prateleira:', error);
                // Em caso de erro, você pode querer desfazer a alteração local
                // ou recarregar a gôndola inteira
                // this.fetchGondola(this.currentGondola.id);
                throw error;
            }
        },
        /**
         * Atualiza os dados de uma prateleira
         * @param shelfId ID da prateleira
         * @param shelfData Dados atualizados da prateleira
         */
        async updateShelf(shelfId: string, shelfData: any) {
            if (!this.currentGondola || !shelfId || !shelfData) return;
            try {
                // 1. Primeiro, atualizamos o estado localmente para feedback imediato
                const updatedSections = this.currentGondola.sections.map((section: any) => {
                    // Procura a prateleira correta em cada seção
                    if (section.shelves) {
                        const updatedShelves = section.shelves.map((shelf: any) => {
                            if (shelf.id === shelfId) {
                                // Retorna um novo objeto com os dados atualizados
                                return { ...shelf, ...shelfData };
                            }
                            return shelf;
                        });
                        // Retorna uma nova seção com as prateleiras atualizadas
                        return { ...section, shelves: updatedShelves };
                    }
                    return section;
                });
                // Atualiza o estado da gôndola com as seções atualizadas
                this.currentGondola = {
                    ...this.currentGondola,
                    sections: updatedSections
                };
                
                this.productsInCurrentGondolaIds(); // Recalculate used IDs

                // 2. Em seguida, enviamos a atualização para o backend
                const response = await apiService.put(`shelves/${shelfId}`, shelfData); 
                // 3. Opcionalmente, você pode atualizar o estado novamente com a resposta do servidor
                // se necessário para garantir consistência
                // return response.data;
            } catch (error: any) {
                console.error(`Erro ao atualizar prateleira ${shelfId}:`, error);
                // Em caso de erro, você pode querer desfazer a alteração local
                // ou recarregar a gôndola inteira
                // this.fetchGondola(this.currentGondola.id);
                throw error;
            }
        },
        /**
         * Atualiza o segmento de uma prateleira
         * @param shelfId ID da prateleira
         * @param segmentId ID do segmento a ser atualizado
         * @param segmentData Dados atualizados do segmento
         */
        async updateSegment(shelfId: string, segmentId: string, segmentData: any, reorder: boolean = false) {
            if (!this.currentGondola || !shelfId || !segmentId || !segmentData) return;
            try {
                // 1. Primeiro, atualizamos o estado localmente para feedback imediato
                const updatedSections = this.currentGondola.sections.map((section: any) => {
                    // Procura a prateleira correta em cada seção
                    if (section.shelves) {
                        const updatedShelves = section.shelves.map((shelf: any) => {
                            if (shelf.id === shelfId) {
                                // Procura o segmento correto na prateleira
                                const updatedSegments = shelf.segments.map((segment: any) => {
                                    if (segment.id === segmentId) {
                                        // Retorna um novo objeto com os dados do segmento atualizados
                                        return { ...segment, ...segmentData };
                                    }
                                    return segment;
                                });
                                // Retorna uma nova prateleira com os segmentos atualizados
                                return { ...shelf, segments: updatedSegments };
                            }
                            return shelf;
                        });
                        // Retorna uma nova seção com as prateleiras atualizadas
                        return { ...section, shelves: updatedShelves };
                    }
                    return section;
                });
                // Atualiza o estado da gôndola com as seções atualizadas
                this.currentGondola = {
                    ...this.currentGondola,
                    sections: updatedSections
                };
                
                this.productsInCurrentGondolaIds(); // Recalculate used IDs

                // 2. Em seguida, enviamos a atualização para o backend
                // Se o segmento for reordenado, envie a atualização de ordem
                if (reorder) {
                    const response = await apiService.put(`segments/${shelfId}/reorder`, {
                        ordering: segmentData
                    });
                    console.log('Resposta do servidor:', response.data);
                } else {
                    // Caso contrário, envie a atualização normal
                    const response = await apiService.put(`segments/${segmentId}`, segmentData);
                    console.log('Resposta do servidor:', response.data);
                }
                // 3. Opcionalmente, você pode atualizar o estado novamente com a resposta do servidor
                // se necessário para garantir consistência
                // return response.data;
            } catch (error: any) {
                console.error(`Erro ao atualizar segmento ${segmentId} da prateleira ${shelfId}:`, error);
                // Em caso de erro, você pode querer desfazer a alteração local
                // ou recarregar a gôndola inteira
                // this.fetchGondola(this.currentGondola.id);
                throw error;
            }
        },


        /**
         * Atualiza a posição vertical de uma prateleira
         * @param shelfId ID da prateleira
         * @param newPosition Nova posição vertical em cm
         */
        async updateShelfPosition(shelfId: string | number, newPosition: number) {
            if (!this.currentGondola || !shelfId) return;

            try {
                // 1. Primeiro, atualizamos o estado localmente para feedback imediato
                const updatedSections = this.currentGondola.sections.map((section: any) => {
                    // Procura a prateleira correta em cada seção
                    if (section.shelves) {
                        const updatedShelves = section.shelves.map((shelf: any) => {
                            if (shelf.id === shelfId) {
                                // Retorna um novo objeto com a posição atualizada
                                return { ...shelf, shelf_position: newPosition };
                            }
                            return shelf;
                        });

                        // Retorna uma nova seção com as prateleiras atualizadas
                        return { ...section, shelves: updatedShelves };
                    }
                    return section;
                });

                // Atualiza o estado da gôndola com as seções atualizadas
                this.currentGondola = {
                    ...this.currentGondola,
                    sections: updatedSections
                };

                // 2. Em seguida, enviamos a atualização para o backend
                // const response = await apiService.put(`shelves/${shelfId}/position`, {
                //   position: newPosition
                // });

                // 3. Opcionalmente, você pode atualizar o estado novamente com a resposta do servidor
                // se necessário para garantir consistência

                // return response.data;
            } catch (error: any) {
                console.error(`Erro ao atualizar posição da prateleira ${shelfId}:`, error);
                // Em caso de erro, você pode querer desfazer a alteração local
                // ou recarregar a gôndola inteira
                // this.fetchGondola(this.currentGondola.id);
                throw error;
            }
        },

        /**
         * Atualiza os dados de uma gôndola
         * @param gondolaData Dados atualizados da gôndola
         */
        updateGondola(gondolaData: any) {
            if (!this.currentGondola || !gondolaData) return;

            this.currentGondola = {
                ...this.currentGondola,
                ...gondolaData
            };
            this.productsInCurrentGondolaIds(); // This now updates productIdsInGondola
        }
    }
});
