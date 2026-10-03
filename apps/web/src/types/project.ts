export interface Label {
  id?: number
  name: string
  color: string
}

export interface ProjectMember {
  id: number
  name: string
  email: string
  pivot?: {
    role: 'owner' | 'admin' | 'member'
  }
}

export interface Project {
  id: number
  name: string
  key: string
  description?: string
  owner_id: number
  members: ProjectMember[]
  labels: Label[]
  created_at?: string
}

export interface SaveProjectPayload {
  name: string
  key: string
  description?: string
  labels?: Array<{ name: string; color: string }>
  members?: Array<{ user_id: number; role: string }>
}