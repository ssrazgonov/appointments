import api from '.';

export const appointmentsApi = {
    async getAppointments(params = {}) {
        const response = await api.get('/appointments', { params });
        return response.data;
    },

    async getTodayAppointments() {
        const response = await api.get('/appointments/today');
        return response.data;
    },

    async getUpcomingAppointments() {
        const response = await api.get('/appointments/upcoming');
        return response.data;
    },

    async getAppointment(id) {
        const response = await api.get(`/appointments/${id}`);
        return response.data;
    },

    async createAppointment(data) {
        const response = await api.post('/appointments', data);
        return response.data;
    },

    async updateAppointment(id, data) {
        const response = await api.put(`/appointments/${id}`, data);
        return response.data;
    },

    async deleteAppointment(id) {
        const response = await api.delete(`/appointments/${id}`);
        return response.data;
    },

    async cancelAppointment(id) {
        const response = await api.post(`/appointments/${id}/cancel`);
        return response.data;
    },

    async completeAppointment(id, data = {}) {
        const response = await api.post(`/appointments/${id}/complete`, data);
        return response.data;
    },
};
