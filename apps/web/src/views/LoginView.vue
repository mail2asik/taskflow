<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const email = ref('admin@taskflow.dev')
const password = ref('password')
const errorMessage = ref('')
const isLoading = ref(false)

const authStore = useAuthStore()
const router = useRouter()

async function onSubmit() {
  errorMessage.value = ''
  isLoading.value = true
  try {
    await authStore.login({ email: email.value, password: password.value })
    router.push({ name: 'dashboard' })
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message || 'Invalid login credentials.'
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="max-w-md w-full mx-auto bg-white p-8 rounded-xl shadow-sm border border-gray-200">
    <h2 class="text-2xl font-bold text-gray-900 text-center mb-6">Sign in to TaskFlow</h2>

    <div v-if="errorMessage" class="mb-4 p-3 bg-red-50 text-red-700 text-sm rounded-lg border border-red-200">
      {{ errorMessage }}
    </div>

    <form @submit.prevent="onSubmit" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input
          v-model="email"
          type="email"
          required
          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
        />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <input
          v-model="password"
          type="password"
          required
          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none"
        />
      </div>

      <button
        type="submit"
        :disabled="isLoading"
        class="w-full bg-indigo-600 text-white py-2 rounded-lg font-semibold hover:bg-indigo-700 disabled:opacity-50 transition"
      >
        {{ isLoading ? 'Signing in...' : 'Sign In' }}
      </button>
    </form>
  </div>
</template>