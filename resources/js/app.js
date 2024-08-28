import './bootstrap';
import '/public/css/styles.css'
import '/public/js/scripts.js'
import { createApp } from 'vue/dist/vue.esm-bundler.js';
import Login from './components/sessions/Login.vue';
import { createPinia } from 'pinia';

const pinia = createPinia();


const app = createApp({});
app.use(pinia);
app.component('login-component', Login);

app.mount("#app");