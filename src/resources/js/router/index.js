import { createRouter, createWebHistory } from 'vue-router';
import { authService } from '@/api/auth';

const routes = [
    {
        path: '/login',
        name: 'Login',
        component: () => import('@/views/Login.vue'),
        meta: { guest: true },
    },
    {
        path: '/register',
        name: 'Register',
        component: () => import('@/views/Register.vue'),
        meta: { guest: true },
    },
    {
        path: '/',
        component: () => import('@/layouts/DashboardLayout.vue'),
        meta: { requiresAuth: true },
        children: [
            {
                path: '',
                name: 'Dashboard',
                component: () => import('@/views/Dashboard.vue'),
            },
            {
                path: 'clients',
                name: 'Clients',
                component: () => import('@/views/Clients.vue'),
            },
            {
                path: 'clients/:id',
                name: 'ClientDetail',
                component: () => import('@/views/ClientDetail.vue'),
            },
            {
                path: 'appointments',
                name: 'Appointments',
                component: () => import('@/views/Appointments.vue'),
            },
            {
                path: 'appointments/create',
                name: 'CreateAppointment',
                component: () => import('@/views/AppointmentForm.vue'),
            },
            {
                path: 'appointments/:id/edit',
                name: 'EditAppointment',
                component: () => import('@/views/AppointmentForm.vue'),
            },
            {
                path: 'reports',
                name: 'Reports',
                component: () => import('@/views/Reports.vue'),
            },
            {
                path: 'subscription',
                name: 'Subscription',
                component: () => import('@/views/Subscription.vue'),
            },
        ],
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'NotFound',
        component: () => import('@/views/NotFound.vue'),
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const isAuthenticated = authService.isAuthenticated();

    if (to.meta.requiresAuth && !isAuthenticated) {
        next('/login');
    } else if (to.meta.guest && isAuthenticated) {
        next('/');
    } else {
        next();
    }
});

export default router;
