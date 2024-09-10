<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useImageStore } from '../../../store/imageStore.ts';
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net';
import dayjs from 'dayjs';
import { useSelectedIdStore } from '../../../store/selectedIdStore.ts' // Ajusta la ruta según tu estructura de carpetas
import $ from 'jquery';
import pdfmake from 'pdfmake';
import pdfFonts from 'pdfmake/build/vfs_fonts';
import 'datatables.net-buttons-dt';
import 'datatables.net-buttons/js/buttons.colVis.mjs';
import ButtonsHtml5 from 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-buttons/js/buttons.print.mjs';
DataTable.use(DataTablesCore);
DataTable.use(pdfmake);
DataTable.use(ButtonsHtml5);
const selectedIdStore = useSelectedIdStore();

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


const imageStore = useImageStore();
onMounted(() => {
    imageStore.fetchImagePath();
});


const columns = [{ data: "id" }, { data: "name" }, { data: "dni" },
{ data: "phone" }, { data: "address" }, { data: "localidad" }, { data: "comercio_address" },
{ data: "comercio_localidad" }, { data: "comercio_tipo" }, { data: "recorrido" }, { data: "created_at" },
{
    data: null,
    render: function (data, type, row) {
        return `<button class="btn btn-info edit-btn" data-id="${row.id}">Editar</button>`;
    },
    orderable: false,
}];

const columnsCel = [{ data: "id" }, { data: "name" }, { data: "dni" },
{
    data: null,
    render: function (data, type, row) {
        return `<button class="btn btn-info ver-btn" data-id="${row.id}">Ver</button>`;
    },
    orderable: false,
},
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
                emit('changeComponent', 'EditarCliente');
            }
        }

        if (target.classList.contains('ver-btn')) {
            const id = target.getAttribute('data-id');
            if (id) {
                selectedIdStore.setSelectedId(id);
                emit('changeComponent', 'VerCliente');
            }
        }

    });
});





</script>

<template>
    <div class="content">
        <div class="box">
            <h1>Clientes</h1><br>
            <DataTable :data="clientesData" :columns="columns" :options="options"
                class="data-table display table table-primary table-hover table-bordered">
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

        <div class="box-cel">
            <h1>Clientes</h1><br>
            <DataTable :data="clientesData" :columns="columnsCel" :options="options"
                class="data-table display table table-primary table-hover table-bordered">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Cliente</th>
                        <th>DNI</th>
                        <th>Detalles</th>
                        <th>Editar</th>
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
    padding: 1rem;
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
        padding: 1rem;
        border-radius: 4rem;
        font-size: 1.4rem;
    }
}
</style>