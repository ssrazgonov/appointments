import api from './index';

export const clientService = {
    async getClients(params = {}) {
        const response = await api.get('/clients', { params });
        return response.data;
    },

    async getClient(id) {
        const response = await api.get(`/clients/${id}`);
        return response.data;
    },

    async createClient(data) {
        const response = await api.post('/clients', data);
        return response.data;
    },

    async updateClient(id, data) {
        const response = await api.put(`/clients/${id}`, data);
        return response.data;
    },

    async deleteClient(id) {
        const response = await api.delete(`/clients/${id}`);
        return response.data;
    },
};
