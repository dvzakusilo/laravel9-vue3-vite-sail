import {routes as r1, components as c1, App as a1} from './v1/router';
//import {routes as r2, components as c2, App as a2} from './v2/router';

/**
 * Роутинг выбирает пути в зависимости от активированной версии приложения в конфиге.
 */

import {createRouter, createWebHistory} from "vue-router/dist/vue-router";


const sVersion = import.meta.env.VITE_APP_JS_VERSION;

let  routes = [];
let exportComponents = {};
let exportApp = {};

switch (sVersion) {
    case 'v1':
        routes = r1;
        exportComponents = c1;
        exportApp = a1;
        break;

    // case 'v2':
    //     routes = r2;
    //     exportComponents = c2;
    //     exportApp = a2;
    //     break;
}

localStorage.setItem('routes', JSON.stringify(routes));
localStorage.setItem('visited', JSON.stringify([]));


const router = createRouter({
    history: createWebHistory(),
    mode: 'history',
    routes,
});
export {router, exportComponents as components, exportApp as App}
