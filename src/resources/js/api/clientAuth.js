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

    async verifyCode(phone, code) {
        const response = await api.post('/client/verify-code', { phone, code });
        if (response.data.token) {
            localStorage.setItem('client_token', response.data.token);
            localStorage.setItem('client_user', JSON.stringify(response.data.client));
        }
        return response.data;
    },

    async logout() {
        try {
            await api.post('/client/logout');
        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            localStorage.removeItem('client_token');
            localStorage.removeItem('client_user');
        }
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

    setToken(token) {
        localStorage.setItem('client_token', token);
    },
};
