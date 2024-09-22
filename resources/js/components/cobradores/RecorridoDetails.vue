<script setup lang="ts">
import { ref, watch, onMounted, computed } from 'vue';
import { useImageStore } from '../../../store/imageStore.ts';
import { useSelectedIdStore } from '../../../store/selectedIdStore.ts' // Ajusta la ruta según tu estructura de carpetas
const selectedIdStore = useSelectedIdStore();
const selectedId = selectedIdStore.selectedId

const imageStore = useImageStore();

onMounted(() => {
    imageStore.fetchImagePath();
    fetchRecorridoInfo(selectedId);
});


interface RecorridoItem {
    id: string | number;
    elementos: string;
    ids: string;
    nombre: string;
    cuota: string;
    tipo: string;
    direcciones: string;
    telefonos: string | number;
    comercio: string;
    cuota_valor: string;
    totales_creditos: string | number;
    saldo: string;
    pago: string;
}

// Recorrido
const selectedRecorrido = ref<string | null>('');
const RecorridoData = ref<RecorridoItem[]>([]);
const elementos = ref<number>(0);

const groupedData = ref<RecorridoItem[][]>([]);
const rowsPerColumn = 30;
const createdAt = ref<string | null>('');
const recorridoid = ref<string | null>('');

const fetchRecorridoInfo = async (recorrido: string | null): Promise<void> => {
    if (recorrido && selectedId) {
        try {
            const response = await fetch(`/info/recorrido/${selectedId}`);
            if (response.ok) {
                const data = await response.json();

                // Actualizar los datos
                elementos.value = data.elementos;
                createdAt.value = formatDate(data.created_at);

                // Asignación de arrays en un formato adecuado
                RecorridoData.value = data.ids.map((id: string | number, index: number) => ({
                    id: id,
                    elementos: data.elementos,
                    ids: data.ids[index],
                    nombre: data.nombres[index],
                    cuota: data.cuota[index],
                    tipo: data.tipo[index],
                    direcciones: data.direcciones[index],
                    telefonos: data.telefonos[index],
                    comercio: data.comercio[index],
                    cuota_valor: data.cuota_valor[index],
                    totales_creditos: data.totales_creditos[index],
                    saldo: data.saldo[index],
                    pago: data.pago[index],
                }));

                // Llamar a la función para dividir los datos en columnas
                splitDataIntoColumns();
            } else {
                console.error('Error al obtener datos.', response.status);
            }
        } catch (error) {
            console.error('Error en la solicitud.', error);
        }
    }
};

function formatDate(dateString: string): string {
    const date = new Date(dateString);
    const day = date.getDate().toString().padStart(2, '0');
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
}

const splitDataIntoColumns = () => {
    // Si no hay elementos, no hace falta procesar
    if (!RecorridoData.value.length) return;

    // Calcula el número de columnas necesarias basado en la cantidad total de elementos
    const numColumns = Math.ceil(RecorridoData.value.length / rowsPerColumn);
    const tempArray: RecorridoItem[][] = [];

    // Divide los elementos en columnas
    for (let i = 0; i < numColumns; i++) {
        const start = i * rowsPerColumn;
        const end = start + rowsPerColumn;

        // Dividir el recorridoData en partes para cada columna
        tempArray.push(RecorridoData.value.slice(start, end));
    }

    // Asignar las columnas divididas a groupedData
    groupedData.value = tempArray;
};


const printPage = () => {
    const printContent = document.getElementById('print-section')?.innerHTML;
    const originalContent = document.body.innerHTML;

    if (printContent) {
        document.body.innerHTML = printContent;
        window.print();
        document.body.innerHTML = originalContent;
        location.reload();
    }
};

const companyLogo = `${imageStore.imagePath}/Principal/logomarca.png`;
const companyName = computed(() => {
    return `Recorrido ${recorridoid.value}`;
});

const reportTitle = 'Fichero de cobro';


</script>


<template>
    <div class="content">
        <button @click="printPage">Imprimir</button>

        <div id="print-section">
            <div class="box">
                <div class="header">

                    <div class="imagen">
                        <img :src="companyLogo" alt="Company Logo" class="logo">
                    </div>

                    <div class="encabezado">
                        <h1>{{ companyName }}</h1>
                        <p>{{ reportTitle }}</p>
                        <p>{{ createdAt }}</p>
                    </div>

                </div>
                <div class="data-columns">
                    <div v-for="(column, columnIndex) in groupedData" :key="columnIndex" class="column">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>Cliente</th>
                                    <th>Cuota</th>
                                    <th>Tipo</th>
                                    <th>Direccion</th>
                                    <th>Telefono</th>
                                    <th>Comercio</th>
                                    <th>Valor cuota</th>
                                    <th>Pago</th>
                                    <th>Saldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in column" :key="index">
                                    <td>{{ item.id }}</td>
                                    <td>{{ item.nombre }}</td>
                                    <td>{{ item.cuota.startsWith('/') ? 'Mora' : item.cuota }}</td>
                                    <td>{{ item.tipo }}</td>
                                    <td>{{ item.direcciones }}</td>
                                    <td>{{ item.telefonos }}</td>
                                    <td>{{ item.comercio }}</td>
                                    <td>${{ item.cuota_valor }}</td>
                                    <td>{{ item.pago }}</td>
                                    <td>{{ item.saldo }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.data-columns {
    display: flex;
    flex-wrap: wrap;
}

.column {
    flex: 1;
    min-width: 0;
    max-width: 100%;
    margin: 0 1rem;
    /* Ajusta el margen entre columnas según sea necesario */
    box-sizing: border-box;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th,
.table td {
    border: 1px solid #ddd;
    padding: .8rem;
}

.info-section {
    display: flex;
    flex-direction: column;
    text-align: left;
}

.info-fichero {
    display: flex;
    justify-content: space-between;
    width: 100%;
    padding: 10px;
}

.header p {
    font-size: var(--fontsize);
}

.logo {
    width: 10rem;
    background-color: var(--color-base);
    border-radius: 10%;
}

.header {
    border: solid 2px grey;
    padding: 2rem;
    height: 100%;
    border-radius: 4rem;
    font-size: 1.4rem;
}

.box {
    border: solid 2px grey;
    padding: 2rem;
    width: 100%;
    border-radius: 4rem;
    font-size: 1.4rem;
}

.content button {
    font-size: 1.2rem;
}

.content select {
    font-size: var(--fontsize);
}

.content {
    opacity: 0;
    padding: 3rem;
    animation: appear 1s forwards;
    /* background-color: var(--color-base); */
}

@keyframes appear {
    100% {
        opacity: 1;
    }
}

@media print {
    body {
        margin: 0;
        padding: 0;
        text-align: center;
    }

    .table th,
    .table td {
        font-size: 9px;
    }

    .info-fichero p {
        font-size: 16px;
    }

    .header,
    .dataColumnas,
    .data-columns {
        width: 100%;
        margin: 0 auto;
    }

    .info-section,
    .column {
        flex: 1;
        text-align: center;
        /* Centra el contenido de cada sección */
    }

    .table {
        margin: 0 auto;
        width: 100%;
    }

    .content {
        width: 100%;
        margin: 0;
        padding: 0;
    }

    .logo {
        position: absolute;
        top: 10px;
        left: 10px;
        width: 120px;
    }
}

@media (max-width: 600px) {

    .content {
        margin-top: 15rem;
    }
}
</style>