<!-- src/views/ChangePasswordView.vue -->
<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const currentPassword = ref('')
const newPassword = ref('')
const newPasswordConfirmation = ref('')

const generalError = ref('')
const fieldErrors = ref<Record<string, string[]>>({})
const successMessage = ref('')
const isLoading = ref(false)

const authStore = useAuthStore()
const router = useRouter()

async function onChangePassword() {
  generalError.value = ''
  fieldErrors.value = {}
  successMessage.value = ''

  if (newPassword.value !== newPasswordConfirmation.value) {
    fieldErrors.value = {
      new_password: ['New password and confirmation do not match.'],
    }
    return
  }

  isLoading.value = true

  try {
    const res = await authStore.changePassword({
      current_password: currentPassword.value,
      new_password: newPassword.value,
      new_password_confirmation: newPasswordConfirmation.value,
    })

    if (res.success) {
      successMessage.value = res.message || 'Password has been successfully changed.'
      currentPassword.value = ''
      newPassword.value = ''
      newPasswordConfirmation.value = ''
    }
  } catch (err: any) {
    if (err.response?.data?.errors) {
      fieldErrors.value = err.response.data.errors
    } else {
      generalError.value = err.response?.data?.message || 'Failed to change password.'
    }
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="max-w-md w-full mx-auto bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-slate-900">Change Password</h2>
      <p class="text-sm text-slate-500 mt-1">Update your password to keep your account secure</p>
    </div>

    <!-- General Error Banner -->
    <div
      v-if="generalError"
      class="mb-4 p-3 bg-red-50 text-red-700 text-sm rounded-xl border border-red-200"
    >
      {{ generalError }}
    </div>

    <!-- Success Message Banner -->
    <div
      v-if="successMessage"
      class="mb-4 p-3 bg-emerald-50 text-emerald-700 text-sm rounded-xl border border-emerald-200"
    >
      {{ successMessage }}
    </div>

    <form @submit.prevent="onChangePassword" class="space-y-4">
      <!-- Current Password -->
      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">Current Password</label>
        <input
          v-model="currentPassword"
          type="password"
          required
          class="w-full px-3.5 py-2.5 border rounded-xl focus:ring-2 focus:ring-brand-500 focus:outline-none text-sm transition"
          :class="fieldErrors.current_password ? 'border-red-400 bg-red-50/30' : 'border-slate-300'"
        />
        <p v-if="fieldErrors.current_password?.[0]" class="text-xs text-red-600 mt-1">
          {{ fieldErrors.current_password[0] }}
        </p>
      </div>

      <!-- New Password -->
      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">New Password</label>
        <input
          v-model="newPassword"
          type="password"
          required
          minlength="8"
          class="w-full px-3.5 py-2.5 border rounded-xl focus:ring-2 focus:ring-brand-500 focus:outline-none text-sm transition"
          :class="fieldErrors.new_password ? 'border-red-400 bg-red-50/30' : 'border-slate-300'"
        />
        <p v-if="fieldErrors.new_password?.[0]" class="text-xs text-red-600 mt-1">
          {{ fieldErrors.new_password[0] }}
        </p>
      </div>

      <!-- Confirm New Password -->
      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">Confirm New Password</label>
        <input
          v-model="newPasswordConfirmation"
          type="password"
          required
          minlength="8"
          class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-brand-500 focus:outline-none text-sm transition"
        />
      </div>

      <!-- Action Buttons -->
      <div class="pt-2 flex items-center gap-3">
        <button
          type="submit"
          :disabled="isLoading"
          class="flex-1 bg-brand-600 text-white py-2.5 rounded-xl font-bold hover:bg-brand-700 disabled:opacity-50 transition shadow-md shadow-brand-600/20 text-sm"
        >
          {{ isLoading ? 'Updating...' : 'Update Password' }}
        </button>
        <button
          type="button"
          @click="router.push({ name: 'projects' })"
          class="px-4 py-2.5 rounded-xl font-semibold text-slate-600 hover:bg-slate-100 transition text-sm"
        >
          Cancel
        </button>
      </div>
    </form>
  </div>
</template>