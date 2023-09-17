import './bootstrap';
import { createApp } from 'vue';
import index from "./components/index.vue"
import router from './router/index.js'

const app = createApp(index);
app.use(router);
app.mount("#app")