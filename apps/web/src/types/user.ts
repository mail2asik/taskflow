// src/types/user.ts
export interface User {
  id: number
  name: string
  email: string
  email_verified_at: string | null
  created_at: string
}

export interface AuthData {
  user: User
  access_token: string
  token_type: string
}

export interface ActivatePayload {
  email: string
  code: string
}