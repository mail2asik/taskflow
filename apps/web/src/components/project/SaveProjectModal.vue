<script setup lang="ts">
import { ref, watch, reactive } from 'vue'
import BaseModal from '../ui/BaseModal.vue'
import { useProjectStore } from '../../stores/project'
import type { Project, ProjectMember } from '../../types/project'

const props = defineProps<{
  isOpen: boolean
  projectToEdit?: Project | null
}>()

const emit = defineEmits(['close', 'saved'])

const projectStore = useProjectStore()

// Predefined Label Templates
const defaultLabels = [
  { name: 'Bug', color: '#EF4444' },
  { name: 'Feature', color: '#3B82F6' },
  { name: 'Enhancement', color: '#10B981' },
  { name: 'UI/UX', color: '#8B5CF6' },
  { name: 'Documentation', color: '#F59E0B' },
]

const form = reactive({
  name: '',
  key: '',
  description: '',
})

const selectedLabels = ref<Array<{ name: string; color: string }>>([...defaultLabels])
const selectedMembers = ref<Array<{ user_id: number; name: string; email: string; role: string }>>([])

// User search state
const searchQuery = ref('')
const searchResults = ref<ProjectMember[]>([])
const isSearching = ref(false)
const isSubmitting = ref(false)

watch(
  () => props.isOpen,
  (newVal) => {
    if (!newVal) return
    if (props.projectToEdit) {
      form.name = props.projectToEdit.name
      form.key = props.projectToEdit.key
      form.description = props.projectToEdit.description || ''
      selectedLabels.value = props.projectToEdit.labels.map((l) => ({
        name: l.name,
        color: l.color || (l as any).color_code || '#3B82F6',
      }))
      selectedMembers.value = (props.projectToEdit.members || []).map((m) => ({
        user_id: m.id,
        name: m.name,
        email: m.email,
        role: m.pivot?.role || 'member',
      }))
    } else {
      form.name = ''
      form.key = ''
      form.description = ''
      selectedLabels.value = [...defaultLabels]
      selectedMembers.value = []
    }
    searchQuery.value = ''
    searchResults.value = []
  }
)

function toggleLabel(template: { name: string; color: string }) {
  const index = selectedLabels.value.findIndex((l) => l.name.toLowerCase() === template.name.toLowerCase())
  if (index > -1) {
    selectedLabels.value.splice(index, 1)
  } else {
    selectedLabels.value.push({ ...template })
  }
}

function isLabelSelected(name: string): boolean {
  return selectedLabels.value.some((l) => l.name.toLowerCase() === name.toLowerCase())
}

let searchDebounceTimer: any = null
function onSearchInput() {
  clearTimeout(searchDebounceTimer)
  if (searchQuery.value.trim().length < 2) {
    searchResults.value = []
    return
  }
  isSearching.value = true
  searchDebounceTimer = setTimeout(async () => {
    try {
      const results = await projectStore.searchUsers(searchQuery.value)
      // Exclude already selected members
      searchResults.value = results.filter(
        (user) => !selectedMembers.value.some((m) => m.user_id === user.id)
      )
    } finally {
      isSearching.value = false
    }
  }, 300)
}

function addMember(user: ProjectMember) {
  selectedMembers.value.push({
    user_id: user.id,
    name: user.name,
    email: user.email,
    role: 'member',
  })
  searchQuery.value = ''
  searchResults.value = []
}

function removeMember(userId: number) {
  selectedMembers.value = selectedMembers.value.filter((m) => m.user_id !== userId)
}

