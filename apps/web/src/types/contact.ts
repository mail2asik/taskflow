export interface ContactPayload {
  name: string
  email: string
  message: string
}

export interface ContactSubmission {
  id: number
  name: string
  email: string
  message: string
  created_at: string
}