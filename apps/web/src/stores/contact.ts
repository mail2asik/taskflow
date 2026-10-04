import { defineStore } from 'pinia'
import { ref } from 'vue'
import apiClient from '../services/api'
import type { ContactPayload, ContactSubmission } from '../types/contact'
import type { ApiResponse } from '../types/api'

export const useContactStore = defineStore('contact', () => {
  const isLoading = ref(false)

  async function submitContact(payload: ContactPayload) {
    isLoading.value = true
    try {
      const response = await apiClient.post<ApiResponse<ContactSubmission>>('/contact', payload)
      return response.data
    } finally {
      isLoading.value = false
    }
  }

  return {
    isLoading,
    submitContact,
  }
})