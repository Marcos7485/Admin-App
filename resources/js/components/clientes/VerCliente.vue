<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import { useImageStore } from '../../../store/imageStore.ts';
import { useSelectedIdStore } from '../../../store/selectedIdStore.ts'
const selectedIdStore = useSelectedIdStore()
const selectedId = selectedIdStore.selectedId


const imageStore = useImageStore();
onMounted(() => {
    imageStore.fetchImagePath();
});


// Definir la interfaz
interface ClienteData {
    id: string | number;
    name: string;
    dni: string;
    phone: string;
    address: string;
    localidad: string;
    comercio_address: string;
    comercio_localidad: string;
    comercio_tipo: string;
    recorrido: string;
    prenda: string;
}

// Tipar clienteData con la interfaz
const clienteData = ref<ClienteData>({
    id: '',
    name: '',
    dni: '',
    phone: '',
    address: '',
    localidad: '',
    comercio_address: '',
    comercio_localidad: '',
    comercio_tipo: '',
    recorrido: '',
    prenda: '',
});

const formData = ref<FormData>({
    id: '',
    name: '',
    dni: '',
    phone: '',
    address: '',
    localidad: '',
    comercio_address: '',
    comercio_localidad: '',
    comercio_tipo: '',
    recorrido: '',
    prenda: '',
});

// Función para cargar datos del cliente
const loadClienteData = async () => {
    const response = await fetch(`/info/cliente/${selectedId}`);
    if (response.ok) {
        const data = await response.json();
        clienteData.value = data;
    }
};

// Cargar datos del cliente al montar el componente
onMounted(() => {
    loadClienteData();
});

// Actualizar formData cuando clienteData cambia
watch(clienteData, (newData) => {
    formData.value = {
        id: newData.id,
        name: newData.name,
        dni: newData.dni,
        phone: newData.phone,
        address: newData.address,
        localidad: newData.localidad,
        comercio_address: newData.comercio_address,
        comercio_localidad: newData.comercio_localidad,
        comercio_tipo: newData.comercio_tipo,
        recorrido: newData.recorrido,
        prenda: newData.prenda,
    };
}, { immediate: true });

interface FormData {
    id: string | number;
    name: string;
    dni: string | number;
    phone: string | number;
    address: string;
    localidad: string;
    comercio_address: string;
    comercio_localidad: string;
    comercio_tipo: string;
    recorrido: string;
    prenda: string;
}


const emit = defineEmits(['changeComponent']);

function cancelForm() {
    emit('changeComponent', 'BuscarCliente');
}

</script>


<template>
    <div class="content">
        <div class="box">
            <div class="form">
                    <div class="cliente">
                        <h1>Detalles del Cliente</h1>
                        <hr>
                        <div class="linea1">
                            <div>
                                <label for="">{{ formData.name }}</label>
                            </div>
                            <div>
                                <label for="">{{ formData.dni }}</label>
                            </div>
                            <div>
                                <label for="">{{ formData.phone }}</label>
                            </div>
                            <div>
                                <label for="">{{ formData.address }}</label>
                            </div>
                        </div>
                        <div class="linea2">
                            <div>
                                <label for="">{{ formData.localidad }}</label>
                            </div>
                            <div>
                                <label for="">Articulo de prenda: {{ formData.prenda }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="comercio">
                        <h1>Comercio</h1>
                        <hr>
                        <div class="linea3">
                            <div>
                                <label for="">{{ formData.comercio_address }}</label>
                            </div>
                            <div>
                                <label for="">{{ formData.comercio_localidad }}</label>
                            </div>
                            <div>
                                <label for="">{{ formData.comercio_tipo }}</label>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="linea4">
                        <div>
                            <label for="recorrido">Recorrido {{ formData.recorrido }}</label>
                        </div>
                    </div>
                    <div class="linea5">
                        <div>
                            <button class="btn btn-info" @click="cancelForm()">Volver</button>
                        </div>
                    </div>
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

.linea5 button {
    font-size: var(--fontsize);
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