<script setup lang="ts">
import { ref } from 'vue'
import BaseModal from '../ui/BaseModal.vue'
import { useProjectStore } from '../../stores/project'

const props = defineProps<{ isOpen: boolean }>()
const emit = defineEmits(['close', 'created'])

const projectStore = useProjectStore()
const name = ref('')
const key = ref('')
const description = ref('')
const isSubmitting = ref(false)

async function handleSubmit() {
  if (!name.value || !key.value) return
  isSubmitting.value = true
  try {
    await projectStore.createProject({
      name: name.value,
      key: key.value.toUpperCase(),
      description: description.value,
    })
    name.value = ''
    key.value = ''
    description.value = ''
    emit('created')
    emit('close')
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <BaseModal :is-open="isOpen" title="Create New Project" @close="$emit('close')">
    <form @submit.prevent="handleSubmit" class="space-y-4">
      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">Project Name</label>
        <input
          v-model="name"
          type="text"
          placeholder="e.g. Platform Redesign"
          required
          class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm"
        />
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">Project Key</label>
        <input
          v-model="key"
          type="text"
          placeholder="e.g. PR"
          maxlength="10"
          required
          class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm uppercase"
        />
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700 mb-1">Description</label>
        <textarea
          v-model="description"
          rows="3"
          placeholder="Short summary of this project..."
          class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm"
        ></textarea>
      </div>

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
          {{ isSubmitting ? 'Creating...' : 'Create Project' }}
        </button>
      </div>
    </form>
  </BaseModal>
</template>