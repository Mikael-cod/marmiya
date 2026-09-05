import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/css/crime-type-report.css', 'resources/css/education-age-report.css', 'resources/css/sentence-type-report.css', 'resources/css/new-intake-report.css', 'resources/css/released-report.css', 'resources/css/under-18-report.css', 'resources/css/parole-released-report.css', 'resources/css/children-with-mother-report.css', 'resources/css/death-sentenced-report.css', 'resources/css/recidivist-report.css', 'resources/css/prisoner-documents-export.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
                bunny('Noto Sans Ethiopic', {
                    weights: [400, 500, 600, 700],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
