import type { User } from './user'

export interface Project {
  id: number
  name: string
  key: string
  description: string | null
  is_archived: boolean
  owner?: User
  members?: User[]
  created_at: string
}

export interface CreateProjectPayload {
  name: string
  key: string
  description?: string
}