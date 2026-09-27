export function isValidEmail(email: string): boolean {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(email)
}

export function validateRegistration(form: { name: string; email: string; password: string; password_confirmation: string }) {
  const errors: Record<string, string> = {}

  if (!form.name.trim()) {
    errors.name = 'Full name is required.'
  }

  if (!form.email.trim()) {
    errors.email = 'Email address is required.'
  } else if (!isValidEmail(form.email)) {
    errors.email = 'Please enter a valid email address.'
  }

  if (!form.password) {
    errors.password = 'Password is required.'
  } else if (form.password.length < 8) {
    errors.password = 'Password must be at least 8 characters long.'
  }

  if (form.password !== form.password_confirmation) {
    errors.password_confirmation = 'Passwords do not match.'
  }

  return errors
}