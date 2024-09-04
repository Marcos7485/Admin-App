<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net';
import { useImageStore } from '../../../store/imageStore.ts';
import dayjs from 'dayjs';
import { useSelectedIdStore } from '../../../store/selectedIdStore.ts'
const selectedIdStore = useSelectedIdStore()
const selectedId = selectedIdStore.selectedId;

const imageStore = useImageStore();
onMounted(() => {
    imageStore.fetchImagePath();
});

interface FormData {
    cliente: string | number;
    valor: number | string;
    pago_fecha: string;
}

const formData = ref<FormData>({
    cliente: '',
    valor: '',
    pago_fecha: ''
});

const emit = defineEmits(['changeComponent']);
const responseMessage = ref<string | null>(null);
const isDisabled = ref<boolean>(false);


const submitForm = async () => {
    if (isDisabled.value) return;
    isDisabled.value = true;

    try {
        const response = await axios.post('/new/pago', formData.value);
        responseMessage.value = response.data.message;
        RegistrarCliente(formData.value.cliente);
        formData.value = {
            cliente: '',
            valor: '',
            pago_fecha: ''
        };
        emit('changeComponent', 'Pagos');

    } catch (error) {
        console.error('Error enviando formulario', error);
        responseMessage.value = 'Error al procesar el pago, revise la informacion.';
    } finally {
        isDisabled.value = false;
    }
};



// DATATABLE
const clientesData = ref([]);

onMounted(async () => {
    const response = await fetch('/datos-clientes');
    if (response.ok) {
        const data = await response.json();
        // Verifica la estructura de los datos
        clientesData.value = data.map(cliente => ({
            id: cliente.id,
            name: cliente.name,
            dni: cliente.dni,
            phone: cliente.phone,
            address: cliente.address,
            localidad: cliente.localidad,
            comercio_address: cliente.comercio_address,
            comercio_localidad: cliente.comercio_localidad,
            comercio_tipo: cliente.comercio_tipo,
            recorrido: cliente.recorrido,
            created_at: dayjs(cliente.created_at).format('DD-MM-YYYY')
        }));
    }
});

DataTable.use(DataTablesCore);


const columns = [{ data: "id" }, { data: "name" }, { data: "dni" },
{ data: "phone" }, { data: "address" }, { data: "localidad" }, { data: "comercio_address" },
{ data: "comercio_localidad" }, { data: "comercio_tipo" }, { data: "recorrido" }, { data: "created_at" },
{
    data: null,
    render: function (data, type, row) {
        return `<button class="btn btn-info edit-btn" data-id="${row.id}">Seleccionar</button>`;
    },
    orderable: false,
}];

function ModifyCliente(): void {
    formData.value.cliente = '';
}

function RegistrarCliente(id): void {
    selectedIdStore.setSelectedId(id);
}


const options = {
    info: false,
    pageLength: 1,
    lengthChange: false,
    language: {
        search: "Buscar:",  // Cambia el texto de la caja de búsqueda
    }
};

onMounted(() => {
    document.addEventListener('click', function (event) {
        const target = event.target as HTMLElement;
        if (target.classList.contains('edit-btn')) {
            const id = target.getAttribute('data-id');
            if (id) {
                formData.value.cliente = id;
                selectedIdStore.setSelectedId(id);
            }
        }
    });
});


</script>

<template>
    <div class="content">
        <div class="box">
            <div class="form">

                <div class="cliente">
                    <h1>Vincular Cliente</h1>
                    <hr>
                    <div v-if="!formData.cliente" class="linea1">
                        <DataTable :data="clientesData" :columns="columns" :options="options"
                            class="display table table-primary table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Cliente</th>
                                    <th>DNI</th>
                                    <th>Telefono</th>
                                    <th>Domicilio</th>
                                    <th>Localidad</th>
                                    <th>Direccion Comercio</th>
                                    <th>Localidad Local</th>
                                    <th>Comercio</th>
                                    <th>Recorrido</th>
                                    <th>Fecha de inicio</th>
                                    <th>Editar</th>
                                </tr>
                            </thead>
                        </DataTable>
                    </div>
                    <div v-if="formData.cliente" class="selected-cliente">
                        <p>Cliente id: {{ formData.cliente }}</p>
                        <button class="btn btn-info" @click="ModifyCliente">Modificar</button>
                    </div>
                </div>
                <form @submit.prevent="submitForm">
                    <div class="credito">
                        <h1>Nuevo Pago</h1>
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
.selected-cliente button {
    border-radius: 1rem 2rem;
    width: 25rem;
    height: 5rem;
    padding: 1rem;
    color: black;
    font-size: var(--fontsize);
}

.selected-cliente {
    align-items: center;
    display: flex;
    flex-direction: column;
    font-size: var(--fontsize);
}

.input-chico input {
    width: 10rem;
}

.cliente {
    display: flex;
    flex-direction: column;
    margin-top: 1rem;
    font-size: 1.2rem;
}

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