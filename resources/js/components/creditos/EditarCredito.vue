<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import { useImageStore } from '../../../store/imageStore.ts';
import { useSelectedIdStore } from '../../../store/selectedIdStore.ts'

const imageStore = useImageStore();
onMounted(() => {
    imageStore.fetchImagePath();
});

const selectedIdStore = useSelectedIdStore()
const selectedId = selectedIdStore.selectedId

// Definir la interfaz
interface CreditoData {
    id: string | number;
    cliente: number | string;
    credito: string;
    cuotas: string;
    modalidad: string;
    inicio: string;
    lugar_cobro: string;
    status: string;
}

// Tipar clienteData con la interfaz
const creditoData = ref<CreditoData>({
    id: '',
    cliente: '',
    credito: '',
    cuotas: '',
    modalidad: '',
    inicio: '',
    lugar_cobro: '',
    status: ''
});

interface FormData {
    id: string | number;
    cliente: number | string;
    credito: string | number;
    cuotas: string | number;
    modalidad: string;
    inicio: string;
    lugar_cobro: string;
    status: string;
}


const formData = ref<FormData>({
    id: '',
    cliente: '',
    credito: '',
    cuotas: '',
    modalidad: '',
    inicio: '',
    lugar_cobro: '',
    status: ''
});

// Función para cargar datos del cliente
const loadCreditoData = async () => {
    const response = await fetch(`/edit/credito/${selectedId}`);
    if (response.ok) {
        const data = await response.json();
        creditoData.value = data;
    }
};

// Cargar datos del cliente al montar el componente
onMounted(() => {
    loadCreditoData();
});

// Actualizar formData cuando creditoData cambia
watch(creditoData, (newData) => {
    formData.value = {
        id: newData.id,
        cliente: newData.cliente,
        credito: newData.credito,
        cuotas: newData.cuotas,
        modalidad: newData.modalidad,
        inicio: newData.inicio,
        lugar_cobro: newData.lugar_cobro,
        status: newData.status,
    };
}, { immediate: true });


const responseMessage = ref<string | null>(null);
const isDisabled = ref<boolean>(false);

const emit = defineEmits(['changeComponent']);

const submitForm = async () => {
    if (isDisabled.value) return;
    isDisabled.value = true;

    try {
        const response = await axios.post('/modify/credito', formData.value);
        responseMessage.value = response.data.message;
        // Reiniciar formData solo si es necesario
        emit('changeComponent', 'Creditos');
    } catch (error) {
        console.error('Error enviando formulario', error);
        responseMessage.value = 'Error al editar cliente.';
    } finally {
        isDisabled.value = false;
    }
};
</script>


<template>
    <div class="content">
        <div class="box">
            <div class="form">
                <form @submit.prevent="submitForm">
                    <div class="cliente">
                        <h1>Editar Credito</h1>
                        <hr>
                        <div class="linea1">
                            <div>
                                <input v-model="formData.id" type="hidden">
                                <input v-model="formData.cliente" type="text" placeholder="id Cliente" required>
                            </div>
                            <div>
                                <input v-model="formData.credito" type="number" placeholder="Credito otorgado" required>
                            </div>
                            <div>
                                <input v-model="formData.cuotas" type="number" placeholder="Cuotas" required>
                            </div>
                            <div>
                                <select v-model="formData.modalidad" required>
                                    <option value="" disabled selected>Modalidad</option>
                                    <option value="Diaria">Diaria</option>
                                    <option value="Semanal">Semanal</option>
                                    <option value="Mensual">Mensual</option>
                                </select>
                            </div>
                        </div>
                        <div class="linea2">
                            <div>
                                <p>
                                    <label for="inicio">Inicio de cobro</label>
                                    <input v-model="formData.inicio" type="date" placeholder="Inicio" required>
                                </p>
                            </div>
                            <div>
                                <select v-model="formData.lugar_cobro" required>
                                    <option value="" disabled selected>Cobrar en</option>
                                    <option value="Domicilio">Domicilio</option>
                                    <option value="Comercio">Comercio</option>
                                </select>
                            </div>
                            <div>
                                <select v-model="formData.status" required>
                                    <option value="" disabled selected>Seleccione estado</option>
                                    <option value="Pendiente">Pendiente</option>
                                    <option value="Mora">Mora</option>
                                    <option value="Refinanciado">Refinanciado</option>
                                    <option value="Pagado">Pagado</option>
                                    <option value="Renovado">Renovado</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="linea3">
                        <div>
                            <button class="btn btn-danger" @click="$emit('changeComponent', 'Creditos')">Cancelar</button>
                            <button class="btn btn-info" type="submit" :disabled="isDisabled">Guardar</button>
                        </div>
                    </div>
                </form>
                <p v-if="responseMessage">{{ responseMessage }} <i class="fa-solid fa-circle-check"></i></p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.cliente {
    display: flex;
    flex-direction: column;
    margin-top: 1rem;
}

.comercio {
    display: flex;
    flex-direction: column;
    margin-top: 5rem;
}

.linea3 button{
    font-size: var(--fontsize);
}

.linea3 div {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    padding: 1rem;
    flex: 1;
}

.linea2 select{
    font-size: var(--fontsize);
}

.linea2 label{
    font-size: var(--fontsize);
}

.linea2 input{
    font-size: var(--fontsize);
}

.linea2 div {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    padding: 1rem;
    flex: 1;
}

.linea1 select{
    font-size: var(--fontsize);
}

.linea1 input{
    font-size: var(--fontsize);
}

.linea1 div {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    padding: 1rem;
    flex: 1;
}

.form p {
    font-size: 1.3rem;
    color: green;
}

.form button {
    border-radius: 1rem 2rem;
    width: 25rem;
    height: 5rem;
    padding: 1rem;
    color: black;
}

.form select {
    background-color: white;
    border-radius: 1rem 2rem;
    padding: 1rem;
    color: black;
}

.form input {
    background-color: white;
    border-radius: 1rem 2rem;
    padding: 1rem;
    color: black;
}

.form div {
    display: flex;
    justify-content: center;
    gap: 0rem;
    font-size: var(--fontsize);
}

h1 {
    font-size: var(--fontsizeTitles);
}

.box {
    border: solid .2rem grey;
    padding: 2rem;
    height: 100%;
    border-radius: 4rem;
}

.content {
    opacity: 0;
    padding: 3rem;
    animation: appear 1s forwards;
}

@keyframes appear {
    100% {
        opacity: 1;
    }
}
</style>