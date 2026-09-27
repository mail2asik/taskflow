<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { validateRegistration } from '../utils/validation'

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const errors = ref<Record<string, string>>({})
const apiError = ref('')
const isLoading = ref(false)

const authStore = useAuthStore()
const router = useRouter()

async function onSubmit() {
  apiError.value = ''
  errors.value = validateRegistration(form)

  if (Object.keys(errors.value).length > 0) return

  isLoading.value = true
  try {
    const res = await authStore.register(form)
    if (res.success) {
      router.push({ name: 'activate', query: { email: form.email } })
    }
  } catch (err: any) {
    if (err.response?.data?.errors) {
      const serverErrors = err.response.data.errors
      Object.keys(serverErrors).forEach((key) => {
        errors.value[key] = serverErrors[key][0]
      })
    } else {
      apiError.value = err.response?.data?.message || 'Registration failed. Please try again.'
    }
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="max-w-md w-full mx-auto my-8 bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
    <div class="text-center mb-6">
      <h2 class="text-2xl font-bold text-slate-900">Create Account</h2>
      <p class="text-sm text-slate-500 mt-1">Join TaskFlow to start managing projects</p>
    </div>

    <div v-if="apiError" class="mb-4 p-3 bg-red-50 text-red-700 text-sm rounded-xl border border-red-200">
      {{ apiError }}
    </div>

    <form @submit.prevent="onSubmit" class="space-y-4" novalidate>
      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">Full Name</label>
        <input
          v-model="form.name"
          type="text"
          class="w-full px-3.5 py-2.5 border rounded-xl focus:ring-2 focus:ring-brand-500 focus:outline-none text-sm"
          :class="errors.name ? 'border-red-400 focus:ring-red-400' : 'border-slate-300'"
        />
        <p v-if="errors.name" class="text-xs text-red-500 mt-1">{{ errors.name }}</p>
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
        <input
          v-model="form.email"
          type="email"
          class="w-full px-3.5 py-2.5 border rounded-xl focus:ring-2 focus:ring-brand-500 focus:outline-none text-sm"
          :class="errors.email ? 'border-red-400 focus:ring-red-400' : 'border-slate-300'"
        />
        <p v-if="errors.email" class="text-xs text-red-500 mt-1">{{ errors.email }}</p>
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
        <input
          v-model="form.password"
          type="password"
          class="w-full px-3.5 py-2.5 border rounded-xl focus:ring-2 focus:ring-brand-500 focus:outline-none text-sm"
          :class="errors.password ? 'border-red-400 focus:ring-red-400' : 'border-slate-300'"
        />
        <p v-if="errors.password" class="text-xs text-red-500 mt-1">{{ errors.password }}</p>
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">Confirm Password</label>
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
        {{ isLoading ? 'Creating Account...' : 'Register' }}
      </button>

      <p class="text-center text-xs text-slate-500 mt-4">
        Already have an account?
        <RouterLink to="/login" class="text-brand-600 font-bold hover:underline">Sign In</RouterLink>
      </p>
    </form>
  </div>
</template>