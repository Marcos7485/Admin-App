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

interface Resumen {
    id: number,
    creditos: string;
    renovados: string | number;
    dinerocancelado: string;
    refinanciados: string;
    pagos_totales: string;
}

// Recorrido
const ResumenData = ref<Resumen[]>([]);

const groupedData = ref<Resumen[][]>([]);
const rowsPerColumn = 30;


const selectedMonth = ref<number | null>(null);
const fetchFechaInfo = async (fecha: string | null): Promise<void> => {
    if (fecha) {
        try {
            const response = await fetch(`/mensual/${fecha}`);
            if (response.ok) {
                const data = await response.json();

                ResumenData.value = [{
                    id: 1, // Aquí podrías ajustar la lógica para asignar el ID si es necesario
                    creditos: data.creditos_otorgados,
                    renovados: data.creditos_renovados,
                    dinerocancelado: data.dinero_renovaciones,
                    refinanciados: data.creditos_refinanciados,
                    pagos_totales: data.pagos_totales,
                }];

                splitDataIntoColumns();  // Llama a la función para dividir los datos en columnas

            } else {
                console.error('Error al obtener datos del resumen mensual.', response.status);
            }
        } catch (error) {
            console.error('Error en la solicitud al servidor.', error);
        }
    }
};


watch(selectedMonth, (newMonth) => {
    if (newMonth !== null) {
        monthString.value = newMonth.toString();
        fetchFechaInfo(monthString.value);
    }
});

const monthString = ref<string>('');
const datas = ref<{ id: number; name: string }[]>([]);

onMounted(async () => {
    try {
        const response = await axios.get('/resumen/datas'); // Ajusta la URL según sea necesario
        datas.value = response.data;

    } catch (error) {
        console.error('Error al obtener los recorridos:', error);
    }
});


const splitDataIntoColumns = () => {
    if (!ResumenData.value.length) return;

    const numColumns = Math.ceil(ResumenData.value.length / rowsPerColumn);
    const tempArray: Resumen[][] = [];

    for (let i = 0; i < numColumns; i++) {
        const start = i * rowsPerColumn;
        const end = start + rowsPerColumn;

        tempArray.push(ResumenData.value.slice(start, end));
    }

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
    return `Resumen Mensual ${monthString.value}`;
});

const currentDate = new Date().toLocaleDateString();

</script>


<template>
    <div class="content">
        <button @click="printPage">Imprimir</button>

        <select v-model="selectedMonth" required>
            <option v-for="data in datas" :key="data.id" :value="data">
                {{ data }}
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
                        <p>{{ currentDate }}</p>
                    </div>

                </div>
                <div class="data-columns">
                    <div v-for="(column, columnIndex) in groupedData" :key="columnIndex" class="column">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Creditos otorgados</th>
                                    <th>Renovados</th>
                                    <th>Cancelado por renovaciones</th>
                                    <th>Refinanciados</th>
                                    <th>Pagos Totales</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Renderizamos cada crédito recibido desde el backend -->
                                <tr v-for="item in ResumenData" :key="item.id">
                                    <td>{{ item.creditos }}</td>
                                    <td>{{ item.renovados }}</td>
                                    <td>{{ item.dinerocancelado }}</td>
                                    <td>{{ item.refinanciados }}</td>
                                    <td>{{ item.pagos_totales }}</td>
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
    height: 100%;
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
</style>