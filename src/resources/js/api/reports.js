import api from './index';

export const reportService = {
    async getMonthlyReport(year, month) {
        const response = await api.get('/reports/monthly', { params: { year, month } });
        return response.data;
    },

    async getYearlyReport(year) {
        const response = await api.get('/reports/yearly', { params: { year } });
        return response.data;
    },

    async getAppointmentsReport(year, month) {
        const response = await api.get('/reports/appointments', { params: { year, month } });
        return response.data;
    },
};
