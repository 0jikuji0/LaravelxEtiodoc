import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

// export default defineConfig({
//     plugins: [
//         laravel({
//             input: ['resources/css/app.css', 'resources/js/app.js'],
//             refresh: true,
//         }),
//         tailwindcss(),
//     ],
// });
export default defineConfig({
  plugins: [laravel({
    input: ['resources/css/app.css', 'resources/js/app.js'],
    refresh: true,
  })],
  server: {
    port: 5174, // ou un autre port libre
    hmr: {
      host: 'localhost',
    },
  },
});
