import { defineConfig } from 'vite';
import { resolve } from 'path';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/logs.css',
                'resources/css/summary.css',
                'resources/css/print-elements.css',
                'resources/js/app.js',
                'resources/js/callsigns.js',
                'resources/js/logs.js'
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '$': 'jQuery',
            $images: resolve('./public/images')
        }
    }
});
