import { defineStore } from 'pinia'
import { ref } from 'vue'
import apiClient from '../services/api'
import type { SubscribePayload, Subscriber } from '../types/subscriber'
import type { ApiResponse } from '../types/api'

export const useSubscriberStore = defineStore('subscriber', () => {
  const isLoading = ref(false)

  async function subscribe(payload: SubscribePayload) {
    isLoading.value = true
    try {
      const response = await apiClient.post<ApiResponse<Subscriber>>('/subscribe', payload)
      return response.data
    } finally {
      isLoading.value = false
    }
  }

  return {
    isLoading,
    subscribe,
  }
})