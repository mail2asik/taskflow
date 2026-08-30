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
  <div class="min-h-screen bg-gray-100 flex flex-col">
    <!-- Authenticated Header Navigation -->
    <header class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <div class="flex items-center space-x-8">
          <RouterLink to="/app/dashboard" class="text-xl font-bold text-indigo-600">
            TaskFlow
          </RouterLink>
          
          <nav class="flex space-x-4">
            <RouterLink
              to="/app/dashboard"
              active-class="text-indigo-600 border-indigo-600"
              class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-indigo-600 border-b-2 border-transparent"
            >
              Dashboard
            </RouterLink>
            <RouterLink
              to="/app/projects"
              active-class="text-indigo-600 border-indigo-600"
              class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-indigo-600 border-b-2 border-transparent"
            >
              Projects
            </RouterLink>
          </nav>
        </div>

        <div class="flex items-center space-x-4">
          <span v-if="authStore.user" class="text-sm text-gray-700 font-medium">
            {{ authStore.user.name }}
          </span>
          <button
            @click="handleLogout"
            class="text-sm font-medium text-red-600 hover:text-red-700 border border-red-200 px-3 py-1.5 rounded-md hover:bg-red-50 transition"
          >
            Logout
          </button>
        </div>
      </div>
    </header>

    <!-- Main Content Body -->
    <main class="flex-1 max-w-7xl w-full mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <RouterView />
    </main>
  </div>
</template>