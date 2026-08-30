<script setup lang="ts">
import { RouterLink, RouterView } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">
    <!-- Guest Navigation Header -->
    <header class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <RouterLink to="/" class="text-xl font-bold text-indigo-600 flex items-center gap-2">
          <span>TaskFlow</span>
        </RouterLink>

        <nav class="flex items-center space-x-4">
          <template v-if="!authStore.isAuthenticated">
            <RouterLink to="/login" class="text-sm font-medium text-gray-700 hover:text-indigo-600">
              Login
            </RouterLink>
            <RouterLink
              to="/register"
              class="text-sm font-medium bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition"
            >
              Register
            </RouterLink>
          </template>
          <template v-else>
            <RouterLink
              to="/app/dashboard"
              class="text-sm font-medium bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700"
            >
              Go to App
            </RouterLink>
          </template>
        </nav>
      </div>
    </header>

    <!-- Page Content -->
    <main class="flex-1 flex flex-col justify-center">
      <RouterView />
    </main>
  </div>
</template>