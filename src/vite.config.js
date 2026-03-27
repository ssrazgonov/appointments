import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/landing/main.js',
                'resources/js/dashboard/main.js',
                'resources/js/booking/main.js',
                'resources/js/client/main.js',
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: 'localhost',
        },
        origin: 'http://localhost:8080',
    },
    build: {
        manifest: true,
        rollupOptions: {
            input: {
                landing: 'resources/js/landing/main.js',
                dashboard: 'resources/js/dashboard/main.js',
                booking: 'resources/js/booking/main.js',
                client: 'resources/js/client/main.js',
            },
            output: {
                entryFileNames: 'assets/[name]-[hash].js',
                chunkFileNames: 'assets/[name]-[hash].js',
                assetFileNames: 'assets/[name]-[hash].[ext]',
            },
        },
    },
});
