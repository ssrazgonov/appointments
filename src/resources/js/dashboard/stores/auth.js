import { defineStore } from 'pinia';
import { authApi } from '@/api/auth';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: authApi.getUser(),
        token: authApi.getToken(),
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
        userName: (state) => state.user?.name || '',
        userEmail: (state) => state.user?.email || '',
    },

    actions: {
        async login(credentials) {
            try {
                const response = await authApi.login(credentials);
                this.user = response.user;
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
                const response = await authApi.register(userData);
                this.user = response.user;
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
                await authApi.logout();
            } catch (error) {
                console.error('Logout error:', error);
            } finally {
                this.user = null;
                this.token = null;
            }
        },

        async fetchUser() {
            try {
                const user = await authApi.getMe();
                this.user = user;
                localStorage.setItem('user', JSON.stringify(user));
            } catch (error) {
                console.error('Fetch user error:', error);
            }
        },
    },
});
