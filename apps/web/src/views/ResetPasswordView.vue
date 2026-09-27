<!-- src/views/ResetPasswordView.vue -->
<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const form = reactive({
  email: '',
  code: '',
  password: '',
  password_confirmation: '',
})

const errors = ref<Record<string, string>>({})
const apiError = ref('')
const isLoading = ref(false)

onMounted(() => {
  if (route.query.email) {
    form.email = String(route.query.email)
  }
})

function validateForm(): boolean {
  errors.value = {}

  if (!form.email.trim()) {
    errors.value.email = 'Email address is required.'
  }
  if (!form.code.trim()) {
    errors.value.code = 'Reset code is required.'
  }
  if (!form.password) {
    errors.value.password = 'New password is required.'
  } else if (form.password.length < 8) {
    errors.value.password = 'Password must be at least 8 characters.'
  }
  if (form.password !== form.password_confirmation) {
    errors.value.password_confirmation = 'Passwords do not match.'
  }

  return Object.keys(errors.value).length === 0
}

async function onSubmit() {
  apiError.value = ''
  if (!validateForm()) return

  isLoading.value = true
  try {
    const res = await authStore.resetPassword(form)
    if (res.success) {
      // Direct back to login upon successful reset
      router.push({ name: 'login' })
    }
  } catch (err: any) {
    if (err.response?.data?.errors) {
      const serverErrors = err.response.data.errors
      Object.keys(serverErrors).forEach((key) => {
        errors.value[key] = serverErrors[key][0]
      })
    } else {
      apiError.value = err.response?.data?.message || 'Failed to reset password. Please try again.'
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
        🔑
      </div>
      <h2 class="text-2xl font-bold text-slate-900">Reset Password</h2>
      <p class="text-sm text-slate-500 mt-1">Enter your reset code and set a new password.</p>
    </div>

    <div v-if="apiError" class="mb-4 p-3 bg-red-50 text-red-700 text-sm rounded-xl border border-red-200">
      {{ apiError }}
    </div>

    <form @submit.prevent="onSubmit" class="space-y-4" novalidate>
      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">Email Address</label>
        <input
          v-model="form.email"
          type="email"
          class="w-full px-3.5 py-2.5 border rounded-xl focus:ring-2 focus:ring-brand-500 focus:outline-none text-sm"
          :class="errors.email ? 'border-red-400 focus:ring-red-400' : 'border-slate-300'"
        />
        <p v-if="errors.email" class="text-xs text-red-500 mt-1">{{ errors.email }}</p>
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">Reset Code</label>
        <input
          v-model="form.code"
          type="text"
          placeholder="e.g. 304642"
          class="w-full px-3.5 py-2.5 border rounded-xl focus:ring-2 focus:ring-brand-500 focus:outline-none text-sm tracking-widest text-center font-mono text-lg"
          :class="errors.code ? 'border-red-400 focus:ring-red-400' : 'border-slate-300'"
        />
        <p v-if="errors.code" class="text-xs text-red-500 mt-1">{{ errors.code }}</p>
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">New Password</label>
        <input
          v-model="form.password"
          type="password"
          class="w-full px-3.5 py-2.5 border rounded-xl focus:ring-2 focus:ring-brand-500 focus:outline-none text-sm"
          :class="errors.password ? 'border-red-400 focus:ring-red-400' : 'border-slate-300'"
        />
        <p v-if="errors.password" class="text-xs text-red-500 mt-1">{{ errors.password }}</p>
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">Confirm New Password</label>
        <input
          v-model="form.password_confirmation"
          type="password"
          class="w-full px-3.5 py-2.5 border rounded-xl focus:ring-2 focus:ring-brand-500 focus:outline-none text-sm"
          :class="errors.password_confirmation ? 'border-red-400 focus:ring-red-400' : 'border-slate-300'"
        />
        <p v-if="errors.password_confirmation" class="text-xs text-red-500 mt-1">{{ errors.password_confirmation }}</p>
      </div>

      <button
        type="submit"
        :disabled="isLoading"
        class="w-full bg-brand-600 text-white py-3 rounded-xl font-bold hover:bg-brand-700 disabled:opacity-50 transition shadow-md shadow-brand-600/20"
      >
        {{ isLoading ? 'Resetting Password...' : 'Reset Password' }}
      </button>

      <p class="text-center text-xs text-slate-500 mt-4">
        Back to
        <RouterLink to="/login" class="text-brand-600 font-bold hover:underline">Sign In</RouterLink>
      </p>
    </form>
  </div>
</template>