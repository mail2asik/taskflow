<script setup lang="ts">
import { onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useProjectStore } from '../stores/project'

const projectStore = useProjectStore()

onMounted(() => {
  projectStore.fetchProjects()
})
</script>

<template>
  <div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Projects Overview</h3>
        <p class="text-3xl font-bold text-indigo-600">{{ projectStore.projects.length }}</p>
        <p class="text-sm text-gray-500 mt-1">Active workspace projects</p>
        <RouterLink to="/app/projects" class="inline-block mt-4 text-sm font-medium text-indigo-600 hover:text-indigo-800">
          View all projects &rarr;
        </RouterLink>
      </div>
    </div>
  </div>
</template>