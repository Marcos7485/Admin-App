<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import { useImageStore } from '../../../store/imageStore.ts';
import { useSelectedIdStore } from '../../../store/selectedIdStore.ts'

const imageStore = useImageStore();
onMounted(() => {
    imageStore.fetchImagePath();
});

const selectedIdStore = useSelectedIdStore()
const selectedId = selectedIdStore.selectedId

interface CreditoData {
    id: string | number;
    cliente: number | string;
    credito: string;
    total_credito: string;
    cuotas: string | number;
    cuotas_restantes: string | number;
    cuotas_valor: string | number;
    modalidad: string;
    pagado: string;
    pago_restante: string;
    inicio: string;
    lugar_cobro: string;
    interes: string;
    status: string;
}

// Tipar clienteData con la interfaz
const creditoData = ref<CreditoData>({
    id: '',
    cliente: '',
    credito: '',
    total_credito: '',
    cuotas: '',
    cuotas_restantes: '',
    cuotas_valor: '',
    modalidad: '',
    pagado: '',
    pago_restante: '',
    inicio: '',
    lugar_cobro: '',
    interes: '',
    status: ''
});

interface FormData {
    id: string | number;
    cliente: number | string;
    credito: string;
    total_credito: string;
    cuotas: string | number;
    cuotas_restantes: string | number;
    cuotas_valor: string | number;
    modalidad: string;
    pagado: string;
    pago_restante: string;
    inicio: string;
    lugar_cobro: string;
    interes: string;
    status: string;
}

const formData = ref<FormData>({
    id: '',
    cliente: '',
    credito: '',
    total_credito: '',
    cuotas: '',
    cuotas_restantes: '',
    cuotas_valor: '',
    modalidad: '',
    pagado: '',
    pago_restante: '',
    inicio: '',
    lugar_cobro: '',
    interes: '',
    status: ''
});

// Función para cargar datos del cliente
const loadCreditoData = async () => {
    const response = await fetch(`/info/credito/${selectedId}`);
    if (response.ok) {
        const data = await response.json();
        creditoData.value = data;
    }
};

// Cargar datos del cliente al montar el componente
onMounted(() => {
    loadCreditoData();
});

// Actualizar formData cuando creditoData cambia
watch(creditoData, (newData) => {
    formData.value = {
        id: newData.id,
        cliente: newData.cliente,
        credito: newData.credito,
        total_credito: newData.total_credito,
        cuotas: newData.cuotas,
        cuotas_restantes: newData.cuotas_restantes,
        cuotas_valor: newData.cuotas_valor,
        modalidad: newData.modalidad,
        pagado: newData.pagado,
        pago_restante: newData.pago_restante,
        inicio: newData.inicio,
        lugar_cobro: newData.lugar_cobro,
        interes: newData.interes,
        status: newData.status
    };
}, { immediate: true });

const emit = defineEmits(['changeComponent']);

function cancelForm() {
    FormClear();
    emit('changeComponent', 'Creditos');
}

function FormClear() {
    formData.value = {
        id: '',
        cliente: '',
        credito: '',
        total_credito: '',
        cuotas: '',
        cuotas_restantes: '',
        cuotas_valor: '',
        modalidad: '',
        pagado: '',
        pago_restante: '',
        inicio: '',
        lugar_cobro: '',
        interes: '',
        status: ''
    };
}

</script>


<template>
    <div class="content">
        <div class="box">
            <div class="form">
                <div class="cliente">
                    <h1>Ver Credito</h1>
                    <hr>
                    <div class="linea1">
                        <div>
                            <p class="input-chico">
                                <label for="inicio">Cliente {{ formData.cliente }}</label>
                            </p>
                        </div>

                        <div>
                            <p>
                                <label for="inicio">Credito {{ formData.credito }}</label>
                            </p>
                        </div>
                        <div>
                            <p class="input-chico">
                                <label for="interes">Interes {{ formData.interes }}%</label>
                            </p>
                        </div>
                        <div>
                            <p>
                                <label for="inicio">Total (+ interes) {{ formData.total_credito }}</label>
                            </p>
                        </div>
                        <div>
                            <p>
                                <label for="inicio">Modalidad {{ formData.modalidad }}</label>
                            </p>
                        </div>
                    </div>
                    <div class="linea2">
                        <div>
                            <p>
                                <label for="inicio">Cuotas {{ formData.cuotas }}</label>
                            </p>
                        </div>
                        <div>
                            <p>
                                <label for="inicio">Cuotas restantes {{ formData.cuotas_restantes }}</label>
                            </p>
                        </div>
                        <div>
                            <p>
                                <label for="inicio">Cuotas valor {{ formData.cuotas_valor }}</label>
                            </p>
                        </div>
                        <div>
                            <p>
                                <label for="inicio">Inicio {{ formData.inicio }}</label>
                            </p>
                        </div>
                        <div>
                            <p>
                                <label for="inicio">Cobrar en {{ formData.lugar_cobro }}</label>
                            </p>
                        </div>
                        <div>
                            <p>
                                <label for="inicio">Pagado {{ formData.pagado }}</label>
                            </p>
                        </div>
                        <div>
                            <p>
                                <label for="inicio">Pago restante {{ formData.pago_restante }}</label>
                            </p>
                        </div>
                        <div>
                            <p>
                                <label for="inicio">Estado: {{ formData.status }}</label>
                            </p>
                        </div>

                    </div>
                </div>

                <div class="linea3">
                    <div>
                        <button class="btn btn-info" @click="cancelForm">Volver</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.cliente {
    display: flex;
    flex-direction: column;
    margin-top: 1rem;
}

.comercio {
    display: flex;
    flex-direction: column;
    margin-top: 5rem;
}

.linea3 button {
    font-size: var(--fontsize);
}

.linea3 div {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    padding: 1rem;
    flex: 1;
}

.linea2 select {
    font-size: var(--fontsize);
    width: 15rem;
}

.linea2 label {
    font-size: var(--fontsize);
    margin-right: 1rem;
}

.input-chico input {
    width: 10rem;
}

.linea2 input {
    font-size: var(--fontsize);
}

.linea2 div {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    padding: 1rem;
    flex: 1;
}

.linea1 label {
    font-size: var(--fontsize);
    margin-right: 1rem;
}

.linea1 select {
    font-size: var(--fontsize);
    width: 25rem;
}

.linea1 input {
    font-size: var(--fontsize);
}

.linea1 div {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    padding: 1rem;
    flex: 1;
}

.form p {
    font-size: 1.3rem;
    color: green;
}

.form button {
    border-radius: 1rem 2rem;
    width: 25rem;
    height: 5rem;
    padding: 1rem;
    color: black;
}

.form select {
    background-color: white;
    border-radius: 1rem 2rem;
    padding: 1rem;
    color: black;
}

.form input {
    background-color: white;
    border-radius: 1rem 2rem;
    padding: 1rem;
    color: black;
}

.form div {
    display: flex;
    justify-content: center;
    gap: 0rem;
    font-size: var(--fontsize);
}

h1 {
    font-size: var(--fontsizeTitles);
}

.box {
    border: solid .2rem grey;
    padding: 2rem;
    border-radius: 4rem;
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

    .form div {
        flex-direction: column;
        align-items: center;
    }
}
</style>