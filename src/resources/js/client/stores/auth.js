import { defineStore } from 'pinia';
import { clientAuthApi } from '../api/auth';

export const useAuthStore = defineStore('clientAuth', {
    state: () => ({
        user: clientAuthApi.getUser(),
        token: clientAuthApi.getToken(),
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
        userName: (state) => state.user?.name || '',
        userPhone: (state) => state.user?.phone || '',
    },

    actions: {
        async login(credentials) {
            try {
                const response = await clientAuthApi.login(credentials);
                this.user = response.client;
                this.token = response.token;
                return { success: true };
            } catch (error) {
                return {
                    success: false,
                    message: error.response?.data?.message || 'Login failed',
                };
            }
        },

        async register(userData) {
            try {
                const response = await clientAuthApi.register(userData);
                this.user = response.client;
                this.token = response.token;
                return { success: true };
            } catch (error) {
                return {
                    success: false,
                    message: error.response?.data?.message || 'Registration failed',
                };
            }
        },

        async logout() {
            try {
                await clientAuthApi.logout();
            } catch (error) {
                console.error('Logout error:', error);
            } finally {
                this.user = null;
                this.token = null;
            }
        },

        async fetchUser() {
            try {
                const user = await clientAuthApi.getMe();
                this.user = user;
                localStorage.setItem('client_user', JSON.stringify(user));
            } catch (error) {
                console.error('Fetch user error:', error);
            }
        },
    },
});
