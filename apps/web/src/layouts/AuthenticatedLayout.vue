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
  <div class="min-h-screen bg-slate-50 flex">
    <!-- Private Sidebar -->
    <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col justify-between p-4 border-r border-slate-800">
      <div class="space-y-6">
        <!-- Logo (Navigates to Public Landing Page) -->
        <RouterLink to="/" class="flex items-center gap-3 px-2 group">
          <div class="w-9 h-9 rounded-xl bg-brand-600 flex items-center justify-center text-white font-bold text-lg shadow-md shadow-brand-600/30">
            ⚡
          </div>
          <span class="text-xl font-black text-white tracking-tight">TaskFlow</span>
        </RouterLink>

        <!-- Private Navigation Links -->
        <nav class="space-y-1">
          <RouterLink
            to="/app/dashboard"
            exact-active-class="bg-brand-600 text-white font-bold"
            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm hover:bg-slate-800 text-slate-300 transition"
          >
            📊 Dashboard
          </RouterLink>
          <RouterLink
            to="/app/projects"
            active-class="bg-brand-600 text-white font-bold"
            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm hover:bg-slate-800 text-slate-300 transition"
          >
            📁 Projects
          </RouterLink>
        </nav>
      </div>

      <!-- Authenticated User Profile & Logout -->
      <div class="pt-4 border-t border-slate-800 space-y-3">
        <div class="px-2">
          <p class="text-xs font-semibold text-slate-400">Signed in as</p>
          <p class="text-sm font-bold text-white truncate">{{ authStore.user?.name || 'User' }}</p>
        </div>

        <button
          @click="handleLogout"
          class="w-full flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-red-400 hover:bg-red-500/10 hover:text-red-300 transition"
        >
          🚪 Logout
        </button>
      </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 p-8 overflow-y-auto">
      <RouterView />
    </main>
  </div>
</template>