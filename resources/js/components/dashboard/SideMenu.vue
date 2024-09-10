<script setup lang="ts">
import { ref, onMounted, defineProps } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { route } from 'ziggy-js';
import { useImageStore } from '../../../store/imageStore.ts';

const props = defineProps<{
    user: {
        name: string;
        admin: boolean;
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

const admin = ref(props.user.admin == true);

const showSubMenu = ref({
    clientes: false, prestamos: false, pagos: false, resumenes: false, recorridos: false, configuraciones: false, mainmenu: false
});


const toggleSubMenu = (menu: string) => {
    // Cierra todos los submenús
    Object.keys(showSubMenu.value).forEach(key => {
        if (key !== menu) {
            if (key !== 'mainmenu') {
                showSubMenu.value[key] = false;
            }
        }
    });

    // Alterna el submenú que se está seleccionando
    showSubMenu.value[menu] = !showSubMenu.value[menu];
};

</script>

<template>
    <div class="side-menu-content" :class="{ active: showSubMenu.mainmenu }">
        <div class="icon">
            <a @click="$emit('changeComponent', 'Principal')"><img :src="`${imageStore.imagePath}/Logo.png`"></a>
        </div>
        <div class="icon-cel" @click="toggleSubMenu('mainmenu')">
            <img :src="`${imageStore.imagePath}/Logo.png`">
        </div>
        <div class="menu-content" :class="{ active: showSubMenu.mainmenu }">
            <div class="user">
                <p>Usuario: {{ user.name }}</p>
                <p v-if="admin"><i class="fa-solid fa-user-shield"></i> Administrador</p>
                <p v-if="!admin"><i class="fa-solid fa-user-large"></i> Asistente</p>
                <button class="btn btn-info" @click="handleLogout">Logout</button>
            </div>
            <ul class="lista-sideMenu">
                <button @click="toggleSubMenu('clientes')">
                    Clientes
                    <ul class="sub-menu" :class="{ active: showSubMenu.clientes }">
                        <a @click="$emit('changeComponent', 'FormCliente')">
                            <li>Nuevo cliente</li>
                        </a>
                        <a @click="$emit('changeComponent', 'BuscarCliente')">
                            <li>Buscar cliente</li>
                        </a>
                    </ul>
                </button>
                <button @click="toggleSubMenu('prestamos')">
                    Creditos
                    <ul class="sub-menu" :class="{ active: showSubMenu.prestamos }">
                        <a @click="$emit('changeComponent', 'FormCredito')">
                            <li>Nuevo credito</li>
                        </a>
                        <a @click="$emit('changeComponent', 'Creditos')">
                            <li>Creditos</li>
                        </a>
                        <a @click="$emit('changeComponent', 'Refinanciacion')">
                            <li>Refinanciacion</li>
                        </a>
                        <a @click="$emit('changeComponent', 'Renovacion')">
                            <li>Renovacion</li>
                        </a>
                    </ul>
                </button>
                <button @click="toggleSubMenu('pagos')">
                    Pagos
                    <ul class="sub-menu" :class="{ active: showSubMenu.pagos }">
                        <a @click="$emit('changeComponent', 'FormPagos')">
                            <li>Nuevo pago</li>
                        </a>
                        <a @click="$emit('changeComponent', 'Pagos')">
                            <li>Pagos</li>
                        </a>
                    </ul>
                </button>
                <button @click="toggleSubMenu('resumenes')">
                    Resumenes
                    <ul class="sub-menu" :class="{ active: showSubMenu.resumenes }">
                        <a @click="$emit('changeComponent', 'Resumenes')">
                            <li>Clientes</li>
                        </a>
                        <a @click="$emit('changeComponent', 'Vendedores')">
                            <li>Vendedores</li>
                        </a>
                        <a @click="$emit('changeComponent', 'Cobradores')">
                            <li>Cobradores</li>
                        </a>
                        <a @click="$emit('changeComponent', 'Mensual')">
                            <li>Mensual</li>
                        </a>
                    </ul>
                </button>
                <button @click="toggleSubMenu('recorridos')">
                    Recorridos
                    <ul class="sub-menu" :class="{ active: showSubMenu.recorridos }">
                        <a @click="$emit('changeComponent', 'RecorridoHoy')">
                            <li>Hoy</li>
                        </a>
                        <a @click="$emit('changeComponent', 'Recorridos')">
                            <li>Historico</li>
                        </a>
                    </ul>
                </button>
                <div v-if="admin">
                    <button @click="toggleSubMenu('configuraciones')">
                        Configuraciones
                        <ul class="sub-menu" :class="{ active: showSubMenu.configuraciones }">
                            <a @click="$emit('changeComponent', 'Seguridad')">
                                <li>Seguridad</li>
                            </a>
                            <a @click="$emit('changeComponent', 'App')">
                                <li>App</li>
                            </a>
                        </ul>
                    </button>
                </div>
            </ul>
        </div>
    </div>
</template>

<style scoped>
.sub-menu.active {
    opacity: 1;
    transform: scaleY(1);
    max-height: 500px;
    /* O el tamaño que necesites */
}

.sub-menu {
    position: relative;
    text-align: center;
    list-style: none;
    opacity: 0;
    transform: scaleY(0);
    transform-origin: top;
    transition: all 0.3s ease-in-out;
    max-height: 0;
    overflow: hidden;
}

.lista-sideMenu ul li:hover {
    width: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    color: white;
    border-radius: 1rem 4rem;
    margin-bottom: .2rem;
    padding: .1rem;
    box-sizing: border-box;
}

.lista-sideMenu ul li a {
    color: black;
}

.lista-sideMenu {
    position: relative;
    list-style: none;
    text-align: center;
}

.menu-content button:hover {
    border: solid 2px black;
    box-shadow: -1px -1px 1px black;
}

.menu-content button {
    margin-top: 1rem;
    width: 100%;
    border-radius: 1rem 4rem;
    background-color: var(--color-first);
    color: black;
}

.menu-content {
    position: relative;
    width: 28rem;
    font-size: var(--fontsize);
}

.user p {
    font-size: 1.5rem;
    color: whitesmoke;
}

.user {
    position: relative;
    margin-top: -5rem;
    border: solid .2rem white;
    border-radius: 1rem 2rem;
    width: 20rem;
    left: 5rem;
    background-color: rgba(0, 0, 0, 0.2);
    padding: .2rem;
}

.icon-cel img {
    display: none;
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


@media (max-width: 600px) {

    .icon-cel img {
        display: block;
        position: relative;
        margin-top: -5rem;
        margin-left: 3rem;
        width: 25rem;
        cursor: pointer;
    }

    .icon img {
        display: none;
    }

    .side-menu-content {
        position: absolute;
        top: 0;
        left: 0;
        width: 30rem;
        height: 15rem;
        border-radius: 1rem 4rem;
        overflow-y: hidden;
        overflow-x: hidden;
        z-index: 1000;
        background-color: var(--color-base);
        transition: height 1s;
    }

    .side-menu-content.active {
        height: 75rem;
    }

    .menu-content {
        position: relative;
        opacity: 0;
        transition: opacity 1s;
    }

    .menu-content.active {
        position: relative;
        opacity: 1;
    }

}
</style>