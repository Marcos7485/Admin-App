<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useImageStore } from '../../../store/imageStore.ts';

const imageStore = useImageStore();
onMounted(() => {
    imageStore.fetchImagePath();
});


interface FormData {
    name: string;
    dni: string | number;
    phone: string | number;
    address: string;
    localidad: string;
    comercio_address: string;
    comercio_localidad: string;
    comercio_tipo: string;
    recorrido: string;
}

const formData = ref<FormData>({
    name: '',
    dni: '',
    phone: '',
    address: '',
    localidad: '',
    comercio_address: '',
    comercio_localidad: '',
    comercio_tipo: '',
    recorrido: '',
});

const responseMessage = ref<string | null>(null);
const isDisabled = ref<boolean>(false);

const submitForm = async () => {
    if (isDisabled.value) return;
    isDisabled.value = true;

    try {
        const response = await axios.post('/new/cliente', formData.value);
        responseMessage.value = response.data.message;
        formData.value = {
            name: '',
            dni: '',
            phone: '',
            address: '',
            localidad: '',
            comercio_address: '',
            comercio_localidad: '',
            comercio_tipo: '',
            recorrido: '',
        };
    } catch (error) {
        console.error('Error enviando formulario', error);
        responseMessage.value = 'Error al guardar el cliente.';
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
                        <h1>Datos del cliente</h1>
                        <hr>
                        <div class="linea1">
                            <div>
                                <input v-model="formData.name" type="text" placeholder="Nombre" required>
                            </div>
                            <div>
                                <input v-model="formData.dni" type="number" placeholder="DNI" required>
                            </div>
                            <div>
                                <input v-model="formData.phone" type="number" placeholder="Telefono" required>
                            </div>
                            <div>
                                <input v-model="formData.address" type="text" placeholder="Domicilio" required>
                            </div>
                        </div>
                        <div class="linea2">
                            <div>
                                <input v-model="formData.localidad" type="text" placeholder="Localidad">
                            </div>
                        </div>
                    </div>

                    <div class="comercio">
                        <h1>Comercio</h1>
                        <hr>
                        <div class="linea3">
                            <div>
                                <input v-model="formData.comercio_address" type="text"
                                    placeholder="Direccion de comercio">
                            </div>
                            <div>
                                <input v-model="formData.comercio_localidad" type="text"
                                    placeholder="Localidad del comercio">
                            </div>
                            <div>
                                <input v-model="formData.comercio_tipo" type="text" placeholder="Tipo de comercio">
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="linea4">
                        <div>
                            <label for="recorrido">Recorrido</label>
                            <select v-model="formData.recorrido" required>
                                <option value="" disabled selected>Seleccione</option>
                                <option value="1">Recorrido 1</option>
                                <option value="2">Recorrido 2</option>
                                <option value="3">Recorrido 3</option>
                            </select>
                        </div>
                    </div>
                    <div class="linea5">
                        <div>
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

.linea5 div {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    padding: 1rem;
    flex: 1;
}

.linea4 div {
    display: flex;
    flex-direction: column;
    padding: 1rem;
    font-size: var(--fontsize);
}

.linea3 div {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    padding: 1rem;
    flex: 1;
}

.linea2 div {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    padding: 1rem;
    flex: 1;
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