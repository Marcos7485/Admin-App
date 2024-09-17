<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useImageStore } from '../../../store/imageStore.ts';
import axios from 'axios';

const imageStore = useImageStore();
onMounted(() => {
    imageStore.fetchImagePath();
});

const daysOfWeek = ref(['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']);
const selectedDays = ref<number[]>([]);
const recorridos = ref<string | number>('');
const vendedores = ref<string | number>('');
const cobradores = ref<string | number>('');
const isDisabled = ref<boolean>(false);
const Message = ref<string>('');


const submitForm = async () => {
    const formData = {
        daysOfWeek: selectedDays.value,
        recorridos: recorridos.value,
        vendedores: vendedores.value,
        cobradores: cobradores.value
    };

    isDisabled.value = true;

    try {
        const response = await axios.post('/setup/update', formData);
        // Maneja la respuesta exitosa
        Message.value = response.data.message;
    } catch (error) {
        if (error.response && error.response.data) {
            // Maneja los errores de la solicitud
            Message.value = error.response.data.message || 'Error en la configuración';
        } else {
            Message.value = 'Error en la configuración';
        }
    } finally {
        isDisabled.value = false;
    }
};


</script>

<template>
    <div class="content">
        <div class="box">
            <div class="form">

                <div class="cliente">
                    <h1>Aplicacion</h1>
                    <hr>
                    <form @submit.prevent="submitForm()">
                        <div class="linea1">
                            <div>
                                <label for="">Dias inoperantes:</label>
                                <label v-for="(day, index) in daysOfWeek" :key="index">
                                    <input type="checkbox" :value="index" v-model="selectedDays" />
                                    {{ day }}
                                </label>
                            </div>
                            <div>
                                <label for="recorridos">Recorridos</label>
                                <input type="number" v-model="recorridos" placeholder="Recorridos" required>
                            </div>
                            <div>
                                <label for="vendedores">Vendedores</label>
                                <input type="number" v-model="vendedores" placeholder="Vendedores" required>
                            </div>
                            <!-- <div>
                                <label for="password">Cobradores</label>
                                <input type="number" v-model="cobradores" placeholder="Cobradores" required>
                            </div> -->
                        </div>
                        <div>
                            <button class="btn btn-info" type="submit" :disabled="isDisabled">Guardar</button>
                        </div>
                    </form>

                    <p v-if="Message">{{ Message }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.cliente label {
    display: flex;
    flex-direction: row;
}

.cliente {
    display: flex;
    flex-direction: column;
    margin-top: 5rem;
}

.linea1 div {
    display: flex;
    flex-direction: column;
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
    font-size: var(--fontsize);
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
    width: 100%;
    justify-content: center;
    align-items: center;
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