<template>
    <!-- Área central rolável (vertical e horizontal) -->
    <div class="flex h-full w-full flex-col gap-6 overflow-x-auto overflow-y-auto">
        <NavigationMenu>
            <NavigationMenuList>
                <NavigationMenuItem class="flex items-center" v-for="gondola in gondolas" :key="gondola.id">
                    <NavigationMenuLink as-child>
                        <router-link
                            :to="{
                                name: 'gondola.view',
                                params: { gondolaId: gondola.id },
                            }"
                            class="flex items-center gap-2 rounded-md p-2 text-sm font-medium text-gray-900 hover:bg-gray-100 dark:text-gray-100 dark:hover:bg-gray-700"
                            :class="{
                                'bg-gray-100 dark:bg-gray-700': route.params.gondolaId == gondola.id,
                            }"
                        >
                            {{ gondola.name }}
                        </router-link>
                    </NavigationMenuLink>
                </NavigationMenuItem>
            </NavigationMenuList>
        </NavigationMenu>
        <router-view :key="route.fullPath" />
    </div>
</template>
<script setup lang="ts">
import { computed, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useEditorStore } from '../../store/editor';
import { NavigationMenu, NavigationMenuItem, NavigationMenuLink, NavigationMenuList } from './../../components/ui/navigation-menu';
const route = useRoute();
const router = useRouter();
const id = ref<string>(route.params.id as string);
const isLoading = ref<boolean>(false);

const editorStore = useEditorStore();

const gondolas = computed(() => {
    return editorStore.gondolas;
});
const selectedProducts = ref<any[]>([]); // Substitua 'any' pelo tipo correto, se possível
</script>
