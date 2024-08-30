<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useImageStore } from '../../../store/imageStore.ts';
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net';
import { useSelectedIdStore } from '../../../store/selectedIdStore.ts' // Ajusta la ruta según tu estructura de carpetas

const selectedIdStore = useSelectedIdStore();

const creditosData = ref([]);

onMounted(async () => {
    const response = await fetch('/datos-creditos');
    if (response.ok) {
        const data = await response.json();
        // Verifica la estructura de los datos
        creditosData.value = data.map(credito => ({
            id: credito.id,
            cliente: credito.cliente,
            credito: credito.credito,
            interes: credito.interes,
            total_credito: credito.total_credito,
            cuotas: credito.cuotas,
            cuotas_restantes: credito.cuotas_restantes,
            cuotas_valor: credito.cuotas_valor,
            modalidad: credito.modalidad,
            pagado: credito.pagado,
            inicio: credito.inicio,
            estado: credito.status
        }));
    }
});

DataTable.use(DataTablesCore);
const imageStore = useImageStore();
onMounted(() => {
    imageStore.fetchImagePath();
});


const columns = [{ data: "id" }, { data: "cliente" }, { data: "credito" },
{ data: "interes" }, { data: "total_credito" }, { data: "cuotas" }, { data: "cuotas_restantes" }, { data: "cuotas_valor" } , { data: "modalidad" },
{ data: "pagado" }, { data: "inicio" }, { data: "estado" },
{
    data: null,
    render: function (data, type, row) {
        return `<button class="btn btn-info edit-btn" data-id="${row.id}">Editar</button>`;
    },
    orderable: false,
}];


const options = {
    info: false,
    pageLength: 10,
    lengthChange: false,
    language: {
        search: "Buscar:",  // Cambia el texto de la caja de búsqueda
    }
};

const emit = defineEmits(['changeComponent']);

onMounted(() => {
    document.addEventListener('click', function (event) {
        const target = event.target as HTMLElement;
        if (target.classList.contains('edit-btn')) {
            const id = target.getAttribute('data-id');
            if (id) {
                selectedIdStore.setSelectedId(id);
                emit('changeComponent', 'EditarCredito');
            }
        }
    });
});


</script>

<template>
    <div class="content">
        <div class="box">
            <DataTable :data="creditosData" :columns="columns" :options="options"
                class="display table table-primary table-hover table-bordered">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Cliente</th>
                        <th>Credito</th>
                        <th>interes</th>
                        <th>Total</th>
                        <th>Cuotas</th>
                        <th>Restantes</th>
                        <th>Valor Cuota</th>
                        <th>Modalidad</th>
                        <th>Pagado</th>
                        <th>Inicio</th>
                        <th>Estado</th>
                        <th>Editar</th>
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