async function handleSubmit() {
  if (!form.name || !form.key) return
  isSubmitting.value = true

  const payload = {
    name: form.name,
    key: form.key.toUpperCase(),
    description: form.description,
    labels: selectedLabels.value,
    members: selectedMembers.value.map((m) => ({ user_id: m.user_id, role: m.role })),
  }

  try {
    if (props.projectToEdit) {
      await projectStore.updateProject(props.projectToEdit.id, payload)
    } else {
      await projectStore.createProject(payload)
    }
    emit('saved')
    emit('close')
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <BaseModal
    :is-open="isOpen"
    :title="projectToEdit ? 'Edit Project' : 'Create New Project'"
    @close="$emit('close')"
  >
    <form @submit.prevent="handleSubmit" class="space-y-5">
      <!-- Basic Info -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="sm:col-span-2">
          <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Project Name</label>
          <input
            v-model="form.name"
            type="text"
            required
            placeholder="e.g. Core Engine"
            class="w-full px-3.5 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm"
          />
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Key</label>
          <input
            v-model="form.key"
            type="text"
            required
            maxlength="10"
            placeholder="e.g. CE"
            class="w-full px-3.5 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm uppercase font-mono"
          />
        </div>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Description</label>
        <textarea
          v-model="form.description"
          rows="2"
          placeholder="Brief description of project goals..."
          class="w-full px-3.5 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm"
        ></textarea>
      </div>

      <!-- Labels Selection -->
      <div>
        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Project Labels</label>
        <div class="flex flex-wrap gap-2">
          <button
            v-for="label in defaultLabels"
            :key="label.name"
            type="button"
            @click="toggleLabel(label)"
            class="px-3 py-1 rounded-lg text-xs font-semibold border transition flex items-center space-x-1.5"
            :class="
              isLabelSelected(label.name)
                ? 'bg-slate-900 text-white border-slate-900 shadow-sm'
                : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100'
            "
          >
            <span class="w-2.5 h-2.5 rounded-full inline-block" :style="{ backgroundColor: label.color }"></span>
            <span>{{ label.name }}</span>
          </button>
        </div>
      </div>

      <!-- Member Search & Selection -->
      <div>
        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Assign Members</label>
        <div class="relative">
          <input
            v-model="searchQuery"
            type="text"
            @input="onSearchInput"
            placeholder="Search users by name or email..."
            class="w-full px-3.5 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm"
          />
          <!-- Search Dropdown -->
          <div
            v-if="searchResults.length > 0"
            class="absolute z-20 left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-lg max-h-48 overflow-y-auto divide-y divide-slate-100"
          >
            <button
              v-for="user in searchResults"
              :key="user.id"
              type="button"
              @click="addMember(user)"
              class="w-full text-left px-3.5 py-2 hover:bg-indigo-50 flex justify-between items-center transition"
            >
              <div>
                <p class="text-sm font-semibold text-slate-800">{{ user.name }}</p>
                <p class="text-xs text-slate-400">{{ user.email }}</p>
              </div>
              <span class="text-xs text-indigo-600 font-semibold">+ Add</span>
            </button>
          </div>
        </div>

        <!-- Selected Members List -->
        <div v-if="selectedMembers.length > 0" class="mt-3 space-y-2 max-h-36 overflow-y-auto pr-1">
          <div
            v-for="member in selectedMembers"
            :key="member.user_id"
            class="flex items-center justify-between p-2 bg-slate-50 rounded-xl border border-slate-200/80 text-xs"
          >
            <div>
              <span class="font-bold text-slate-800">{{ member.name }}</span>
              <span class="text-slate-400 ml-1">({{ member.email }})</span>
            </div>
            <div class="flex items-center space-x-2">
              <select
                v-model="member.role"
                class="px-2 py-1 border border-slate-200 rounded-lg bg-white text-xs font-medium focus:outline-none"
              >
                <option value="member">Member</option>
                <option value="admin">Admin</option>
              </select>
              <button
                type="button"
                @click="removeMember(member.user_id)"
                class="text-slate-400 hover:text-red-600 font-bold px-1"
              >
                &times;
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
        <button
          type="button"
          @click="$emit('close')"
          class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 rounded-xl border border-slate-200"
        >
          Cancel
        </button>
        <button
          type="submit"
          :disabled="isSubmitting"
          class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl disabled:opacity-50 transition shadow-sm"
        >
          {{ isSubmitting ? 'Saving...' : projectToEdit ? 'Update Project' : 'Create Project' }}
        </button>
      </div>
    </form>
  </BaseModal>
</template>