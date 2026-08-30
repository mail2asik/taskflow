<script setup lang="ts">
import { RouterLink } from 'vue-router'
</script>

<template>
  <div class="text-center px-4 py-16">
    <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl">
      Manage Projects & Issues in Real-Time
    </h1>
    <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
      TaskFlow provides intuitive Kanban boards, project management, and live WebSocket updates.
    </p>
    <div class="mt-8 flex justify-center gap-4">
      <RouterLink
        to="/register"
        class="bg-indigo-600 text-white font-semibold px-6 py-3 rounded-lg hover:bg-indigo-700 transition"
      >
        Get Started Free
      </RouterLink>
      <RouterLink
        to="/login"
        class="bg-white text-gray-700 font-semibold px-6 py-3 rounded-lg border border-gray-300 hover:bg-gray-50 transition"
      >
        Sign In
      </RouterLink>
    </div>
  </div>
</template>