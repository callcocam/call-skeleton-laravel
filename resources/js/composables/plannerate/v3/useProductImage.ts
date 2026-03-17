import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { update } from '@/actions/App/Http/Controllers/Tenant/Plannerate/Api/ProductImageController';

export function useProductImage() {
    const isDownloading = ref(false);

    /**
     * Baixa e atualiza a imagem do produto a partir do servidor usando o EAN
     */
    async function downloadAndUpdateImage(
        productId: string,
        productEan?: string | null,
    ) {
        if (!productEan || isDownloading.value) {
            return false;
        }

        isDownloading.value = true;

        return new Promise<boolean>((resolve) => {
            const formData = new FormData();
            formData.append('product_id', productId);

            router.post(update.url(), formData, {
                preserveState: false,
                preserveScroll: true,
                onSuccess: () => {
                    resolve(true);
                },
                onError: (errors) => {
                    console.error('Erro ao atualizar imagem:', errors);
                    resolve(false);
                },
                onFinish: () => {
                    isDownloading.value = false;
                },
            });
        });
    }

    return {
        isDownloading,
        downloadAndUpdateImage,
    };
}
