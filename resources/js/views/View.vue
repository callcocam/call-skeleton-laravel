<template>
    <div>View.vue</div>
</template>
<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import { apiService } from '../services';
import { useEditorStore } from '../store/editor';

const route = useRoute();
const id = ref<string>(route.params.id as string);
const isLoading = ref<boolean>(false);

const editorStore = useEditorStore();

const get = async () => {
    const response  = await apiService.get('plannerate/'.concat(id.value));
    console.log('response', response.data);
    if (response) {
    }
};

onMounted(async () => {
    isLoading.value = true;
    await get();
    isLoading.value = false;
});
</script>
