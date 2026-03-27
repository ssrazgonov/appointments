import api from '.';

export const masterProfileApi = {
    async getProfile() {
        const response = await api.get('/master-profile');
        return response.data;
    },

    async createProfile(data) {
        const response = await api.post('/master-profile', data);
        return response.data;
    },

    async updateProfile(data) {
        const response = await api.put('/master-profile', data);
        return response.data;
    },

    async getWorkingHours() {
        const response = await api.get('/master-profile/working-hours');
        return response.data;
    },

    async updateWorkingHours(workingHours) {
        const response = await api.put('/master-profile/working-hours', { working_hours: workingHours });
        return response.data;
    },

    async getServices() {
        const response = await api.get('/master-profile/services');
        return response.data;
    },

    async createService(data) {
        const response = await api.post('/master-profile/services', data);
        return response.data;
    },

    async updateService(id, data) {
        const response = await api.put(`/master-profile/services/${id}`, data);
        return response.data;
    },

    async deleteService(id) {
        const response = await api.delete(`/master-profile/services/${id}`);
        return response.data;
    },
};
