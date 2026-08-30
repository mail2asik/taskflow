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
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Projects</h1>
    </div>

    <div v-if="projectStore.isLoading" class="text-gray-500">Loading projects...</div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="project in projectStore.projects"
        :key="project.id"
        class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition"
      >
        <div class="flex justify-between items-start mb-2">
          <span class="text-xs font-bold px-2 py-0.5 bg-gray-100 text-gray-700 rounded">{{ project.key }}</span>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-1">{{ project.name }}</h3>
        <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ project.description || 'No description provided.' }}</p>
        
        <RouterLink
          :to="`/app/projects/${project.id}/board`"
          class="inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-800"
        >
          Open Kanban Board &rarr;
        </RouterLink>
      </div>
    </div>
  </div>
</template>