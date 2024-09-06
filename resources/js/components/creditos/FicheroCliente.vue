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
    cliente: string;
    inicio: string;
    cuotas: number | string;
    cuotas_valor: string;
    valor_otorgado: string;
    valor_final: string;
    modalidad: string;
    lugar_cobro: string;
}

// Ficha
const FicheroData = ref<FicheroItem[]>([]);
const groupedData = ref<FicheroItem[][]>([]); // Array para manejar múltiples columnas
const Fechas = ref<string[]>([]); // Array para manejar múltiples columnas
const rowsPerColumn = 30;
const maxColumns = 4; // Número máximo de columnas
const idFicha = ref(0);
const cuotaCount = ref(0);
const cuotaValor = ref('');
const fechaFirma = ref('');
const idCliente = ref('');
const valor_final = ref('');
const modalidad = ref('');
const credito_valor = ref('');

// Cliente
const nombreCliente = ref('');
const domicilioCliente = ref('');
const localidadDomicilio = ref('');
const phone = ref('');
const comercio_tipo = ref('');
const comercio_address = ref('');
const comercio_localidad = ref('');
const recorrido = ref('');


const fetchClientInfo = async (idCliente: string | null): Promise<void> => {
    if (idCliente !== null) {
        try {
            const response = await fetch(`/info/cliente/${idCliente}`);
            if (response.ok) {
                const clientData = await response.json();
                nombreCliente.value = clientData.name;
                domicilioCliente.value = clientData.address;
                localidadDomicilio.value = clientData.localidad;
                phone.value = clientData.phone;
                comercio_tipo.value = clientData.comercio_tipo;
                recorrido.value = clientData.recorrido;
                comercio_address.value = clientData.comercio_address;
                comercio_localidad.value = clientData.comercio_localidad;

            } else {
                console.error('Error al obtener datos del cliente:', response.status);
            }
        } catch (error) {
            console.error('Error en la solicitud del cliente:', error);
        }
    }
};


const fetchFechasInfo = async (idCliente: string | null): Promise<void> => {
    if (idCliente !== null) {
        try {
            const response = await fetch(`/info/fechasCuotas/${idCliente}`);
            if (response.ok) {
                const FechaData = await response.json();
                Fechas.value = FechaData;
            } else {
                console.error('Error al obtener datos del cliente:', response.status);
            }
        } catch (error) {
            console.error('Error en la solicitud del cliente:', error);
        }
    }
};

onMounted(async () => {
    try {
        const response = await fetch(`/info/fichero/${selectedId}`);
        if (response.ok) {
            const data = await response.json();
            if (data && typeof data === 'object') {
                FicheroData.value = [data]; // Envuelve el objeto en un array

                // Aquí ya no necesitas verificar si `FicheroData.value` es un array
                if (FicheroData.value.length > 0) {
                    const firstItem = FicheroData.value[0];
                    cuotaCount.value = parseInt(firstItem.cuotas.toString(), 10);
                    idFicha.value = parseInt(firstItem.id.toString(), 10);
                    cuotaValor.value = firstItem.cuotas_valor;
                    fechaFirma.value = firstItem.inicio;
                    idCliente.value = firstItem.cliente;
                    valor_final.value = firstItem.valor_final;
                    modalidad.value = firstItem.modalidad;
                    credito_valor.value = firstItem.valor_otorgado;
                }

                splitDataIntoColumns();
                await fetchClientInfo(idCliente.value);
                await fetchFechasInfo(idCliente.value);

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
        id: i,
        cliente: '',
        inicio: fechaFirma.value,
        cuotas: cuotaCount.value.toString(),
        cuotas_valor: cuotaValor.value,
        valor_otorgado: '',
        valor_final: '',
        modalidad: '',
        lugar_cobro: ''
    }));

    // Calcula el número de columnas necesarias basado en la cantidad total de elementos
    const numColumns = Math.ceil(itemsArray.length / rowsPerColumn);
    const tempArray: FicheroItem[][] = [];
    const fechasPorColumna: string[][] = [];

    // Divide los elementos en columnas
    for (let i = 0; i < numColumns; i++) {
        const start = i * rowsPerColumn;
        const end = start + rowsPerColumn;
        tempArray.push(itemsArray.slice(start, end));
        fechasPorColumna.push(Fechas.value.slice(start, end)); // Divide las fechas en columnas
    }

    groupedData.value = tempArray;
    // Si el número de fechas no coincide con el número total de filas, puedes manejar eso aquí
    Fechas.value = fechasPorColumna.flat(); // Reasigna las fechas para que correspondan a cada columna
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

const companyLogo = `${imageStore.imagePath}/Principal/logomarca.png`;
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
                            <p>{{ currentDate }}</p>
                            <p>Nº Ficha: {{ idFicha }}</p>
                            <p>Recorrido: {{ recorrido }}</p>
                            <p>Modalidad: {{ modalidad }}</p>
                        </div>
                        <div class="info-section">
                            <p>Cliente: {{ nombreCliente }}</p>
                            <p>Domicilio: {{ domicilioCliente }}</p>
                            <p>Zona: {{ localidadDomicilio }}</p>
                            <p>Telefono: {{ phone }}</p>
                        </div>
                        <div class="info-section">
                            <p>Rubro: {{ comercio_tipo }}</p>
                            <p>Direccion: {{ comercio_address }}</p>
                            <p>Zona: {{ comercio_localidad }}</p>
                        </div>
                        <div class="info-section">
                            <p>Credito: ${{ credito_valor }}</p>
                            <p>Cuotas: {{ cuotaCount }}</p>
                            <p>Valor: ${{ cuotaValor }}</p>
                            <p>Total: ${{ valor_final }}</p>
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
                                    <td>{{ Fechas[rowIndex + columnIndex * rowsPerColumn] }}</td>
                                    <td></td>
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
        width: 120px;
    }
}
</style>