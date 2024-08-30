<script setup lang="ts">
import { ref, onMounted, defineProps } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { route } from 'ziggy-js';
import { useImageStore } from '../../../store/imageStore.ts';

const props = defineProps<{
    user: {
        name: string;
        admin: number;
    };
}>();

const { user } = props;

const imageStore = useImageStore();
onMounted(() => {
    imageStore.fetchImagePath();
});


const handleLogout = async () => {
    try {
        await Inertia.post(route('logout'));
    } catch (error) {
        console.error('Logout failed:', error);
    }
};

const admin = ref(props.user.admin === 1);

const showSubMenu = ref({
  clientes: false,
});

const toggleSubMenu = (menu: string) => {
  showSubMenu.value[menu] = !showSubMenu.value[menu];
};

</script>

<template>
    <div class="side-menu-content">
        <div class="icon">
            <a @click="$emit('changeComponent', 'Principal')"><img :src="`${imageStore.imagePath}/Logo.png`"></a>
        </div>
        <div class="user">
            <p>Usuario: {{ user.name }}</p>
            <p v-if="admin"><i class="fa-solid fa-user-shield"></i> Administrador</p>
            <p v-if="!admin"><i class="fa-solid fa-user-large"></i> Asistente</p>
            <button class="btn btn-info" @click="handleLogout">Logout</button>
        </div>
        <div class="menu-content">
            <ul class="lista-sideMenu">
                <button @click="toggleSubMenu('clientes')">
                    Clientes
                    <ul v-if="showSubMenu.clientes" class="sub-menu" :class="{ active: showSubMenu.clientes }">
                        <a @click="$emit('changeComponent', 'FormCliente')"><li>Nuevo cliente</li></a>
                        <a @click="$emit('changeComponent', 'BuscarCliente')"><li>Buscar cliente</li></a>
                        <a @click="$emit('changeComponent', 'ModificarCliente')"><li>Modificar cliente</li></a>
                    </ul>
                </button>
            </ul>
        </div>
    </div>
</template>

<style scoped>

.sub-menu {
    position: relative;
    text-align: center;
    list-style: none;
    opacity: 1;
    overflow: hidden;
}

.lista-sideMenu ul li{
    width: 100%; /* Asegura que los elementos <li> ocupen todo el ancho del contenedor <ul> */
    background-color: rgba(0, 0, 0, 0.5); /* Elige el color de fondo deseado */
    border-radius: 4rem 1rem;
    margin-bottom: .2rem;
    padding: .1rem; /* Añade un relleno para que el contenido no esté pegado a los bordes */
    box-sizing: border-box; /* Asegura que el padding no altere el ancho del <li> */
}

.lista-sideMenu ul li a{
    color: black;
}

.lista-sideMenu{
    position: relative;
    list-style: none;
    text-align: center;
}

.menu-content button {
    width: 100%;
    border-radius: 4rem 1rem;
    background-color: var(--color-first);
    border: solid 2px black;
    color: black;
}

.menu-content {
    position: relative;
    width: 28rem;
    font-size: var(--fontsize);
    margin-top: 1rem;
}

.user p{
    font-size: 1.5rem;
    color: whitesmoke;
}

.user{
    position: relative;
    margin-top: -5rem;
    border: solid .2rem white;
    border-radius: 2rem;
    width: 20rem;
    left: 5rem;
    background-color: rgba(0, 0, 0, 0.2);
    padding: .2rem;
}

.icon img {
    position: relative;
    margin-top: -5rem;
    width: 25rem;
    cursor: pointer;
}

.side-menu-content {
    position: absolute;
    top: 0;
    left: 0;
    width: 30rem;
    height: 100vh;
    overflow-y: hidden;
    overflow-x: hidden;
    background-color: var(--color-base);
}

@keyframes appear {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}
</style>