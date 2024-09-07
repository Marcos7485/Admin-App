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
import FicheroCliente from '../../components/creditos/FicheroCliente.vue';
import Refinanciacion from '../../components/creditos/Refinanciacion.vue';
import RefinanciarCredito from '../../components/creditos/RefinanciarCredito.vue';
import Renovacion from '../../components/creditos/Renovacion.vue';
import RenovarCredito from '../../components/creditos/RenovarCredito.vue';
import FormPagos from '../../components/pagos/FormPagos.vue';
import Pagos from '../../components/pagos/Pagos.vue';
import EditarPago from '../../components/pagos/EditarPago.vue';
import Resumenes from '../../components/resumenes/Resumenes.vue';
import RecorridoHoy from '../../components/cobradores/RecorridoHoy.vue';
import Recorridos from '../../components/cobradores/Recorridos.vue';
import RecorridoDetails from '../../components/cobradores/RecorridoDetails.vue';
import Seguridad from '../../components/configuraciones/Seguridad.vue';

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
    FicheroCliente: markRaw(FicheroCliente),
    Refinanciacion: markRaw(Refinanciacion),
    Renovacion: markRaw(Renovacion),
    RefinanciarCredito: markRaw(RefinanciarCredito),
    RenovarCredito: markRaw(RenovarCredito),
    FormPagos: markRaw(FormPagos),
    Pagos: markRaw(Pagos),
    EditarPago: markRaw(EditarPago),
    Resumenes: markRaw(Resumenes),
    RecorridoHoy: markRaw(RecorridoHoy),
    Recorridos: markRaw(Recorridos),
    RecorridoDetails: markRaw(RecorridoDetails),
    Seguridad: markRaw(Seguridad),
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