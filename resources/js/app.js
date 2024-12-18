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

import { faMagnifyingGlass, faPhone, faLock, faWrench, faCircleCheck, faClock, faChartSimple, faDatabase, faGear, faComments, faUserGroup,faPlus,faCircleXmark,faChevronLeft } from "@fortawesome/free-solid-svg-icons";
library.add(faMagnifyingGlass, faPhone, faLock, faWrench, faCircleCheck, faClock, faChartSimple, faDatabase, faGear, faComments, faUserGroup,faPlus,faCircleXmark,faChevronLeft);

// Vuetify
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import '@mdi/font/css/materialdesignicons.css';

const vuetify = createVuetify({
  components,
  directives,
  icons: {
    defaultSet: 'mdi', // Default is Material Design Icons
  },
})


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
        app.use(vuetify);
        app.mount(el);
    },
})
