import api from './index';

export const dashboardService = {
    async getDashboard() {
        const response = await api.get('/dashboard');
        return response.data;
    },
};
