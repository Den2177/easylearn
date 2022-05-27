import { createWebHistory, createRouter } from "vue-router";
import Main from "./components/Main";
import Words from "./components/Words";

const routes = [
    {
        path: '/',
        name: 'main.index',
        component: Main,
    },
    {
        path: '/words',
        name: 'words.index',
        component: Words,
    },
];

const router = createRouter({
    history: createWebHistory(), routes,
});

export default router;
