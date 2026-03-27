import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    {
        path: '/',
        name: 'Home',
        component: () => import('@/landing/views/Home.vue'),
    },
    {
        path: '/features',
        name: 'Features',
        component: () => import('@/landing/views/Features.vue'),
    },
    {
        path: '/pricing',
        name: 'Pricing',
        component: () => import('@/landing/views/Pricing.vue'),
    },
    {
        path: '/login',
        name: 'Login',
        component: () => import('@/dashboard/views/Login.vue'),
    },
    {
        path: '/register',
        name: 'Register',
        component: () => import('@/dashboard/views/Register.vue'),
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition;
        }
        return { top: 0 };
    },
});

export default router;
