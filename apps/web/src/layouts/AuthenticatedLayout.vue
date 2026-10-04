<script setup lang="ts">
import { RouterLink, RouterView, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()
const router = useRouter()

async function handleLogout() {
  await authStore.logout()
  router.push({ name: 'login' })
}
</script>

<template>
  <div class="min-h-screen bg-slate-50 flex flex-col">
    <!-- Top Navigation Bar -->
    <header class="bg-slate-900 text-slate-300 border-b border-slate-800 px-6 py-4">
      <div class="max-w-7xl mx-auto flex items-center justify-between">
        
        <!-- Left Side: Logo & App Name -->
        <RouterLink to="/" class="flex items-center gap-3 group">
          <div class="w-9 h-9 rounded-xl bg-brand-600 flex items-center justify-center text-white font-bold text-lg shadow-md shadow-brand-600/30">
            ⚡
          </div>
          <span class="text-xl font-black text-white tracking-tight">TaskFlow</span>
        </RouterLink>

        <!-- Right Side: User Controls -->
        <div class="flex items-center gap-6">
          <!-- Welcome Message -->
          <span class="text-sm text-slate-300 font-medium">
            Welcome, <strong class="text-white">{{ authStore.user?.name || 'User' }}</strong>
          </span>

          <!-- Navigation / Actions -->
          <div class="flex items-center gap-3 text-sm">
            <!-- Placeholder for Change Password feature -->
            <RouterLink 
              to="/app/change-password" 
              class="px-3 py-1.5 rounded-lg font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition"
            >
              🔒 Change Password
            </RouterLink>

            <!-- Logout Button -->
            <button
              @click="handleLogout"
              class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-semibold text-red-400 hover:bg-red-500/10 hover:text-red-300 transition"
            >
              🚪 Logout
            </button>
          </div>
        </div>

      </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-7xl w-full mx-auto p-8 overflow-y-auto">
      <RouterView />
    </main>
  </div>
</template>