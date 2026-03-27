import api from '@/api';

export const clientDashboardApi = {
    async getDashboard() {
        const response = await api.get('/client/dashboard');
        return response.data;
    },

    async getMasters() {
        const response = await api.get('/client/masters');
        return response.data;
    },

    async getAppointmentHistory(params = {}) {
        const response = await api.get('/client/appointments', { params });
        return response.data;
    },
};
