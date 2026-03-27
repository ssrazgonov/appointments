import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    {
        path: '/book/:slug',
        name: 'Booking',
        component: () => import('@/booking/views/BookingPage.vue'),
    },
    {
        path: '/book/:slug/success',
        name: 'BookingSuccess',
        component: () => import('@/booking/views/BookingSuccess.vue'),
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'BookingNotFound',
        component: () => import('@/booking/views/NotFound.vue'),
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
