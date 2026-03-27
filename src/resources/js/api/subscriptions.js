import api from '.';

export const subscriptionsApi = {
    async getPlans() {
        const response = await api.get('/subscriptions/plans');
        return response.data.plans;
    },

    async getCurrentSubscription() {
        const response = await api.get('/subscriptions/current');
        return response.data;
    },

    async hasActiveSubscription() {
        const response = await api.get('/subscriptions/has-active');
        return response.data.has_active_subscription;
    },
};
