import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css'
import '../css/app.css'
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import axios from 'axios';


//vue toastification
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";


import {ref} from 'vue';

//link
import { Link } from '@inertiajs/vue3';

//ziggy
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import {route} from 'ziggy-js';

//fontawesome
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

import { faMagnifyingGlass, faPhone, faLock, faWrench, faCircleCheck, faClock, faChartSimple, faDatabase, faGear, faComments, faUserGroup } from "@fortawesome/free-solid-svg-icons";
library.add(faMagnifyingGlass, faPhone, faLock, faWrench, faCircleCheck, faClock, faChartSimple, faDatabase, faGear, faComments, faUserGroup);

// const getThemeColor = async () => {
//     const response = await axios.get('/api/settings?key=theme_color');

//     if(response.data.status != 200){
//         console.log(response.data.message);
//     }

//     localStorage.setItem('themeColor',response.data.data[0].value);
//     return themeColor;
// }

// //theme color
// const themeColor = localStorage.getItem('themeColor') ?? getThemeColor();


createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
        return pages[`./Pages/${name}.vue`]
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        app.use(plugin);
        app.component('Link', Link);
        app.config.globalProperties.$themeColor = "#DD9E36";
        app.config.globalProperties.$appName = "Vintage";
        app.config.globalProperties.$route = route;
        app.provide('baseUrl', 'https://lovecar.autos/api');
        app.component("font-awesome-icon", FontAwesomeIcon);
        app.use(Toast);
        app.use(ref);
        app.use(ZiggyVue);
        app.mount(el);
    },
})
