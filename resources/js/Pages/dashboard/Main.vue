<script setup lang="ts">
import { ref, defineProps, onMounted, markRaw } from 'vue';
import SideMenu from '../../components/dashboard/SideMenu.vue';
import FooterComponent from '../../components/layouts/Footer.vue';
import { useImageStore } from '../../../store/imageStore.ts';
import Principal from '../../components/dashboard/Principal.vue';
import FormCliente from '../../components/clientes/FormCliente.vue';
import BuscarCliente from '../../components/clientes/BuscarCliente.vue';
import EditarCliente from '../../components/clientes/EditarCliente.vue';
import EditarCredito from '../../components/creditos/EditarCredito.vue';
import FormCredito from '../../components/creditos/FormCredito.vue';
import Creditos from '../../components/creditos/Creditos.vue';

const imageStore = useImageStore();

onMounted(() => {
    imageStore.fetchImagePath();
});

const components = {
    Principal: markRaw(Principal),
    FormCliente: markRaw(FormCliente),
    BuscarCliente: markRaw(BuscarCliente),
    EditarCliente: markRaw(EditarCliente),
    FormCredito: markRaw(FormCredito),
    Creditos: markRaw(Creditos),
    EditarCredito: markRaw(EditarCredito),
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
            <component :is="currentComponent" @changeComponent="changeComponent"/>
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
    width: 90.5%;
    height: 100vh;
    overflow-x: hidden;
    overflow-y: hidden;
}
</style>