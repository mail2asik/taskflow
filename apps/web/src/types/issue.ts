import type { User } from './user'

export type IssueStatus = 'backlog' | 'todo' | 'in_progress' | 'review' | 'done'
export type IssuePriority = 'low' | 'medium' | 'high' | 'urgent'

export interface Label {
  id: number
  name: string
  color_code: string
}

export interface Comment {
  id: number
  issue_id: number
  user_id: number
  body: string
  user: User
  created_at: string
  updated_at: string
}

export interface Attachment {
  id: number
  issue_id: number
  user_id: number
  file_name: string
  file_path: string
  mime_type: string
  file_size: number
  created_at: string
  updated_at: string
}

export interface Issue {
  id: number
  issue_key: string
  title: string
  description: string | null
  status: IssueStatus
  priority: IssuePriority
  due_date: string | null
  project_id: number
  assignee?: User
  reporter?: User
  labels?: Label[]
  comments?: Comment[]
  attachments?: Attachment[]
  created_at: string
  updated_at: string
}

export interface CreateIssuePayload {
  title: string
  description?: string
  status?: IssueStatus
  priority?: IssuePriority
  assignee_id?: number | null
  due_date?: string | null
  label_ids?: number[]
}

export interface UpdateIssuePayload {
  title?: string
  description?: string
  status?: IssueStatus
  priority?: IssuePriority
  assignee_id?: number | null
  due_date?: string | null
  label_ids?: number[]
}