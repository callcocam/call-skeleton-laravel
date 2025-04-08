import './../css/app.css';
import type { App } from 'vue'
import Plannerate from './App.vue';
// @ts-ignore 
interface PluginOptions {
    [key: string]: any
}

const install = (app: App, options: PluginOptions = {}) => {
    const componentRegistry: string[] = [];
    app.component('Plannerate', Plannerate);
    app.component('v-plannerate', Plannerate);
    console.log('Plannerate', Plannerate)
    app.config.globalProperties.$plannerate = options
}

export default {
    install
}