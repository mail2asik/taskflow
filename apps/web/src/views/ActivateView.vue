<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const code = ref('')
const errorMessage = ref('')
const isLoading = ref(false)

onMounted(() => {
  if (route.query.email) {
    email.value = String(route.query.email)
  }
})

async function onActivate() {
  errorMessage.value = ''
  if (!email.value || !code.value) {
    errorMessage.value = 'Please provide both email and activation code.'
    return
  }

  isLoading.value = true
  try {
    const res = await authStore.activateAccount({
      email: email.value,
      code: code.value,
    })
    if (res.success) {
      router.push({ name: 'dashboard' })
    }
  } catch (err: any) {
    if (err.response?.data?.errors) {
      const errs = err.response.data.errors
      errorMessage.value = errs.code?.[0] || errs.email?.[0] || 'Invalid activation details.'
    } else {
      errorMessage.value = err.response?.data?.message || 'Activation failed.'
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
      <h2 class="text-2xl font-bold text-slate-900">Activate Account</h2>
      <p class="text-sm text-slate-500 mt-1">Enter the activation code sent to your email.</p>
    </div>

    <div v-if="errorMessage" class="mb-4 p-3 bg-red-50 text-red-700 text-sm rounded-xl border border-red-200">
      {{ errorMessage }}
    </div>

    <form @submit.prevent="onActivate" class="space-y-4">
      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">Email Address</label>
        <input
          v-model="email"
          type="email"
          required
          class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-brand-500 focus:outline-none text-sm"
        />
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">Activation Code</label>
        <input
          v-model="code"
          type="text"
          placeholder="e.g. 832769"
          required
          class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-brand-500 focus:outline-none text-sm tracking-widest text-center font-mono text-lg"
        />
      </div>

      <button
        type="submit"
        :disabled="isLoading"
        class="w-full bg-brand-600 text-white py-3 rounded-xl font-bold hover:bg-brand-700 disabled:opacity-50 transition shadow-md shadow-brand-600/20"
      >
        {{ isLoading ? 'Activating...' : 'Activate Account' }}
      </button>
    </form>
  </div>
</template>