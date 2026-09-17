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
    <!-- Sidebar Navigation -->
    <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col border-r border-slate-800">
      <div class="h-16 flex items-center px-6 border-b border-slate-800">
        <RouterLink to="/app/dashboard" class="text-xl font-black text-white tracking-wider flex items-center gap-2">
          <span class="w-3 h-3 bg-indigo-500 rounded-full"></span>
          TaskFlow
        </RouterLink>
      </div>

      <nav class="flex-1 px-4 py-6 space-y-1">
        <RouterLink
          to="/app/dashboard"
          active-class="bg-slate-800 text-white font-semibold"
          class="flex items-center px-4 py-3 text-sm rounded-xl hover:bg-slate-800/60 hover:text-white transition"
        >
          📊 Dashboard
        </RouterLink>

        <RouterLink
          to="/app/projects"
          active-class="bg-slate-800 text-white font-semibold"
          class="flex items-center px-4 py-3 text-sm rounded-xl hover:bg-slate-800/60 hover:text-white transition"
        >
          📁 Projects
        </RouterLink>
      </nav>

      <div class="p-4 border-t border-slate-800 flex items-center justify-between">
        <div class="truncate">
          <p class="text-xs text-slate-400">Signed in as</p>
          <p class="text-sm font-medium text-white truncate">{{ authStore.user?.name }}</p>
        </div>
        <button
          @click="handleLogout"
          title="Logout"
          class="p-2 text-slate-400 hover:text-red-400 hover:bg-slate-800 rounded-lg transition"
        >
          🚪
        </button>
      </div>
    </aside>

    <!-- Main Content Body -->
    <div class="flex-1 flex flex-col overflow-y-auto">
      <main class="p-8 max-w-7xl w-full mx-auto">
        <RouterView />
      </main>
    </div>
  </div>
</template>