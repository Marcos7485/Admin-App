<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useImageStore } from '../../../store/imageStore.ts';

const imageStore = useImageStore();
onMounted(() => {
    imageStore.fetchImagePath();
    //   <img :src="`${imageStore.imagePath}/drs.webp`">
});


interface FormData {
    email: string;
    password: string;
}

const formData = ref<FormData>({
    email: '',
    password: '',
});

const errors = ref({});
const isDisabled = ref(false);



</script>

<template>
    <div class="login-content">
        <div class="card-login">
            <div>
                <img :src="`${imageStore.imagePath}/Logo.png`">
            </div>
            <div class="formulario">
                <form @submit.prevent="">
                    <div>
                        <input v-model="formData.email" type="email" placeholder="Usuario" required>
                    </div>
                    <div>
                        <input v-model="formData.password" type="password" placeholder="Contraseña" required>
                    </div>
                    <div>
                        <button class="btn" type="submit" :disabled="isDisabled">
                            <span>Login</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
.card-login button {
    font-size: var(--fontsize);
    background-color: white;
    border-radius: 1rem 2rem;
}

.card-login input,
span {
    font-size: var(--fontsize);
    border-radius: 1rem 2rem;
    padding: .2rem;
}

.formulario div {
    margin-bottom: 1rem;
}

.formulario {
    position: relative;
    top: -10rem;
}

.card-login img {
    position: relative;
    width: 50rem;
    top: 0;
}

.card-login {
    position: absolute;
    display: flex;
    flex-direction: column;
}

.login-content {
    position: relative;
    width: 100vw;
    height: 69rem;
    overflow-y: hidden;
    overflow-x: hidden;
    display: flex;
    justify-content: center;
    /* Esto centra el contenido horizontalmente */
    align-items: flex-start;
    /* Esto alinea el contenido en la parte superior */
}
</style>