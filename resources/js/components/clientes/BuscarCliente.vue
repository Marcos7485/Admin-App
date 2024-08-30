<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useImageStore } from '../../../store/imageStore.ts';
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net';
import dayjs from 'dayjs'; 

const clientesData = ref([]);

onMounted(async () => {
    const response = await fetch('/datos-clientes');
    if (response.ok) {
        const data = await response.json();
        // Verifica la estructura de los datos
        console.log(data);
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
const imageStore = useImageStore();
onMounted(() => {
    imageStore.fetchImagePath();
});

const columns = [{data:"id"}, {data:"name"}, {data:"dni"}, 
{data:"phone"}, {data:"address"}, {data:"localidad"}, {data:"comercio_address"},
{data:"comercio_localidad"}, {data:"comercio_tipo"}, {data:"recorrido"}, {data:"created_at"}];


const options = {
    info: false,
    pageLength: 10,
    lengthChange: false,
    language: {
        search: "Buscar:",  // Cambia el texto de la caja de búsqueda
    }
};

</script>

<template>
    <div class="content">
        <div class="box">
            <DataTable  :data="clientesData" :columns="columns" :options="options" class="display table table-primary table-hover table-bordered">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Cliente</th>
                        <th>DNI</th>
                        <th>Telefono</th>
                        <th>Direccion Domicilio</th>
                        <th>Localidad Domicilio</th>
                        <th>Direccion Comercio</th>
                        <th>Localidad Local</th>
                        <th>Comercio</th>
                        <th>Recorrido</th>
                        <th>Fecha de inicio</th>
                    </tr>
                </thead>
            </DataTable>
        </div>
    </div>
</template>

<style scoped>

.box {
    border: solid 2px grey;
    padding: 2rem;
    height: 100%;
    border-radius: 4rem;
    font-size: 1.4rem;
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