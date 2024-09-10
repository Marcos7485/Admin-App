<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useImageStore } from '../../../store/imageStore.ts';

const imageStore = useImageStore();
onMounted(() => {
    imageStore.fetchImagePath();
});

const background = computed(() => ({
    backgroundImage: `url(${imageStore.imagePath}/Principal/fondo.webp)`
}));


</script>
<template>
    <div class="content" :style="background">
        <img :src="`${imageStore.imagePath}/Principal/logomarca.png`">
    </div>
</template>

<style scoped>
.content img {
    position: absolute;
    width: 45rem;
    left: 45%;
    top: -5rem;
    transform: translateX(-10rem);
    opacity: 0;
    animation: TranslateIzqDer 2s 1s forwards;
}

.content {
    padding: 1rem;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    opacity: 0;
    animation: appear 2s forwards;
}

@keyframes appear {
    100% {
        opacity: 1;
    }
}

@keyframes TranslateIzqDer {
    100% {
        transform: translateX(0);
        opacity: 1;
    }
}


@media (max-width: 600px) {
    .content img {
        display: none;
    }
}
</style>