<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useImageStore } from '../../../store/imageStore.ts';

const imageStore = useImageStore();
onMounted(() => {
    imageStore.fetchImagePath();
});


</script>

<template>
    <div class="footer">
        <a href="https://www.dragonrojosoftware.com"><p>Powered by <img :src="`${imageStore.imagePath}/footer/drs.webp`"> &copy;2024</p></a>
    </div>
</template>

<style scoped>

.footer p {
    font-size: 1.5rem;
}

.footer img{
    width: 2rem;
}

.footer a{
    text-decoration: none;
    color: black;
}

.footer{
    position: fixed;
    bottom: 0;
    left: 7rem;
    z-index: 1000;
}
</style>