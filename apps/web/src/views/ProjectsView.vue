<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { RouterLink } from 'vue-router'
import { useProjectStore } from '../stores/project'
import CreateProjectModal from '../components/project/CreateProjectModal.vue'

const projectStore = useProjectStore()
const isModalOpen = ref(false)

onMounted(() => {
  projectStore.fetchProjects()
})
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-slate-900">Projects</h1>
        <p class="text-sm text-slate-500 mt-1">Manage your team workspace and board trackers</p>
      </div>
      <button
        @click="isModalOpen = true"
        class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2.5 rounded-xl text-sm transition shadow-sm"
      >
        + New Project
      </button>
    </div>

    <div v-if="projectStore.isLoading" class="text-slate-400 text-sm">Loading projects...</div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="project in projectStore.projects"
        :key="project.id"
        class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition flex flex-col justify-between"
      >
        <div>
          <div class="flex justify-between items-center mb-3">
            <span class="text-xs font-bold px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-lg uppercase tracking-wider">
              {{ project.key }}
            </span>
            <span class="text-xs text-slate-400">{{ project.members?.length || 1 }} members</span>
          </div>
          <h3 class="text-lg font-bold text-slate-900 mb-1">{{ project.name }}</h3>
          <p class="text-sm text-slate-500 line-clamp-2 mb-6">
            {{ project.description || 'No description provided.' }}
          </p>
        </div>

        <RouterLink
          :to="`/app/projects/${project.id}/board`"
          class="inline-flex items-center justify-between text-sm font-semibold text-indigo-600 hover:text-indigo-800 pt-4 border-t border-slate-100"
        >
          <span>Open Kanban Board</span>
          <span>&rarr;</span>
        </RouterLink>
      </div>
    </div>

    <CreateProjectModal :is-open="isModalOpen" @close="isModalOpen = false" />
  </div>
</template>