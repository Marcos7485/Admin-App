<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useImageStore } from '../../../store/imageStore.ts';
import { useSelectedIdStore } from '../../../store/selectedIdStore.ts' // Ajusta la ruta según tu estructura de carpetas

const imageStore = useImageStore();
onMounted(() => {
    imageStore.fetchImagePath();
});

const selectedIdStore = useSelectedIdStore();
const selectedId = selectedIdStore.selectedId

interface FicheroItem {
    id: number;
    cliente: number | string;
    interes: string;
    total_credito: string;
    cuotas: number | string; // Ajustado a número o cadena
    modalidad: string;
    cuotas_valor: string;
    lugar_cobro: string;
    inicio: string;
}

const FicheroData = ref<FicheroItem[]>([]);
const groupedData = ref<FicheroItem[][]>([]); // Array para manejar múltiples columnas
const rowsPerColumn = 30;
const maxColumns = 4; // Número máximo de columnas
const cuotaCount = ref(0);
const cuotaValor = ref('');
const fechaFirma = ref('');

onMounted(async () => {
    try {
        const response = await fetch(`/info/fichero/${selectedId}`);
        if (response.ok) {
            const data = await response.json();
            console.log('Datos recibidos:', data);

            // Verifica si `data` es un objeto y ajusta el código en consecuencia
            if (data && typeof data === 'object') {
                FicheroData.value = [data]; // Envuelve el objeto en un array

                // Aquí ya no necesitas verificar si `FicheroData.value` es un array
                if (FicheroData.value.length > 0) {
                    const firstItem = FicheroData.value[0];
                    cuotaCount.value = parseInt(firstItem.cuotas.toString(), 10);
                    cuotaValor.value = firstItem.cuotas_valor;
                    fechaFirma.value = firstItem.inicio;
                }

                splitDataIntoColumns();
            } else {
                console.error('Los datos recibidos no son un objeto.');
            }
        } else {
            console.error('Error al obtener datos:', response.status);
        }
    } catch (error) {
        console.error('Error en la solicitud:', error);
    }
});

const splitDataIntoColumns = () => {
    // Genera una lista de elementos basada en el número de cuotas
    const itemsArray: FicheroItem[] = Array.from({ length: cuotaCount.value }, (_, i) => ({
        id: i, // Puedes generar un ID único si es necesario
        cliente: '', // Ajusta este valor según sea necesario
        interes: '', // Ajusta este valor según sea necesario
        total_credito: '', // Ajusta este valor según sea necesario
        cuotas: cuotaCount.value.toString(),
        modalidad: '', // Ajusta este valor según sea necesario
        cuotas_valor: cuotaValor.value,
        lugar_cobro: '', // Ajusta este valor según sea necesario
        inicio: fechaFirma.value
    }));

    // Calcula el número de columnas necesarias
    const numColumns = Math.min(Math.ceil(itemsArray.length / rowsPerColumn), maxColumns);
    const tempArray: FicheroItem[][] = [];

    // Divide los elementos en columnas
    for (let i = 0; i < numColumns; i++) {
        const start = i * rowsPerColumn;
        const end = start + rowsPerColumn;
        tempArray.push(itemsArray.slice(start, end));
    }

    groupedData.value = tempArray;
};

const emit = defineEmits(['changeComponent']);

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

const companyLogo = `${imageStore.imagePath}/Logo.png`;
const companyName = 'Lemax Finanzas';
const reportTitle = 'Fichero de pago';
const currentDate = new Date().toLocaleDateString();

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
                    </div>
                    <div class="info-fichero">
                        <div class="info-section">
                            <p>Fecha: {{ currentDate }}</p>
                            <p>Nº Ficha: 1</p>
                            <p>Recorrido: 1</p>
                            <p>Rubro: Heladeria</p>
                        </div>
                        <div class="info-section">
                            <p>Cliente: Marcos Gonzalez</p>
                            <p>Domicilio: Arelauquen 255</p>
                            <p>Zona: Merlo</p>
                            <p>Telefono: 1155448575</p>
                        </div>
                        <div class="info-section">
                            <p>Cuotas: 10</p>
                            <p>Valor: $15000</p>
                            <p>Total: $150000</p>
                            <p>Metodologia: Diario</p>
                        </div>
                    </div>
                </div>
                <div class="data-columns">
                    <!-- Iterar sobre las columnas -->
                    <div v-for="(column, columnIndex) in groupedData" :key="columnIndex" class="column">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Cuota</th>
                                    <th>Firma</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Iterar sobre las filas dentro de cada columna -->
                                <tr v-for="(item, rowIndex) in column" :key="rowIndex">
                                    <td>{{ item.inicio }}</td>
                                    <td>{{ item.cuotas_valor }}</td>
                                    <td></td>
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
    border-radius: 50%;
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

    .encabezado p {
        font-size: 16px;
    }

    .encabezado h1 {
        font-size: 20px;
    }

    .encabezado {
        position: absolute;
        left: 30%;
        top: 5%;
        text-align: left;
        background-color: var(--color-base);
        border-radius: 50%;
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
        width: 140px;
    }
}
</style>