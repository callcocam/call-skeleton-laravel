import './../css/app.css';
import type { App } from 'vue'
import Plannerate from './App.vue';
import router from './routes';
 // Vamos comfigurar o pinia para o plannerate
 // @ts-ignore
import { createPinia } from 'pinia';

const pinia = createPinia();
// @ts-ignore 
interface PluginOptions {
    [key: string]: any
}

const install = (app: App, options: PluginOptions = {}) => {
    const componentRegistry: string[] = [];
    app.component('Plannerate', Plannerate);
    app.component('v-plannerate', Plannerate); 

    app.use(router);

    app.use(pinia);

    app.config.globalProperties.$plannerate = options
}

export default {
    install
}