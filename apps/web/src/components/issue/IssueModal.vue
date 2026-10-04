<script setup lang="ts">
import { ref, watch } from 'vue'
import { useIssueStore } from '../../stores/issue'
import { useProjectStore } from '../../stores/project'
import type { Issue, IssueStatus, CreateIssuePayload, Comment, Attachment } from '../../types/issue'

const props = defineProps<{
  isOpen: boolean
  issue: Issue | null
  defaultStatus: IssueStatus
  projectId: number
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'save', payload: CreateIssuePayload): void
}>()

const issueStore = useIssueStore()
const projectStore = useProjectStore()

// State for Issue Fields
const form = ref<CreateIssuePayload>({
  title: '',
  description: '',
  status: 'backlog',
  priority: 'medium',
  assignee_id: null,
  label_ids: [],
})

// State for Comments and Attachments
const comments = ref<Comment[]>([])
const attachments = ref<Attachment[]>([])
const newCommentBody = ref('')
const isUploading = ref(false)
const downloadingId = ref<number | null>(null)
const isSubmittingComment = ref(false)
const isLoadingAttachments = ref(false)

watch(
  () => props.isOpen,
  async (newVal) => {
    if (newVal) {
      if (!projectStore.currentProject || projectStore.currentProject.id !== props.projectId) {
        await projectStore.fetchProjectById(props.projectId)
      }

      if (props.issue) {
        form.value = {
          title: props.issue.title,
          description: props.issue.description || '',
          status: props.issue.status,
          priority: props.issue.priority,
          assignee_id: props.issue.assignee?.id ?? null,
          label_ids: props.issue.labels ? props.issue.labels.map((l) => l.id) : [],
        }

        // Load Comments
        try {
          comments.value = await issueStore.fetchComments(props.issue.id)
        } catch {
          comments.value = []
        }

        // Load Attachments directly via endpoint
        isLoadingAttachments.value = true
        try {
          attachments.value = await issueStore.fetchAttachments(props.issue.id)
        } catch {
          attachments.value = props.issue.attachments || []
        } finally {
          isLoadingAttachments.value = false
        }
      } else {
        form.value = {
          title: '',
          description: '',
          status: props.defaultStatus || 'backlog',
          priority: 'medium',
          assignee_id: null,
          label_ids: [],
        }
        comments.value = []
        attachments.value = []
      }
      newCommentBody.value = ''
    }
  },
  { immediate: true }
)

// Comment Actions
async function handleAddComment() {
  if (!newCommentBody.value.trim() || !props.issue) return
  isSubmittingComment.value = true
  try {
    const comment = await issueStore.addComment(props.issue.id, newCommentBody.value)
    comments.value.unshift(comment)
    newCommentBody.value = ''
  } catch {
    alert('Failed to post comment.')
  } finally {
    isSubmittingComment.value = false
  }
}

async function handleDeleteComment(commentId: number) {
  if (!confirm('Are you sure you want to delete this comment?')) return
  try {
    await issueStore.deleteComment(commentId)
    comments.value = comments.value.filter((c) => c.id !== commentId)
  } catch {
    alert('Failed to delete comment.')
  }
}

// Attachment Actions
async function handleFileUpload(event: Event) {
  const target = event.target as HTMLInputElement
  if (!target.files || target.files.length === 0 || !props.issue) return

  const file = target.files[0]
  isUploading.value = true

  try {
    const uploadedAttachment = await issueStore.uploadAttachment(props.issue.id, file)
    attachments.value.unshift(uploadedAttachment)
    target.value = ''
  } catch {
    alert('Failed to upload file.')
  } finally {
    isUploading.value = false
  }
}

async function handleDownloadAttachment(attachment: Attachment) {
  downloadingId.value = attachment.id
  try {
    await issueStore.downloadAttachment(attachment.id, attachment.file_name)
  } catch {
    alert('Failed to download file.')
  } finally {
    downloadingId.value = null
  }
}

