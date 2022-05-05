import { createWebHistory, createRouter } from "vue-router";
import Main from "./components/Main";
import Test from "./components/Test";
import Words from "./components/Words";

const routes = [
    {
        path: '/',
        component: Main,
    },
    {
        path: '/words',
        component: Words,
    },
];

const router = createRouter({
    history: createWebHistory(), routes,
});

export default router;
