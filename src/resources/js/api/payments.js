import api from '.';

export const paymentsApi = {
    async getPayments(params = {}) {
        const response = await api.get('/payments', { params });
        return response.data;
    },

    async createPayment(planId) {
        const response = await api.post('/payments/create', { plan_id: planId });
        return response.data;
    },

    async getPayment(id) {
        const response = await api.get(`/payments/${id}`);
        return response.data;
    },
};
