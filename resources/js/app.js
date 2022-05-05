require('./bootstrap');

import { createApp } from 'vue';
import HelloWorld from './components/Main';
import router from './router';
import Index from "./components";

const app = createApp({
    components: {
        Index
    },
    router
});
app.use(router);
app.mount('#app')
