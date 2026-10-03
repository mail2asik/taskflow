<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { RouterLink } from 'vue-router'
import { useProjectStore } from '../stores/project'
import SaveProjectModal from '../components/project/SaveProjectModal.vue'
import type { Project } from '../types/project'

const projectStore = useProjectStore()
const isModalOpen = ref(false)
const selectedProject = ref<Project | null>(null)

onMounted(() => {
  projectStore.fetchProjects()
})

function openCreateModal() {
  selectedProject.value = null
  isModalOpen.value = true
}

function openEditModal(project: Project) {
  selectedProject.value = project
  isModalOpen.value = true
}

async function handleDelete(project: Project) {
  if (confirm(`Are you sure you want to delete "${project.name}"?`)) {
    await projectStore.deleteProject(project.id)
  }
}
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-slate-900">Projects</h1>
        <p class="text-sm text-slate-500 mt-1">Manage your team workspace and board trackers</p>
      </div>
      <button
        @click="openCreateModal"
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
            <div class="flex items-center space-x-2">
              <button @click="openEditModal(project)" class="text-xs text-slate-500 hover:text-indigo-600 font-medium">
                Edit
              </button>
              <button @click="handleDelete(project)" class="text-xs text-slate-400 hover:text-red-600 font-medium">
                Delete
              </button>
            </div>
          </div>
          
          <h3 class="text-lg font-bold text-slate-900 mb-1">{{ project.name }}</h3>
          <p class="text-sm text-slate-500 line-clamp-2 mb-4">
            {{ project.description || 'No description provided.' }}
          </p>

          <!-- Label Badges -->
          <div v-if="project.labels && project.labels.length > 0" class="flex flex-wrap gap-1.5 mb-4">
            <span
              v-for="label in project.labels"
              :key="label.id || label.name"
              class="text-[10px] font-bold px-2 py-0.5 rounded-md text-white"
              :style="{ backgroundColor: label.color || (label as any).color_code || '#64748B' }"
            >
              {{ label.name }}
            </span>
          </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
          <span class="text-xs text-slate-400 font-medium">{{ project.members?.length || 1 }} members</span>
          <RouterLink
            :to="`/app/projects/${project.id}/board`"
            class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 flex items-center space-x-1"
          >
            <span>Open Board</span>
            <span>&rarr;</span>
          </RouterLink>
        </div>
      </div>
    </div>

    <!-- Unified Save/Edit Modal -->
    <SaveProjectModal
      :is-open="isModalOpen"
      :project-to-edit="selectedProject"
      @close="isModalOpen = false"
      @saved="projectStore.fetchProjects()"
    />
  </div>
</template>