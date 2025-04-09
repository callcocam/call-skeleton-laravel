<template>
    <div>
        <Info v-if="gondola" :gondola="gondola" />
    </div>
</template>
<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useEditorStore } from '../../store/editor';

import { apiService } from '../../services';
import Info from './partials/Info.vue';

const route = useRoute();
const router = useRouter();
const id = ref<string>(route.params.gondolaId as string);

const isLoading = ref<boolean>(false);
const editorStore = useEditorStore();

const gondola = ref<any>(null); // Substitua 'any' pelo tipo correto, se possível

const get = async () => {
    const response = await apiService.get('gondolas/'.concat(id.value));
    gondola.value = response.data;
};
onMounted(async () => {
    isLoading.value = true;
    await get();
    isLoading.value = false;
});
</script>
