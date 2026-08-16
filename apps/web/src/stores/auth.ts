import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import apiClient from '../services/api'
import type { User, AuthResponse } from '../types/user'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const token = ref<string | null>(localStorage.getItem('access_token'))

  const isAuthenticated = computed(() => !!token.value)

  async function login(credentials: Record<string, string>) {
    const response = await apiClient.post<AuthResponse>('/auth/login', credentials)
    setAuthData(response.data)
  }

  async function register(payload: Record<string, string>) {
    const response = await apiClient.post<AuthResponse>('/auth/register', payload)
    setAuthData(response.data)
  }

  async function fetchUser() {
    if (!token.value) return
    try {
      const response = await apiClient.get<{ data: User }>('/auth/me')
      user.value = response.data.data
    } catch {
      logout()
    }
  }

  function setAuthData(data: AuthResponse) {
    user.value = data.user
    token.value = data.access_token
    localStorage.setItem('access_token', data.access_token)
  }

  function logout() {
    user.value = null
    token.value = null
    localStorage.removeItem('access_token')
  }

  return { user, token, isAuthenticated, login, register, fetchUser, logout }
})