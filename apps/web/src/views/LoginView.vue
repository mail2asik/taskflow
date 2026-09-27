<script setup lang="ts">
import { ref } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const email = ref('')
const password = ref('')
const errorMessage = ref('')
const isLoading = ref(false)

const authStore = useAuthStore()
const router = useRouter()

async function onLogin() {
  errorMessage.value = ''
  isLoading.value = true

  try {
    const res = await authStore.login({
      email: email.value,
      password: password.value,
    })
    if (res.success) {
      router.push({ name: 'dashboard' })
    }
  } catch (err: any) {
    if (err.response?.data?.errors) {
      const errMsg = err.response.data.errors.email?.[0] || 'Invalid credentials.'
      errorMessage.value = errMsg

      // If account is not active, offer auto redirect to activate page
      if (errMsg.includes('not active')) {
        setTimeout(() => {
          router.push({ name: 'activate', query: { email: email.value } })
        }, 1500)
      }
    } else {
      errorMessage.value = err.response?.data?.message || 'Login failed.'
    }
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="max-w-md w-full mx-auto my-8 bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
    <div class="text-center mb-6">
      <h2 class="text-2xl font-bold text-slate-900">Sign In</h2>
      <p class="text-sm text-slate-500 mt-1">Access your TaskFlow dashboard</p>
    </div>

    <div v-if="errorMessage" class="mb-4 p-3 bg-red-50 text-red-700 text-sm rounded-xl border border-red-200">
      {{ errorMessage }}
    </div>

    <form @submit.prevent="onLogin" class="space-y-4">
      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
        <input
          v-model="email"
          type="email"
          required
          class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-brand-500 focus:outline-none text-sm"
        />
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
        <input
          v-model="password"
          type="password"
          required
          class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-brand-500 focus:outline-none text-sm"
        />
      </div>

      <button
        type="submit"
        :disabled="isLoading"
        class="w-full bg-brand-600 text-white py-3 rounded-xl font-bold hover:bg-brand-700 disabled:opacity-50 transition shadow-md shadow-brand-600/20"
      >
        {{ isLoading ? 'Signing In...' : 'Sign In' }}
      </button>

      <p class="text-center text-xs text-slate-500 mt-4">
        Don't have an account?
        <RouterLink to="/register" class="text-brand-600 font-bold hover:underline">Register</RouterLink>
      </p>
    </form>
  </div>
</template>