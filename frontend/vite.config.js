import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [react()],
  server: {
    port: 3000, // Frontend chạy ở cổng 3000
    proxy: {
      '/api': {
        target: 'http://localhost:8000', // Trỏ tới backend PHP
        changeOrigin: true,
      }
    }
  }
})
