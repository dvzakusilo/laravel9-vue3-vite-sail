import './bootstrap';
import { createApp } from 'vue';
import { createI18n } from "vue-i18n";


import {router, components as includedComponents, App} from './router';

//import FontAwesomeIcon from './fontawesome';

// Локализация
import messages from "@intlify/unplugin-vue-i18n/messages";

const i18n = createI18n({
    legacy: false,
    globalInjection: true,
    locale: "ru",
    fallbackLocale: "ru",
    availableLocales: ["ru", "en"],
    messages: messages,
});

const app = createApp(App);
app.use(router);





Object.entries(includedComponents).forEach(([path, definition]) => {
    // Get name of component, based on filename
    // "./components/Fruits.vue" will become "Fruits"
    const componentName = path.split('/').pop().replace(/\.\w+$/, '')

    // Register component on this Vue instance
    app.component(componentName, definition.default)
})
router.isReady().then(() => {
    app
        .use(i18n)
        // .component('FontAwesomeIcon', FontAwesomeIcon)
        .mount('#app');
})
