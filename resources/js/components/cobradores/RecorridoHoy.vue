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

interface RecorridoItem {
    id: string | number;
    nombre: string;
    cuotas_restantes: string;
    cuotas_reales: string;
    cuotas_totales: string;
    tipo_credito: string;
    direccion: string;
    total_creditos: string | number;
    telefonos: string;
    tiposdecomercio: string;
    valorescuotas: string;
    saldosreales: string;
}

// Recorrido
const selectedRecorrido = ref<string | null>('');
const RecorridoData = ref<RecorridoItem[]>([]);
const elementos = ref<number>(0);

const groupedData = ref<RecorridoItem[][]>([]);
const rowsPerColumn = 30;

const recorridoid = ref<string | null>('');
const Total = ref<number>(0);


const fetchRecorridoInfo = async (recorrido: string | null): Promise<void> => {
    if (selectedRecorrido.value) {

        try {
            const response = await fetch(`/recorrido/${selectedRecorrido.value}`);
            if (response.ok) {
                const data = await response.json();
                elementos.value = data.elementos;

                // Asignación de arrays en un formato adecuado
                RecorridoData.value = data.ids.map((id: string | number, index: number) => ({
                    id: id,
                    nombre: data.nombres[index],
                    cuotas_restantes: (data.cuotas_totales[index] - data.cuotas_restantes[index]) + 1,
                    cuotas_reales: data.cuotas_reales[index],
                    cuotas_totales: data.cuotas_totales[index],
                    direccion: data.direcciones[index],
                    total_creditos: data.totales_creditos[index],
                    tipo_credito: data.tipo_credito[index],
                    telefonos: data.telefonos[index],
                    tiposdecomercio: data.tiposdecomercio[index],
                    valorescuotas: data.valorescuotas[index],
                    saldosreales: data.saldosreales[index]
                }));

                Total.value = RecorridoData.value.reduce(
                    (acc, item) => acc + Number(item.valorescuotas), // Sumar los créditos
                    0
                );

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

watch(selectedRecorrido, (newVal) => {
    if (newVal !== null) {
        recorridoid.value = newVal;
        fetchRecorridoInfo(newVal);
    }
});


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

const recorridos = ref<{ id: number; name: string }[]>([]);

onMounted(async () => {
    try {
        const response = await axios.get('/setup/recorridos'); // Ajusta la URL según sea necesario
        recorridos.value = response.data;

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
    return `Recorrido ${recorridoid.value}`;
});

const reportTitle = 'Fichero de cobro';
const currentDate = new Date().toLocaleDateString();

</script>


<template>
    <div class="content">
        <button @click="printPage">Imprimir</button>

        <select v-model="selectedRecorrido" @change="fetchRecorridoInfo(selectedRecorrido)" required>
            <option v-for="recorrido in recorridos" :key="recorrido.id" :value="recorrido.id">
                {{ recorrido.name }}
            </option>
        </select>

        <div id="print-section">
            <div class="box">
                <div class="header">
                    <div class="encabezado">
                        <p>{{ currentDate }} - {{ companyName }} - {{ reportTitle }}</p>

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
                                    <td>{{ item.cuotas_reales !== null ? item.cuotas_reales + '/' + item.cuotas_totales
                                        : 'Mora' }}</td>
                                    <td>{{ item.tipo_credito }}</td>
                                    <td>{{ item.direccion }}</td>
                                    <td>{{ item.telefonos }}</td>
                                    <td>{{ item.tiposdecomercio }}</td>
                                    <td>${{ item.valorescuotas }}</td>
                                    <td></td>
                                    <td>{{ item.saldosreales }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="total_page">
                    <p>Total a cobrar: ${{ Total }}</p>
                    <p>Cobrado:______________</p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

.total_page p{
    border: 1px solid gray;
    margin-right: 1rem;
}

.total_page {
    display: flex;
    justify-content: center;
    font-size: 2rem;
}

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