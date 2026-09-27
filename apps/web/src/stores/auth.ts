import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import apiClient from '../services/api'
import type { User, AuthData, ActivatePayload } from '../types/user'
import type { ApiResponse } from '../types/api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const token = ref<string | null>(localStorage.getItem('access_token'))

  const isAuthenticated = computed(() => !!token.value)

  function setAuthData(authData: AuthData) {
    user.value = authData.user
    token.value = authData.access_token
    localStorage.setItem('access_token', authData.access_token)
  }

  async function login(credentials: Record<string, string>) {
    const response = await apiClient.post<ApiResponse<AuthData>>('/auth/login', credentials)
    if (response.data.success && response.data.data) {
      setAuthData(response.data.data)
    }
    return response.data
  }

  async function register(payload: Record<string, string>) {
    const response = await apiClient.post<ApiResponse<{ user: User }>>('/auth/register', payload)
    return response.data
  }

  async function activateAccount(payload: ActivatePayload) {
    const response = await apiClient.post<ApiResponse<AuthData>>('/auth/activate', payload)
    if (response.data.success && response.data.data) {
      setAuthData(response.data.data)
    }
    return response.data
  }

  async function fetchUser() {
    if (!token.value) return
    try {
      const response = await apiClient.get<ApiResponse<User>>('/auth/me')
      if (response.data.data) {
        user.value = response.data.data
      }
    } catch {
      logoutLocal()
    }
  }

  async function logout() {
    try {
      await apiClient.post('/auth/logout')
    } catch (err) {
      // Ignore API logout errors and clear local credentials
    } finally {
      logoutLocal()
    }
  }

  function logoutLocal() {
    user.value = null
    token.value = null
    localStorage.removeItem('access_token')
  }

  return {
    user,
    token,
    isAuthenticated,
    login,
    register,
    activateAccount,
    fetchUser,
    logout,
  }
})