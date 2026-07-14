import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import fs from 'node:fs';

const backendUrl = process.env.VITE_BACKEND_URL || 'http://127.0.0.1:8000';

function angularAuthPages() {
    return {
        name: 'salon-angular-auth-pages',
        configureServer(server) {
            server.middlewares.use(async (request, response, next) => {
                const requestUrl = request.url ?? '/';
                const path = requestUrl.split('?')[0];
                const angularPages = ['/login', '/register', '/termini/create'];
                const viteOrApiPrefixes = [
                    '/@vite',
                    '/resources',
                    '/node_modules',
                    '/api',
                    '/csrf-token',
                    '/angular-login',
                    '/angular-register',
                    '/angular-session',
                ];

                if (request.method === 'GET' && angularPages.includes(path ?? '')) {
                    const html = fs.readFileSync('index.html', 'utf-8');
                    const transformed = await server.transformIndexHtml(requestUrl, html);

                    response.statusCode = 200;
                    response.setHeader('Content-Type', 'text/html');
                    response.end(transformed);
                    return;
                }

                const shouldRedirectToPhp = request.method === 'GET'
                    && path
                    && ! path.includes('.')
                    && ! viteOrApiPrefixes.some((prefix) => path.startsWith(prefix));

                if (shouldRedirectToPhp) {
                    response.statusCode = 302;
                    response.setHeader('Location', `${backendUrl}${requestUrl}`);
                    response.end();
                    return;
                }

                next();
            });
        },
    };
}

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
            '/csrf-token': {
                target: backendUrl,
                changeOrigin: true,
            },
            '/angular-login': {
                target: backendUrl,
                changeOrigin: true,
            },
            '/angular-register': {
                target: backendUrl,
                changeOrigin: true,
            },
            '/angular-session': {
                target: backendUrl,
                changeOrigin: true,
            },
        },
    },
    plugins: [
        angularAuthPages(),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
