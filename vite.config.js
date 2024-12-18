import { defineConfig } from 'vite'
import symfonyPlugin from 'vite-plugin-symfony'

export default defineConfig({
    plugins: [
        symfonyPlugin(),
    ],
    build: {
        rollupOptions: {
            input: {
                app: 'assets/app.js',
                registration: 'assets/scripts/pages/registration.ts',
                login: 'assets/scripts/pages/login.ts',
                sections: 'assets/scripts/pages/sections.ts',
            },
        },
        minify: false,
    },
    server: {
        strictPort: true,
        port: 5133,
    },
})