<script setup lang="ts">
import type { Issue } from '../../types/issue'

defineProps<{
  issue: Issue
}>()
</script>

<template>
  <div
    draggable="true"
    @dragstart="$emit('dragstart', issue)"
    class="p-4 mb-3 bg-white rounded-lg shadow-sm border border-gray-200 cursor-grab active:cursor-grabbing hover:shadow-md transition-shadow"
  >
    <div class="flex items-center justify-between mb-2">
      <span class="text-xs font-semibold text-gray-500">{{ issue.issue_key }}</span>
      <span
        class="text-xs px-2 py-0.5 rounded font-medium uppercase"
        :class="{
          'bg-red-100 text-red-700': issue.priority === 'urgent',
          'bg-orange-100 text-orange-700': issue.priority === 'high',
          'bg-blue-100 text-blue-700': issue.priority === 'medium',
          'bg-gray-100 text-gray-700': issue.priority === 'low',
        }"
      >
        {{ issue.priority }}
      </span>
    </div>
    <h4 class="text-sm font-medium text-gray-900 mb-2">{{ issue.title }}</h4>
    <div class="flex items-center justify-between text-xs text-gray-500">
      <div class="flex items-center space-x-1">
        <span v-for="label in issue.labels" :key="label.id" class="px-1.5 py-0.5 rounded text-[10px] text-white" :style="{ backgroundColor: label.color_code }">
          {{ label.name }}
        </span>
      </div>
      <span v-if="issue.assignee" class="font-medium text-gray-700">
        {{ issue.assignee.name }}
      </span>
    </div>
  </div>
</template>