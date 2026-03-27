import api from '.';

export const dashboardApi = {
    async getDashboard() {
        const response = await api.get('/dashboard');
        return response.data;
    },
};
