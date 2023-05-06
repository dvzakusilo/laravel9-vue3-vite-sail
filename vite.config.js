// vite.config.js
import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'
import VueI18nPlugin from "@intlify/unplugin-vue-i18n/vite";

import { fileURLToPath} from "node:url";
import { resolve, dirname } from "node:path";


export default ({ mode }) => {
    // Load app-level env vars to node-level env vars.
    process.env = {...process.env, ...loadEnv(mode, process.cwd())};
    return defineConfig({
        plugins: [
            vue(),
            VueI18nPlugin({
                include: resolve(dirname(fileURLToPath(import.meta.url)), './resources/js/'+process.env.VITE_APP_JS_VERSION+'/locales/**'),
                strictMessage: false
            }),
            laravel([
                'resources/css/'+process.env.VITE_APP_JS_VERSION+'/app.css',
                'resources/js/app.js',
            ]),
        ],
    })
};
