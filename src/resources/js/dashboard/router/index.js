import { createRouter, createWebHistory } from 'vue-router';
import { authApi } from '@/api/auth';

const routes = [
    {
        path: '/login',
        name: 'Login',
        component: () => import('@/dashboard/views/Login.vue'),
        meta: { guest: true },
    },
    {
        path: '/register',
        name: 'Register',
        component: () => import('@/dashboard/views/Register.vue'),
        meta: { guest: true },
    },
    {
        path: '/',
        component: () => import('@/dashboard/layouts/DashboardLayout.vue'),
        meta: { requiresAuth: true },
        children: [
            {
                path: '',
                name: 'Dashboard',
                component: () => import('@/dashboard/views/Dashboard.vue'),
            },
            {
                path: 'clients',
                name: 'Clients',
                component: () => import('@/dashboard/views/Clients.vue'),
            },
            {
                path: 'clients/:id',
                name: 'ClientDetail',
                component: () => import('@/dashboard/views/ClientDetail.vue'),
            },
            {
                path: 'appointments',
                name: 'Appointments',
                component: () => import('@/dashboard/views/Appointments.vue'),
            },
            {
                path: 'appointments/create',
                name: 'CreateAppointment',
                component: () => import('@/dashboard/views/AppointmentForm.vue'),
            },
            {
                path: 'appointments/:id/edit',
                name: 'EditAppointment',
                component: () => import('@/dashboard/views/AppointmentForm.vue'),
            },
            {
                path: 'reports',
                name: 'Reports',
                component: () => import('@/dashboard/views/Reports.vue'),
            },
            {
                path: 'subscription',
                name: 'Subscription',
                component: () => import('@/dashboard/views/Subscription.vue'),
            },
            {
                path: 'profile',
                name: 'Profile',
                component: () => import('@/dashboard/views/Profile.vue'),
            },
        ],
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'NotFound',
        component: () => import('@/dashboard/views/NotFound.vue'),
    },
];

const router = createRouter({
    history: createWebHistory('/app'),
    routes,
});

router.beforeEach((to, from, next) => {
    const isAuthenticated = authApi.isAuthenticated();

    if (to.meta.requiresAuth && !isAuthenticated) {
        next('/app/login');
    } else if (to.meta.guest && isAuthenticated) {
        next('/app');
    } else {
        next();
    }
});

export default router;
