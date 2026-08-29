import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import apiClient from '../services/api'
import type { Issue, IssueStatus, CreateIssuePayload, UpdateIssuePayload } from '../types/issue'

export const useIssueStore = defineStore('issue', () => {
  const issues = ref<Issue[]>([])
  const isLoading = ref(false)

  // Computed columns for Kanban Board
  const columns = computed(() => {
    const statuses: IssueStatus[] = ['backlog', 'todo', 'in_progress', 'review', 'done']
    return statuses.reduce((acc, status) => {
      acc[status] = issues.value.filter((issue) => issue.status === status)
      return acc
    }, {} as Record<IssueStatus, Issue[]>)
  })

  async function fetchProjectIssues(projectId: number, filters: Record<string, string> = {}) {
    isLoading.value = true
    try {
      const response = await apiClient.get<{ data: Issue[] }>(`/projects/${projectId}/issues`, {
        params: filters,
      })
      issues.value = response.data.data
    } finally {
      isLoading.value = false
    }
  }

  async function createIssue(projectId: number, payload: CreateIssuePayload) {
    const response = await apiClient.post<{ data: Issue }>(`/projects/${projectId}/issues`, payload)
    issues.value.unshift(response.data.data)
    return response.data.data
  }

  // Optimistic UI Update for Kanban Drag & Drop
  async function updateIssueStatus(issueId: number, newStatus: IssueStatus) {
    const issue = issues.value.find((i) => i.id === issueId)
    if (!issue) return

    const originalStatus = issue.status
    // Optimistic update
    issue.status = newStatus

    try {
      // Sync with API
      await apiClient.patch<{ data: Issue }>(`/issues/${issueId}`, { status: newStatus })
    } catch (error) {
      // Rollback on failure
      issue.status = originalStatus
      throw error
    }
  }

  return { issues, columns, isLoading, fetchProjectIssues, createIssue, updateIssueStatus }
})