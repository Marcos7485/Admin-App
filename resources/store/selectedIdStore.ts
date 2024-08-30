import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useSelectedIdStore = defineStore('selectedId', () => {
  const selectedId = ref<string | null>(null)

  function setSelectedId(id: string) {
    selectedId.value = id
  }

  return { selectedId, setSelectedId }
})
