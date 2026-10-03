import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

declare global {
  interface Window {
    Pusher: typeof Pusher
    Echo: Echo
  }
}

window.Pusher = Pusher

export const echo = new Echo({
  broadcaster: 'reverb',
  key: import.meta.env.VITE_REVERB_APP_KEY || 'gvhhvbruiifew5evxm0i',
  wsHost: import.meta.env.VITE_REVERB_HOST || 'api-taskflow.asik.local',
  wsPort: Number(import.meta.env.VITE_REVERB_PORT) || 80,
  wssPort: Number(import.meta.env.VITE_REVERB_PORT) || 80,
  forceTLS: false,
  enabledTransports: ['ws', 'wss'],
  authEndpoint: 'http://api-taskflow.asik.local/api/v1/broadcasting/auth',
  auth: {
    headers: {
      Authorization: `Bearer ${localStorage.getItem('access_token') || ''}`,
    },
  },
})