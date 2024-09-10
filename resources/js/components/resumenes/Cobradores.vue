<script setup lang="ts">
import { ref, watch, onMounted, computed } from 'vue';
import { useImageStore } from '../../../store/imageStore.ts';
import { useSelectedIdStore } from '../../../store/selectedIdStore.ts' // Ajusta la ruta según tu estructura de carpetas
import axios from 'axios';

const imageStore = useImageStore();
onMounted(() => {
    imageStore.fetchImagePath();
});

const selectedIdStore = useSelectedIdStore();
const selectedId = selectedIdStore.selectedId

interface CobradorItem {
    id: number;
    fecha: string;
    idPago: string | number;
    cliente: string;
    pago: string;
}

// Recorrido
const selectedCobrador = ref<string | null>('');
const CobradorData = ref<CobradorItem[]>([]);

const groupedData = ref<CobradorItem[][]>([]);
const rowsPerColumn = 30;

const cobradorid = ref<string | null>('');



const fetchCobradorInfo = async (cobrador: string | null): Promise<void> => {
    if (cobrador) {
        try {
            const response = await fetch(`/cobrador/${cobrador}`);
            if (response.ok) {
                const data = await response.json();

                // Asignación de los datos obtenidos a VendedorData
                CobradorData.value = data.map((item: any, index: number) => ({
                    id: index+1,
                    fecha: item.pago_fecha,
                    idPago: item.id,
                    cliente: item.nombre_cliente,
                    pago: item.valor,
                }));

                splitDataIntoColumns();
            } else {
                console.error('Error al obtener datos del vendedor.', response.status);
            }
        } catch (error) {
            console.error('Error en la solicitud al servidor.', error);
        }
    }
};
watch(selectedCobrador, (newVal) => {
    if (newVal !== null) {
        cobradorid.value = newVal;
        fetchCobradorInfo(newVal);
    }
});


const splitDataIntoColumns = () => {
    // Si no hay elementos, no hace falta procesar
    if (!CobradorData.value.length) return;

    // Calcula el número de columnas necesarias basado en la cantidad total de elementos
    const numColumns = Math.ceil(CobradorData.value.length / rowsPerColumn);
    const tempArray: CobradorItem[][] = [];

    // Divide los elementos en columnas
    for (let i = 0; i < numColumns; i++) {
        const start = i * rowsPerColumn;
        const end = start + rowsPerColumn;

        // Dividir el recorridoData en partes para cada columna
        tempArray.push(CobradorData.value.slice(start, end));
    }

    // Asignar las columnas divididas a groupedData
    groupedData.value = tempArray;
};


const cobradores = ref<{ id: number; name: string }[]>([]);

onMounted(async () => {
    try {
        const response = await axios.get('/setup/cobradores'); // Ajusta la URL según sea necesario
        cobradores.value = response.data;

    } catch (error) {
        console.error('Error al obtener los recorridos:', error);
    }
});

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
    return `Cobrador ${cobradorid.value}`;
});

const reportTitle = 'Resumen de Cobrador';
const currentDate = new Date().toLocaleDateString();

</script>


<template>
    <div class="content">
        <button @click="printPage">Imprimir</button>

        <select v-model="selectedCobrador" @change="fetchCobradorInfo(selectedCobrador)" required>
            <option v-for="cobrador in cobradores" :key="cobrador.id" :value="cobrador.id">
                {{ cobrador.name }}
            </option>
        </select>

        <div id="print-section">
            <div class="box">
                <div class="header">

                    <div class="imagen">
                        <img :src="companyLogo" alt="Company Logo" class="logo">
                    </div>

                    <div class="encabezado">
                        <h1>{{ companyName }}</h1>
                        <p>{{ reportTitle }}</p>
                        <p>{{ currentDate }}</p>
                    </div>

                </div>
                <div class="data-columns">
                    <div v-for="(column, columnIndex) in groupedData" :key="columnIndex" class="column">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Fecha</th>
                                    <th>Pago id</th>
                                    <th>Cliente</th>
                                    <th>Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Renderizamos cada crédito recibido desde el backend -->
                                <tr v-for="(item, index) in CobradorData" :key="index">
                                    <td>{{ item.id }}</td>
                                    <td>{{ item.fecha }}</td>
                                    <td>{{ item.idPago }}</td>
                                    <td>{{ item.cliente }}</td>
                                    <td>{{ item.pago }}</td>
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