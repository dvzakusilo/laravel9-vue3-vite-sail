
import App from './App.vue';

import Home from './pages/Home.vue';
import Varnish from "./pages/Varnish.vue";

import messages from "@intlify/unplugin-vue-i18n/messages";

const routes = [
    { path: '/', name: 'main', title: messages.en.pages.main.title , component: Home},

    { path: '/varnish/', name: 'varnish', title: messages.en.pages.varnish.title , component: Varnish},

];

const pages = import.meta.globEager('./pages/*.vue');

const components = import.meta.globEager('./components/*.vue');
const blocks = import.meta.globEager('./blocks/*.vue');

localStorage.setItem('routes', JSON.stringify(routes));

let exportComponents = {...components, ...pages, ...blocks};

export {routes , exportComponents as components, App};
