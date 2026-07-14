import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

const backendUrl = process.env.VITE_BACKEND_URL || 'http://127.0.0.1:8000';

export default defineConfig({
    server: {
        host: '127.0.0.1',
        port: 4200,
        strictPort: true,
        proxy: {
            '/api': {
                target: backendUrl,
                changeOrigin: true,
            },
        },
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
