import api from '.';

export const bookingApi = {
    async getMasterBySlug(slug) {
        const response = await api.get(`/public/master/${slug}`);
        return response.data;
    },

    async getServices(userId) {
        const response = await api.get(`/public/services/${userId}`);
        return response.data;
    },

    async getAvailableSlots(userId, date, serviceId = null) {
        const response = await api.get('/public/available-slots', {
            params: { user_id: userId, date, service_id: serviceId },
        });
        return response.data;
    },

    async createBooking(bookingData) {
        const response = await api.post('/public/book', bookingData);
        return response.data;
    },
};
