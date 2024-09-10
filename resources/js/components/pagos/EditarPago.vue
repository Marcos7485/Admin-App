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

interface PagoData {
    id: string | number;
    cliente: string | number;
    valor: number | string;
    pago_fecha: string;
}

// Tipar clienteData con la interfaz
const pagoData = ref<PagoData>({
    id: '',
    cliente: '',
    valor: '',
    pago_fecha: ''
});

interface FormData {
    id: string | number;
    cliente: string | number;
    valor: number | string;
    pago_fecha: string;
}


const formData = ref<FormData>({
    id: '',
    cliente: '',
    valor: '',
    pago_fecha: ''
});

// Función para cargar datos del cliente
const loadPagosData = async () => {
    const response = await fetch(`/info/pago/${selectedId}`);
    if (response.ok) {
        const data = await response.json();
        pagoData.value = data;
    }
};

// Cargar datos del cliente al montar el componente
onMounted(() => {
    loadPagosData();
});

// Actualizar formData cuando creditoData cambia
watch(pagoData, (newData) => {
    formData.value = {
        id: newData.id,
        cliente: newData.cliente,
        valor: newData.valor,
        pago_fecha: newData.pago_fecha,
    };
}, { immediate: true });

const responseMessage = ref<string | null>(null);
const isDisabled = ref<boolean>(false);

const emit = defineEmits(['changeComponent']);

const submitForm = async () => {
    if (isDisabled.value) return;
    isDisabled.value = true;

    try {
        const response = await axios.post('/modify/pago', formData.value);
        responseMessage.value = response.data.message;
        RegistrarCliente(formData.value.cliente);
        emit('changeComponent', 'Pagos');
    } catch (error) {
        console.error('Error enviando formulario', error);
        responseMessage.value = 'Error al editar cliente.';
    } finally {
        isDisabled.value = false;
    }
};

function cancelForm() {
    FormClear();
    emit('changeComponent', 'Pagos');
}

function FormClear() {
    formData.value = {
        id: '',
        cliente: '',
        valor: '',
        pago_fecha: ''
    };
}
function RegistrarCliente(id): void {
    selectedIdStore.setSelectedId(id);
}

</script>


<template>
    <div class="content">
        <div class="box">
            <div class="form">
                <form @submit.prevent="submitForm">
                    <div class="credito">
                        <h1>Editar Pago</h1>
                        <hr>
                        <div class="linea2">
                            <div class="input-chico">
                                <p>
                                    <label for="id">Cliente</label>
                                    <input v-model="formData.cliente" type="number" placeholder="id" required>
                                </p>
                            </div>
                            <div>
                                <p>$ <input v-model="formData.valor" type="number" placeholder="Valor" required></p>
                            </div>
                            <div>
                                <p>
                                    <label for="inicio">Fecha</label>
                                    <input v-model="formData.pago_fecha" type="date" placeholder="Inicio" required>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="linea4">
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
.credito {
    display: flex;
    flex-direction: column;
    margin-top: 5rem;
    font-size: var(--fontsize);
    font-size: 1.3rem;
}


.linea4 button {
    border-radius: 1rem 2rem;
    width: 25rem;
    height: 5rem;
    padding: 1rem;
    color: black;
    font-size: var(--fontsize);
}

.linea4 div {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    padding: 1rem;
    flex: 1;
}

.linea2 div p {
    font-size: var(--fontsize);
    margin-right: 1.5rem;
}

.linea2 input,
select {
    font-size: var(--fontsize);
}

.linea2 div {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    padding: 1rem;
    flex: 1;
}

.form label {
    padding-inline: 1rem;
}

.form select {
    background-color: white;
    border-radius: 1rem 2rem;
    width: 23rem;
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
}

h1 {
    font-size: var(--fontsizeTitles);
}

.box {
    border: solid .2rem grey;
    padding: 2rem;
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

@media (max-width: 600px) {

    .content {
        margin-top: 15rem;
    }

    .form div {
        flex-direction: column;
        align-items: center;
    }

    .form input {
        width: 15rem;
    }

}
</style>