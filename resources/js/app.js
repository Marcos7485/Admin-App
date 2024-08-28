import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createPinia } from 'pinia';
import { ZiggyVue } from 'ziggy-js';
import { Ziggy } from './ziggy'; // Asegúrate de que la ruta sea correcta
import './bootstrap';
import '/public/css/styles.css';
import '/public/js/scripts.js';

const pinia = createPinia();


createInertiaApp({
  resolve: name => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) });
    app.use(plugin);
    app.use(pinia);
    app.use(ZiggyVue, Ziggy); // Integración de Ziggy
    app.mount(el);
  },
});
