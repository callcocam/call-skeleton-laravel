import './../css/app.css';
import type { App } from 'vue'
import Plannerate from './App.vue';
import router from './routes';
// @ts-ignore 
interface PluginOptions {
    [key: string]: any
}

const install = (app: App, options: PluginOptions = {}) => {
    const componentRegistry: string[] = [];
    app.component('Plannerate', Plannerate);
    app.component('v-plannerate', Plannerate); 

    app.use(router);

    app.config.globalProperties.$plannerate = options
}

export default {
    install
}