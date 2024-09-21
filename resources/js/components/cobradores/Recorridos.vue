<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useImageStore } from '../../../store/imageStore.ts';
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net';
import { useSelectedIdStore } from '../../../store/selectedIdStore.ts';
import pdfmake from 'pdfmake';
import $ from 'jquery';
import 'datatables.net-buttons-dt';
import 'datatables.net-buttons/js/buttons.colVis.mjs';
import ButtonsHtml5 from 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-buttons/js/buttons.print.mjs';
DataTable.use(DataTablesCore);
DataTable.use(pdfmake);
DataTable.use(ButtonsHtml5);

const selectedIdStore = useSelectedIdStore();

interface Recorrido {
    id: number;
    elementos: number;
    ids: number[];
    nombres: string[];
    direcciones: string[];
    totales_creditos: string[];
    recorrido: string;
    created_at: string;
    updated_at: string;
}

const RecorridoData = ref<Recorrido[]>([]);

function formatDate(dateString: string): string {
    const date = new Date(dateString);
    const day = date.getDate().toString().padStart(2, '0');
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
}

onMounted(async () => {
    try {
        const response = await fetch('/datos-recorridos');
        if (response.ok) {
            const data: Recorrido[] = await response.json();

            // Mapear los datos para asegurar su estructura
            RecorridoData.value = data.map((recorrido: Recorrido) => ({
                ...recorrido, // Usa los datos como vienen del servidor
                ids: recorrido.ids || [], // Asegura que estos sean arrays
                nombres: recorrido.nombres || [],
                direcciones: recorrido.direcciones || [],
                totales_creditos: recorrido.totales_creditos || [],
                elementos: recorrido.elementos || 0,
                id: recorrido.id,
                recorrido: recorrido.recorrido || '',
                created_at: formatDate(recorrido.created_at),
            }));
        } else {
            console.error('Error al obtener los datos');
        }
    } catch (error) {
        console.error('Error en la solicitud:', error);
    }
});


DataTable.use(DataTablesCore);
const imageStore = useImageStore();
onMounted(() => {
    imageStore.fetchImagePath();
});


const columns = [{ data: "id" }, { data: "elementos" }, { data: "ids" }, { data: "recorrido" }, { data: "created_at" },
{
    data: null,
    render: function (data, type, row) {
        return `<button class="btn btn-info edit-btn" data-id="${row.id}">Ver</button>`;
    },
    orderable: false,
}];

const columnsCel = [{ data: "id" }, { data: "elementos" }, { data: "ids" }, { data: "recorrido" }, { data: "created_at" },
{
    data: null,
    render: function (data, type, row) {
        return `<button class="btn btn-info edit-btn" data-id="${row.id}">Ver</button>`;
    },
    orderable: false,
}];


const options = {
    info: false,
    pageLength: 10,
    lengthChange: false,
    language: {
        search: "Buscar:",  // Cambia el texto de la caja de búsqueda
    },
    dom: "Bfrtip",
    responsive: true,
    autoWidth: false,
    buttons: [
        {
            extend: 'print',
            title: '', // Aquí defines el título que aparecerá
            customize: function (win) {
                $(win.document.body)
                    .css({
                        'font-size': '10pt', // Ajuste del tamaño de la fuente
                        'text-align': 'center' // Centra el contenido
                    })
                    .prepend(
                        '<div style="position:absolute; top: 0; width:10%;left:45%;">' +
                        '<img src="/images/Logo.png" style="width: 150px; margin-bottom: 20px;" />' +
                        '</div>'
                    );

                $(win.document.body).find('table')
                    .css({
                        'margin-top': '20px', // Añade margen superior para separar la tabla de la imagen
                        'width': '100%', // Asegura que la tabla ocupe el 100% del ancho disponible
                        'text-align': 'center' // Centra el contenido de la tabla
                    });
            },
            exportOptions: {
                // Configura las columnas que quieres exportar, etc.
            }
        },
    ],

};

const emit = defineEmits(['changeComponent']);

onMounted(() => {
    document.addEventListener('click', function (event) {
        const target = event.target as HTMLElement;
        if (target.classList.contains('edit-btn')) {
            const id = target.getAttribute('data-id');
            if (id) {
                selectedIdStore.setSelectedId(id);
                emit('changeComponent', 'RecorridoDetails');
            }
        }
    });
});


</script>

<template>
    <div class="content">
        <div class="box">
            <h1>Recorridos</h1><br>
            <DataTable :data="RecorridoData" :columns="columns" :options="options"
                class="display table table-primary table-hover table-bordered">
                <thead>
                    <tr>
                        <th>id</th>
                        <th>Clientes</th>
                        <th>Ids</th>
                        <th>Recorrido</th>
                        <th>Fecha</th>
                        <th>Detalles</th>
                    </tr>
                </thead>
            </DataTable>
        </div>
        <div class="box-cel">
            <h1>Recorridos</h1><br>
            <DataTable :data="RecorridoData" :columns="columnsCel" :options="options"
                class="display table table-primary table-hover table-bordered">
                <thead>
                    <tr>
                        <th>id</th>
                        <th>Clientes</th>
                        <th>Ids</th>
                        <th>Recorrido</th>
                        <th>Fecha</th>
                        <th>Detalles</th>
                    </tr>
                </thead>
            </DataTable>
        </div>
    </div>
</template>

<style scoped>
.box-cel {
    display: none;
}

.box {
    border: solid 2px grey;
    padding: 2rem;
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

@media (max-width: 600px) {

    .content {
        margin-top: 15rem;
    }

    .box {
        display: none;
    }

    .box-cel {
        display: block;
        border: solid 2px grey;
        padding: 2rem;
        border-radius: 4rem;
        font-size: 1.4rem;
    }
}
</style>