import { defineStore } from 'pinia'
import { ref } from 'vue'
import apiClient from '../services/api'
import type { Project, SaveProjectPayload, ProjectMember } from '../types/project'

export const useProjectStore = defineStore('project', () => {
  // State
  const projects = ref<Project[]>([])
  const currentProject = ref<Project | null>(null)
  const isLoading = ref(false)

  /**
   * Fetch all projects for the authenticated user
   */
  async function fetchProjects() {
    isLoading.value = true
    try {
      const response = await apiClient.get<{ data: Project[] }>('/projects')
      projects.value = response.data.data
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Fetch a single project by ID along with its members, labels, and details
   * @param id - The ID of the project to fetch
   */
  async function fetchProjectById(id: number) {
    isLoading.value = true
    try {
      const response = await apiClient.get<{ data: Project }>(`/projects/${id}`)
      currentProject.value = response.data.data
      return response.data.data
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Create a new project
   * @param payload - The project name, key, and description
   */
  async function createProject(payload: SaveProjectPayload) {
    const response = await apiClient.post<{ data: Project }>('/projects', payload)
    projects.value.unshift(response.data.data)
    return response.data.data
  }

  /**
   * Update an existing project by ID
   * @param id - The ID of the project to update
   * @param payload - Updated project fields
   */
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

  /**
   * Delete a project by ID
   * @param id - The ID of the project to remove
   */
  async function deleteProject(id: number) {
    await apiClient.delete(`/projects/${id}`)
    projects.value = projects.value.filter((p) => p.id !== id)
    if (currentProject.value?.id === id) {
      currentProject.value = null
    }
  }

  /**
   * Search users by keyword for project member assignment
   * @param query - Search text (minimum 2 characters)
   */
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
    fetchProjectById,
    createProject,
    updateProject,
    deleteProject,
    searchUsers,
  }
})