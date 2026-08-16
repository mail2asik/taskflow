import { defineStore } from 'pinia'
import { ref } from 'vue'
import apiClient from '../services/api'
import type { Project, CreateProjectPayload } from '../types/project'

export const useProjectStore = defineStore('project', () => {
  const projects = ref<Project[]>([])
  const currentProject = ref<Project | null>(null)
  const isLoading = ref(false)

  async function fetchProjects() {
    isLoading.value = true
    try {
      const response = await apiClient.get<{ data: Project[] }>('/projects')
      projects.value = response.data.data
    } finally {
      isLoading.value = false
    }
  }

  async function createProject(payload: CreateProjectPayload) {
    const response = await apiClient.post<{ data: Project }>('/projects', payload)
    projects.value.unshift(response.data.data)
    return response.data.data
  }

  async function fetchProject(id: number) {
    isLoading.value = true
    try {
      const response = await apiClient.get<{ data: Project }>(`/projects/${id}`)
      currentProject.value = response.data.data
      return response.data.data
    } finally {
      isLoading.value = false
    }
  }

  return { projects, currentProject, isLoading, fetchProjects, createProject, fetchProject }
})