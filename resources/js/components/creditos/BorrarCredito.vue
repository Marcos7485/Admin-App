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

interface FormData {
    id: string | number;
}


const formData = ref<FormData>({
    id: '',
});


const responseMessage = ref<string | null>(null);
const isDisabled = ref<boolean>(false);
const showModal = ref<boolean>(false); // Para controlar la visibilidad del modal

// Muestra el modal cuando se intenta eliminar
const openConfirmationModal = () => {
    showModal.value = true;
};

// Cierra el modal sin hacer nada
const closeModal = () => {
    showModal.value = false;
};

// Confirma la eliminación y envía el formulario
const confirmDelete = async () => {
    if (isDisabled.value) return;
    isDisabled.value = true;
    showModal.value = false; // Cierra el modal después de confirmar
    try {
        const response = await axios.post('/destroy/credito', formData.value);
        responseMessage.value = response.data.message;
    } catch (error) {
        console.error('Error enviando formulario', error);
        responseMessage.value = 'Error al eliminar el credito.';
    } finally {
        isDisabled.value = false;
    }
};



</script>


<template>
    <div class="content">
        <div class="box">
            <div class="form">
                <form @submit.prevent="openConfirmationModal">
                    <div class="cliente">
                        <h1>Eliminar Credito</h1>
                        <hr>
                        <div class="linea1">
                            <div>
                                <p class="input-chico">
                                    <label for="inicio">Credito </label>
                                    <input v-model="formData.id" type="number" placeholder="Id">
                                </p>
                            </div>
                        </div>
                        <div class="linea3">
                            <div>
                                <button class="btn btn-danger" type="submit" :disabled="isDisabled">Eliminar</button>
                            </div>
                        </div>
                    </div>
                </form>
                <p v-if="responseMessage">{{ responseMessage }} <i class="fa-solid fa-circle-check"></i></p>
            </div>
            <div v-if="showModal" class="modal">
                <div class="modal-content">
                    <p>¿Está seguro que quiere borrar el crédito con ID {{ formData.id }}?</p>
                    <button @click="confirmDelete" class="btn btn-success">Sí</button>
                    <button @click="closeModal" class="btn btn-danger">No</button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.modal {
    font-size: var(--fontsize);
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
}

.modal-content {
    background-color: white;
    padding: 20px;
    border-radius: 5px;
    text-align: center;
}

.btn {
    font-size: var(--fontsize);
    margin: 10px;
    padding: 10px 20px;
    cursor: pointer;
}

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

.linea2 input {
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
}
</style>