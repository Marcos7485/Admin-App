<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useImageStore } from '../../../store/imageStore.ts';

const imageStore = useImageStore();
onMounted(() => {
    imageStore.fetchImagePath();
});


interface FormData {
    user_id: number | string;
    oldPassword: string;
    newPassword: string | number;
    passwordRepeat: string | number;
}

const adminForm = ref<FormData>({
    user_id: 1,
    oldPassword: '',
    newPassword: '',
    passwordRepeat: ''
});

const assistantForm = ref<FormData>({
    user_id: 2,
    oldPassword: '',
    newPassword: '',
    passwordRepeat: ''
});
const adminErrors = ref<Record<string, string>>({});
const assistantErrors = ref<Record<string, string>>({});
const adminSuccessMessage = ref<string | null>(null);
const assistantSuccessMessage = ref<string | null>(null);


const isDisabled = ref<boolean>(false);


const submitForm = async (userId: number) => {
    if (isDisabled.value) return;
    isDisabled.value = true;

    const form = userId === 1 ? adminForm.value : assistantForm.value;

    try {
        const response = await axios.post('/setup/password', form);
        if (userId === 1) {
            adminSuccessMessage.value = 'Contraseña del admin cambiada correctamente.';
            adminErrors.value = {};
        } else {
            assistantSuccessMessage.value = 'Contraseña del asistente cambiada correctamente.';
            assistantErrors.value = {};
        }
    } catch (error) {
        if (error.response && error.response.status === 422) {
            // Handle validation errors
            if (userId === 1) {
                adminErrors.value = error.response.data.errors;
                adminSuccessMessage.value = null;
            } else {
                assistantErrors.value = error.response.data.errors;
                assistantSuccessMessage.value = null;
            }
        } else {
            console.error('Error enviando formulario', error);
            if (userId === 1) {
                adminSuccessMessage.value = null;
            } else {
                assistantSuccessMessage.value = null;
            }
        }
    } finally {
        isDisabled.value = false;
    }
};


</script>

<template>
    <div class="content">
        <div class="box">
            <div class="form">

                <div class="cliente">
                    <h1>Contraseña admin</h1>
                    <hr>
                    <form @submit.prevent="submitForm(1)">
                        <div class="linea1">
                            <div>
                                <input v-model="adminForm.user_id" type="hidden" value="1">
                            </div>
                            <div>
                                <label for="password">Contraseña actual</label>
                                <input v-model="adminForm.oldPassword" type="password" placeholder="Contraseña actual"
                                    required>
                                <p v-if="adminErrors.oldPassword" class="error">{{ adminErrors.oldPassword }}</p>
                            </div>
                            <div>
                                <label for="password">Contraseña nueva</label>
                                <input v-model="adminForm.newPassword" type="password" placeholder="Nueva contraseña"
                                    required>
                                <p v-if="adminErrors.newPassword" class="error">{{ adminErrors.newPassword }}</p>
                            </div>
                            <div>
                                <label for="password">Repetir contraseña</label>
                                <input v-model="adminForm.passwordRepeat" type="password"
                                    placeholder="Repetir contraseña" required>
                                <p v-if="adminErrors.passwordRepeat" class="error">{{ adminErrors.passwordRepeat }}</p>
                            </div>
                        </div>
                        <div>
                            <button class="btn btn-info" type="submit" :disabled="isDisabled">Guardar</button>
                        </div>
                    </form>
                    <p v-if="adminSuccessMessage">{{ adminSuccessMessage }}</p>
                </div>

                <div class="cliente">
                    <h1>Contraseña asistente</h1>
                    <hr>
                    <form @submit.prevent="submitForm(2)">
                        <div class="linea1">
                            <div>
                                <input v-model="assistantForm.user_id" type="hidden" value="2">
                            </div>
                            <div>
                                <label for="password">Contraseña actual</label>
                                <input v-model="assistantForm.oldPassword" type="password"
                                    placeholder="Contraseña actual" required>
                                <p v-if="assistantErrors.oldPassword" class="error">{{ assistantErrors.oldPassword }}
                                </p>
                            </div>
                            <div>
                                <label for="password">Contraseña nueva</label>
                                <input v-model="assistantForm.newPassword" type="password"
                                    placeholder="Nueva contraseña" required>
                                <p v-if="assistantErrors.newPassword" class="error">{{ assistantErrors.newPassword }}
                                </p>
                            </div>
                            <div>
                                <label for="password">Repetir contraseña</label>
                                <input v-model="assistantForm.passwordRepeat" type="password"
                                    placeholder="Repetir contraseña" required>
                                <p v-if="assistantErrors.passwordRepeat" class="error">{{ assistantErrors.passwordRepeat
                                    }}</p>
                            </div>
                        </div>
                        <div>
                            <button class="btn btn-info" type="submit" :disabled="isDisabled">Guardar</button>
                        </div>
                    </form>
                    <p v-if="assistantSuccessMessage">{{ assistantSuccessMessage }}</p>
                </div>

                <!-- <p v-if="responseMessage">{{ responseMessage }} <i class="fa-solid fa-circle-check"></i></p> -->
            </div>
        </div>
    </div>
</template>

<style scoped>
.cliente {
    display: flex;
    flex-direction: column;
    margin-top: 5rem;
}

.linea1 div {
    display: flex;
    flex-direction: column;
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
    font-size: var(--fontsize);
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
    width: 100%;
    justify-content: center;
    align-items: center;
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