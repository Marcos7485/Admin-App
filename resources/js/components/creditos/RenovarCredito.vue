<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import { useImageStore } from '../../../store/imageStore.ts';
import { useSelectedIdStore } from '../../../store/selectedIdStore.ts'

const imageStore = useImageStore();
onMounted(() => {
    imageStore.fetchImagePath();
});


interface CuotaOption {
    value: number | string;
}

const CuotaOptions = ref<CuotaOption[]>([]);

const fetchCuotas = async (modalidad: string) => {
    try {
        const response = await axios.get(`/cuotas/${modalidad}`);
        CuotaOptions.value = response.data.map((cuota: any) => ({
            value: cuota.value
        }));
    } catch (error) {
        console.log('Error al obtener ciertos valores');
    }
};


const selectedIdStore = useSelectedIdStore()
const selectedId = selectedIdStore.selectedId


interface CreditoData {
    id: string | number;
    cliente: number | string;
    credito: string;
    cuotas: string;
    modalidad: string;
    inicio: string;
    lugar_cobro: string;
    interes: string;
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
    interes: ''
});

interface FormData {
    id: string | number;
    cliente: number | string;
    credito: string | number;
    cuotas: string | number;
    modalidad: string;
    inicio: string;
    lugar_cobro: string;
    interes: string;
}


const formData = ref<FormData>({
    id: '',
    cliente: '',
    credito: '',
    cuotas: '',
    modalidad: '',
    inicio: '',
    lugar_cobro: '',
    interes: ''
});

// Función para cargar datos del cliente
const loadCreditoData = async () => {
    const response = await fetch(`/info/credito/${selectedId}`);
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
        interes: newData.interes,
    };
}, { immediate: true });

watch(() => formData.value.modalidad, (newModalidad) => {
    if (newModalidad) {
        fetchCuotas(newModalidad);
    } else {
        CuotaOptions.value = [];
    }
});
const responseMessage = ref<string | null>(null);
const isDisabled = ref<boolean>(false);

const emit = defineEmits(['changeComponent']);

const submitForm = async () => {
    if (isDisabled.value) return;
    isDisabled.value = true;

    try {
        console.log(formData.value);
        const response = await axios.post('/renovar/credito', formData.value);
        responseMessage.value = response.data.message;
        RegistrarCliente(formData.value.cliente);
        emit('changeComponent', 'FicheroCliente');
    } catch (error) {
        console.error('Error enviando formulario', error);
        responseMessage.value = 'Error al editar cliente.';
    } finally {
        isDisabled.value = false;
    }
};

function RegistrarCliente(id): void {
    selectedIdStore.setSelectedId(id);
}


function cancelForm() {
    FormClear();
    emit('changeComponent', 'Renovacion');
}

function FormClear() {
    formData.value = {
        id: '',
        cliente: '',
        credito: '',
        cuotas: '',
        modalidad: '',
        inicio: '',
        lugar_cobro: '',
        interes: '',
    };
}

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
                                <p class="input-chico">
                                    <label for="inicio">Cliente </label>
                                    <input v-model="formData.id" type="hidden">
                                    <input type="text" :placeholder="String(formData.cliente)" disabled>
                                </p>
                            </div>
                            
                            <div>
                                <p>
                                    <label for="inicio">Credito</label>
                                    <input v-model="formData.credito" type="number" placeholder="Credito otorgado"
                                        required>
                                </p>
                            </div>
                            <div>
                                <p>
                                    <label for="inicio">Modalidad</label>
                                    <select v-model="formData.modalidad" required>
                                        <option value="Diaria">Diaria</option>
                                        <option value="Semanal">Semanal</option>
                                    </select>
                                </p>
                            </div>
                        </div>
                        <div class="linea2">
                            <div>
                                <p>
                                    <label for="inicio">Cuotas</label>
                                    <select v-model="formData.cuotas" required>
                                        <option :value="formData.cuotas" disabled selected>{{ formData.cuotas }}
                                        </option>
                                        <option v-for="cuota in CuotaOptions" :key="cuota.value" :value="cuota.value">{{
                                            cuota.value }}</option>
                                    </select>
                                </p>
                            </div>
                            <div>
                                <p>
                                    <label for="inicio">Inicio</label>
                                    <input v-model="formData.inicio" type="date" placeholder="Inicio" required>
                                </p>
                            </div>
                            <div>
                                <p>
                                    <label for="inicio">Cobrar en</label>
                                    <select v-model="formData.lugar_cobro" required>
                                        <option value="Domicilio">Domicilio</option>
                                        <option value="Comercio">Comercio</option>
                                    </select>
                                </p>
                            </div>  
                            <div>
                                <p class="input-chico">
                                    <label for="interes">Interes</label>
                                    <input v-model="formData.interes" type="number" required>
                                    <label for="interes">%</label>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="linea3">
                        <div>
                            <button class="btn btn-danger" @click="cancelForm">Cancelar</button>
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

.linea3 button {
    font-size: var(--fontsize);
}

.linea3 div {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    padding: 1rem;
    flex: 1;
}

.linea2 select {
    font-size: var(--fontsize);
    width: 15rem;
}

.linea2 label {
    font-size: var(--fontsize);
    margin-right: 1rem;
}
.input-chico input {
    width: 10rem;
}

.linea2 input {
    font-size: var(--fontsize);
}

.linea2 div {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    padding: 1rem;
    flex: 1;
}

.linea1 label {
    font-size: var(--fontsize);
    margin-right: 1rem;
}

.linea1 select {
    font-size: var(--fontsize);
    width: 25rem;
}

.linea1 input {
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