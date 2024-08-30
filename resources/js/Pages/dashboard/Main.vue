<script setup lang="ts">
import { ref, defineProps, onMounted, markRaw, computed } from 'vue';
import SideMenu from '../../components/dashboard/SideMenu.vue';
import FooterComponent from '../../components/layouts/Footer.vue';
import { useImageStore } from '../../../store/imageStore.ts';
import Principal from '../../components/dashboard/Principal.vue';
import FormCliente from '../../components/clientes/FormCliente.vue';
import BuscarCliente from '../../components/clientes/BuscarCliente.vue';
import ModificarCliente from '../../components/clientes/ModificarCliente.vue';

const imageStore = useImageStore();

onMounted(() => {
    imageStore.fetchImagePath();
});

const components = {
    Principal: markRaw(Principal),
    FormCliente: markRaw(FormCliente),
    BuscarCliente: markRaw(BuscarCliente),
    ModificarCliente: markRaw(ModificarCliente),
};

const currentComponent = ref(components.Principal);

function changeComponent(componentName: string) {
  currentComponent.value = components[componentName];
}

const props = defineProps<{
    user: {
        name: string;
        admin: boolean;
    };
}>();

const { user } = props;

</script>

<template>
    <div class="app-layout">
        <SideMenu :user="user" @changeComponent="changeComponent" />
        <div class="content">
            <component :is="currentComponent"></component>
        </div>
        <FooterComponent/>
    </div>
</template>

<style scoped>
.app-layout {
    position: relative;
    display: flex;
    width: 100vw;
    height: 100vh;
    background-color: white;
}

.content {
    margin-left: 14.7rem;
    width: 90.3%;
    height: 100vh;
    overflow-x: hidden;
    overflow-y: hidden;
}
</style>