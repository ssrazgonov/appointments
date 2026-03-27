import api from '@/api';

export const clientAuthApi = {
    async register(userData) {
        const response = await api.post('/client/register', userData);
        if (response.data.token) {
            localStorage.setItem('client_token', response.data.token);
            localStorage.setItem('client_user', JSON.stringify(response.data.client));
        }
        return response.data;
    },

    async login(credentials) {
        const response = await api.post('/client/login', credentials);
        if (response.data.token) {
            localStorage.setItem('client_token', response.data.token);
            localStorage.setItem('client_user', JSON.stringify(response.data.client));
        }
        return response.data;
    },

    async sendVerificationCode(phone) {
        const response = await api.post('/client/send-verification-code', { phone });
        return response.data;
    },

    async logout() {
        await api.post('/client/logout');
        localStorage.removeItem('client_token');
        localStorage.removeItem('client_user');
    },

    async getMe() {
        const response = await api.get('/client/me');
        return response.data.client;
    },

    getToken() {
        return localStorage.getItem('client_token');
    },

    getUser() {
        const user = localStorage.getItem('client_user');
        return user ? JSON.parse(user) : null;
    },

    isAuthenticated() {
        return !!this.getToken();
    },
};
