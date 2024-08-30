<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useImageStore } from '../../../store/imageStore.ts';

const imageStore = useImageStore();
onMounted(() => {
    imageStore.fetchImagePath();
});


</script>

<template>
    <div class="content">
        <div class="box">
            <h1>Modificar Cliente</h1>
        </div>
    </div>
</template>

<style scoped>


.box {
    border: solid 2px grey;
    padding: 2rem;
    height: 100%;
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
</style>