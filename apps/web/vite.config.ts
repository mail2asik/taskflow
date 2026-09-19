import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vite.dev/config/
export default defineConfig({
  plugins: [
    vue()
  ],
  server: {
    host: '0.0.0.0', // Expose dev server to container network
    port: 5173,
    watch: {
      usePolling: true, // Required for file change detection inside Docker volumes
    },
    allowedHosts: ['web-taskflow.asik.local'], // Allow access from host machine
  },
})
