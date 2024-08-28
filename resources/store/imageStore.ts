// stores/imageStore.ts
import { defineStore } from 'pinia';

export const useImageStore = defineStore('image', {
  state: () => ({
    imagePath: '' as string,
  }),
  actions: {
    async fetchImagePath() {
      try {
        this.imagePath = '/images';
      } catch (error) {
        console.error('Error fetching image path:', error);
      }
    }
  }
});
