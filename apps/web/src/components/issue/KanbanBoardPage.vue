<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useIssueStore } from '../../stores/issue'
import IssueCard from '../../components/issue/IssueCard.vue'
import type { Issue, IssueStatus } from '../../types/issue'

const route = useRoute()
const issueStore = useIssueStore()
const projectId = Number(route.params.projectId)
const draggedIssue = ref<Issue | null>(null)

const columns: { label: string; key: IssueStatus }[] = [
  { label: 'Backlog', key: 'backlog' },
  { label: 'To Do', key: 'todo' },
  { label: 'In Progress', key: 'in_progress' },
  { label: 'Code Review', key: 'review' },
  { label: 'Done', key: 'done' },
]

onMounted(() => {
  issueStore.fetchProjectIssues(projectId)
})

function onDragStart(issue: Issue) {
  draggedIssue.value = issue
}

async function onDrop(targetStatus: IssueStatus) {
  if (!draggedIssue.value || draggedIssue.value.status === targetStatus) return
  const issueId = draggedIssue.value.id
  draggedIssue.value = null
  
  try {
    await issueStore.updateIssueStatus(issueId, targetStatus)
  } catch {
    alert('Failed to update issue status. Rollback applied.')
  }
}
</script>

<template>
  <div class="p-6">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Project Board</h2>
    
    <div class="flex space-x-4 overflow-x-auto pb-4">
      <div
        v-for="column in columns"
        :key="column.key"
        @dragover.prevent
        @drop="onDrop(column.key)"
        class="w-72 flex-shrink-0 bg-gray-50 p-4 rounded-xl border border-gray-200 min-h-[600px]"
      >
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wider">{{ column.label }}</h3>
          <span class="bg-gray-200 text-gray-700 text-xs px-2 py-0.5 rounded-full font-bold">
            {{ issueStore.columns[column.key]?.length || 0 }}
          </span>
        </div>

        <div class="space-y-3">
          <IssueCard
            v-for="issue in issueStore.columns[column.key]"
            :key="issue.id"
            :issue="issue"
            @dragstart="onDragStart"
          />
        </div>
      </div>
    </div>
  </div>
</template>