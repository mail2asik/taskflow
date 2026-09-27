<!-- src/views/ForgotPasswordView.vue -->
<script setup lang="ts">
import { ref } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const email = ref('')
const errorMessage = ref('')
const isLoading = ref(false)

const authStore = useAuthStore()
const router = useRouter()

async function onSubmit() {
  errorMessage.value = ''
  if (!email.value) {
    errorMessage.value = 'Please enter your email address.'
    return
  }

  isLoading.value = true
  try {
    const res = await authStore.forgotPassword(email.value)
    if (res.success) {
      // Redirect to Reset Password page with pre-filled email query parameter
      router.push({ name: 'reset-password', query: { email: email.value } })
    }
  } catch (err: any) {
    if (err.response?.data?.errors?.email) {
      errorMessage.value = err.response.data.errors.email[0]
    } else {
      errorMessage.value = err.response?.data?.message || 'Failed to send password reset code.'
    }
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="max-w-md w-full mx-auto my-8 bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
    <div class="text-center mb-6">
      <div class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center mx-auto text-2xl mb-3">
        🔒
      </div>
      <h2 class="text-2xl font-bold text-slate-900">Forgot Password</h2>
      <p class="text-sm text-slate-500 mt-1">Enter your email and we'll send you a password reset code.</p>
    </div>

    <div v-if="errorMessage" class="mb-4 p-3 bg-red-50 text-red-700 text-sm rounded-xl border border-red-200">
      {{ errorMessage }}
    </div>

    <form @submit.prevent="onSubmit" class="space-y-4">
      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">Email Address</label>
        <input
          v-model="email"
          type="email"
          required
          placeholder="your.email@example.com"
          class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-brand-500 focus:outline-none text-sm"
        />
      </div>

      <button
        type="submit"
        :disabled="isLoading"
        class="w-full bg-brand-600 text-white py-3 rounded-xl font-bold hover:bg-brand-700 disabled:opacity-50 transition shadow-md shadow-brand-600/20"
      >
        {{ isLoading ? 'Sending Code...' : 'Send Reset Code' }}
      </button>

      <p class="text-center text-xs text-slate-500 mt-4">
        Remembered your password?
        <RouterLink to="/login" class="text-brand-600 font-bold hover:underline">Sign In</RouterLink>
      </p>
    </form>
  </div>
</template>