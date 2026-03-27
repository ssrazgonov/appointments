import { createRouter, createWebHistory } from 'vue-router';
import { clientAuthApi } from '../api/auth';

const routes = [
    {
        path: '/login',
        name: 'ClientLogin',
        component: () => import('../views/Login.vue'),
        meta: { guest: true },
    },
    {
        path: '/register',
        name: 'ClientRegister',
        component: () => import('../views/Register.vue'),
        meta: { guest: true },
    },
    {
        path: '/',
        component: () => import('../layouts/ClientLayout.vue'),
        meta: { requiresAuth: true },
        children: [
            {
                path: '',
                name: 'ClientDashboard',
                component: () => import('../views/Dashboard.vue'),
            },
            {
                path: 'masters',
                name: 'ClientMasters',
                component: () => import('../views/Masters.vue'),
            },
            {
                path: 'masters/:id',
                name: 'MasterProfile',
                component: () => import('../views/MasterProfile.vue'),
            },
            {
                path: 'appointments',
                name: 'ClientAppointments',
                component: () => import('../views/Appointments.vue'),
            },
            {
                path: 'profile',
                name: 'ClientProfile',
                component: () => import('../views/Profile.vue'),
            },
        ],
    },
];

const router = createRouter({
    history: createWebHistory('/client'),
    routes,
});

router.beforeEach((to, from, next) => {
    const isAuthenticated = clientAuthApi.isAuthenticated();

    if (to.meta.requiresAuth && !isAuthenticated) {
        next({ path: '/login' });
    } else if (to.meta.guest && isAuthenticated) {
        next({ path: '/' });
    } else {
        next();
    }
});

export default router;
