import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import apiClient from '../services/api'
import type { Issue, IssueStatus, CreateIssuePayload, UpdateIssuePayload, Comment, Attachment } from '../types/issue'

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

  async function updateIssue(issueId: number, payload: UpdateIssuePayload) {
    const response = await apiClient.patch<{ data: Issue }>(`/issues/${issueId}`, payload)
    const updatedIssue = response.data.data
    const index = issues.value.findIndex((i) => i.id === issueId)
    if (index !== -1) {
      issues.value[index] = updatedIssue
    }
    return updatedIssue
  }

  async function deleteIssue(issueId: number) {
    await apiClient.delete(`/issues/${issueId}`)
    issues.value = issues.value.filter((i) => i.id !== issueId)
  }

  async function updateIssueStatus(issueId: number, newStatus: IssueStatus) {
    const issue = issues.value.find((i) => i.id === issueId)
    if (!issue) return

    const originalStatus = issue.status
    issue.status = newStatus

    try {
      await apiClient.patch<{ data: Issue }>(`/issues/${issueId}`, { status: newStatus })
    } catch (error) {
      issue.status = originalStatus
      throw error
    }
  }

  // --- Comments API Methods ---

  async function fetchComments(issueId: number): Promise<Comment[]> {
    const response = await apiClient.get<{ data: Comment[] }>(`/issues/${issueId}/comments`)
    return response.data.data
  }

  async function addComment(issueId: number, body: string): Promise<Comment> {
    const response = await apiClient.post<{ data: Comment }>(`/issues/${issueId}/comments`, { body })
    return response.data.data
  }

  async function deleteComment(commentId: number): Promise<void> {
    await apiClient.delete(`/comments/${commentId}`)
  }

  // --- Attachments API Methods ---

  async function fetchAttachments(issueId: number): Promise<Attachment[]> {
    const response = await apiClient.get<{ data: Attachment[] }>(`/issues/${issueId}/attachments`)
    return response.data.data
  }

  async function uploadAttachment(issueId: number, file: File): Promise<Attachment> {
    const formData = new FormData()
    formData.append('file', file)

    const response = await apiClient.post<{ data: Attachment }>(`/issues/${issueId}/attachments`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })
    return response.data.data
  }

  async function deleteAttachment(attachmentId: number): Promise<void> {
    await apiClient.delete(`/attachments/${attachmentId}`)
  }

  async function downloadAttachment(attachmentId: number, fileName: string): Promise<void> {
    const response = await apiClient.get(`/attachments/${attachmentId}/download`, {
      responseType: 'blob',
    })

    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', fileName)
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)
  }

  return { 
    issues, 
    columns, 
    isLoading, 
    fetchProjectIssues, 
    createIssue, 
    updateIssue, 
    deleteIssue, 
    updateIssueStatus,
    fetchComments,
    addComment,
    deleteComment,
    fetchAttachments,
    uploadAttachment,
    deleteAttachment,
    downloadAttachment,
  }
})