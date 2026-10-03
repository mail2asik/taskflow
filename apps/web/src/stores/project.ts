import { defineStore } from 'pinia'
import { ref } from 'vue'
import apiClient from '../services/api'
import type { Project, SaveProjectPayload, ProjectMember } from '../types/project'

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

  async function createProject(payload: SaveProjectPayload) {
    const response = await apiClient.post<{ data: Project }>('/projects', payload)
    projects.value.unshift(response.data.data)
    return response.data.data
  }

  async function updateProject(id: number, payload: SaveProjectPayload) {
    const response = await apiClient.put<{ data: Project }>(`/projects/${id}`, payload)
    const index = projects.value.findIndex((p) => p.id === id)
    if (index !== -1) {
      projects.value[index] = response.data.data
    }
    if (currentProject.value?.id === id) {
      currentProject.value = response.data.data
    }
    return response.data.data
  }

  async function deleteProject(id: number) {
    await apiClient.delete(`/projects/${id}`)
    projects.value = projects.value.filter((p) => p.id !== id)
  }

  async function searchUsers(query: string): Promise<ProjectMember[]> {
    if (!query || query.length < 2) return []
    const response = await apiClient.get<{ data: ProjectMember[] }>(`/users/search?q=${encodeURIComponent(query)}`)
    return response.data.data || []
  }

  return {
    projects,
    currentProject,
    isLoading,
    fetchProjects,
    createProject,
    updateProject,
    deleteProject,
    searchUsers,
  }
})