async function handleDeleteAttachment(attachmentId: number) {
  if (!confirm('Are you sure you want to remove this attachment?')) return
  try {
    await issueStore.deleteAttachment(attachmentId)
    attachments.value = attachments.value.filter((a) => a.id !== attachmentId)
  } catch {
    alert('Failed to delete attachment.')
  }
}

function toggleLabel(labelId: number) {
  if (!form.value.label_ids) form.value.label_ids = []
  const index = form.value.label_ids.indexOf(labelId)
  if (index === -1) {
    form.value.label_ids.push(labelId)
  } else {
    form.value.label_ids.splice(index, 1)
  }
}

function handleSubmit() {
  if (!form.value.title.trim()) return
  emit('save', { ...form.value })
}

function formatFileSize(bytes: number): string {
  if (bytes < 1024) return bytes + ' B'
  if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB'
  return (bytes / 1048576).toFixed(1) + ' MB'
}
</script>

<template>
  <div
    v-if="isOpen"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
  >
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col p-6 space-y-4">
      <!-- Modal Header -->
      <div class="flex justify-between items-center border-b pb-3">
        <h3 class="text-lg font-bold text-gray-800">
          {{ issue ? `Edit Issue (${issue.issue_key})` : 'Create New Issue' }}
        </h3>
        <button
          @click="$emit('close')"
          class="text-gray-400 hover:text-gray-600 text-xl font-bold"
        >
          &times;
        </button>
      </div>

      <!-- Scrollable Form Body -->
      <div class="overflow-y-auto space-y-4 pr-1 flex-1">
        <form id="issue-form" @submit.prevent="handleSubmit" class="space-y-4">
          <!-- Title -->
          <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Title</label>
            <input
              v-model="form.title"
              type="text"
              required
              placeholder="Issue title"
              class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
            />
          </div>

          <!-- Description -->
          <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Description</label>
            <textarea
              v-model="form.description"
              rows="3"
              placeholder="Add detailed description..."
              class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
            ></textarea>
          </div>

          <!-- Status & Priority Row -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Status</label>
              <select v-model="form.status" class="w-full px-3 py-2 border rounded-lg text-sm bg-white">
                <option value="backlog">Backlog</option>
                <option value="todo">To Do</option>
                <option value="in_progress">In Progress</option>
                <option value="review">Code Review</option>
                <option value="done">Done</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Priority</label>
              <select v-model="form.priority" class="w-full px-3 py-2 border rounded-lg text-sm bg-white">
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
                <option value="urgent">Urgent</option>
              </select>
            </div>
          </div>

          <!-- Assignee Selection -->
          <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Assignee</label>
            <select v-model="form.assignee_id" class="w-full px-3 py-2 border rounded-lg text-sm bg-white">
              <option :value="null">Unassigned</option>
              <option
                v-for="member in projectStore.currentProject?.members || []"
                :key="member.id"
                :value="member.id"
              >
                {{ member.name }}
              </option>
            </select>
          </div>

          <!-- Labels Selection -->
          <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Labels</label>
            <div class="flex flex-wrap gap-2 mt-1">
              <button
                type="button"
                v-for="label in projectStore.currentProject?.labels || []"
                :key="label.id"
                @click="label.id !== undefined && toggleLabel(label.id)"
                :disabled="label.id === undefined"
                class="px-2.5 py-1 rounded-full text-xs font-medium transition-all border"
                :style="{
                  backgroundColor: label.id !== undefined && form.label_ids?.includes(label.id) ? label.color : '#F3F4F6',
                  color: label.id !== undefined && form.label_ids?.includes(label.id) ? '#FFFFFF' : '#374151',
                  borderColor: label.color
                }"
              >
                {{ label.name }}
              </button>
            </div>
          </div>
        </form>

        <!-- Attachments & Comments Section (Visible when editing issue) -->
        <template v-if="issue">
          <hr class="my-6 border-gray-200" />

          <!-- Attachments -->
          <div>
            <div class="flex items-center justify-between mb-3">
              <h4 class="text-sm font-bold text-gray-800">Attachments</h4>
              <label class="inline-flex items-center px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-xs font-semibold cursor-pointer border border-indigo-200 transition-colors">
                <span>{{ isUploading ? 'Uploading...' : '+ Add Attachment' }}</span>
                <input type="file" @change="handleFileUpload" class="hidden" :disabled="isUploading" />
              </label>
            </div>

            <div v-if="isLoadingAttachments" class="text-xs text-gray-400 italic py-2">
              Loading attachments...
            </div>

            <div v-else class="space-y-2 mb-3">
              <div
                v-for="att in attachments"
                :key="att.id"
                class="flex items-center justify-between p-2.5 bg-gray-50 hover:bg-gray-100/80 transition-colors rounded-lg border text-xs"
              >
                <div class="flex items-center space-x-2 truncate">
                  <span class="font-medium text-gray-800 truncate">{{ att.file_name }}</span>
                  <span class="text-gray-400 text-[11px]">({{ formatFileSize(att.file_size) }})</span>
                </div>
                <div class="flex items-center space-x-3 flex-shrink-0">
                  <button
                    @click="handleDownloadAttachment(att)"
                    type="button"
                    :disabled="downloadingId === att.id"
                    class="text-indigo-600 hover:text-indigo-800 font-semibold disabled:opacity-50"
                  >
                    {{ downloadingId === att.id ? 'Downloading...' : 'Download' }}
                  </button>
                  <button
                    @click="handleDeleteAttachment(att.id)"
                    type="button"
                    class="text-red-500 hover:text-red-700 font-semibold"
                  >
                    Delete
                  </button>
                </div>
              </div>

              <p v-if="attachments.length === 0" class="text-xs text-gray-400 italic">No attachments added.</p>
            </div>
          </div>

          <hr class="my-6 border-gray-200" />

          <!-- Comments -->
          <div>
            <h4 class="text-sm font-bold text-gray-800 mb-3">Comments</h4>
            
            <!-- Comment Input -->
            <div class="space-y-2 mb-4">
              <textarea
                v-model="newCommentBody"
                rows="2"
                placeholder="Write a comment..."
                class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
              ></textarea>
              <div class="flex justify-end">
                <button
                  type="button"
                  @click="handleAddComment"
                  :disabled="isSubmittingComment || !newCommentBody.trim()"
                  class="px-3 py-1.5 bg-indigo-600 text-white text-xs font-semibold rounded-lg hover:bg-indigo-700 disabled:opacity-50"
                >
                  Post Comment
                </button>
              </div>
            </div>

            <!-- Comments List -->
            <div class="space-y-3">
              <div
                v-for="comment in comments"
                :key="comment.id"
                class="p-3 bg-gray-50 rounded-lg border text-xs space-y-1"
              >
                <div class="flex justify-between items-center text-gray-500">
                  <span class="font-bold text-gray-700">{{ comment.user?.name || 'User' }}</span>
                  <div class="flex items-center space-x-2">
                    <span>{{ new Date(comment.created_at).toLocaleString() }}</span>
                    <button
                      @click="handleDeleteComment(comment.id)"
                      class="text-red-500 hover:text-red-700 font-semibold"
                    >
                      Delete
                    </button>
                  </div>
                </div>
                <p class="text-gray-800 whitespace-pre-wrap">{{ comment.body }}</p>
              </div>
              <p v-if="comments.length === 0" class="text-xs text-gray-400 italic">No comments yet.</p>
            </div>
          </div>
        </template>
      </div>

      <!-- Modal Footer -->
      <div class="flex justify-end space-x-2 pt-3 border-t">
        <button
          type="button"
          @click="$emit('close')"
          class="px-4 py-2 border rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50"
        >
          Cancel
        </button>
        <button
          type="submit"
          form="issue-form"
          class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 shadow-sm"
        >
          {{ issue ? 'Update Issue' : 'Create Issue' }}
        </button>
      </div>
    </div>
  </div>
</template>