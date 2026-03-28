import axios from 'axios';

const api = axios.create({
    baseURL: '/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
});

// Request interceptor to add auth token (supports both master and client tokens)
api.interceptors.request.use(
    (config) => {
        // Try client token first, then master token
        const token = localStorage.getItem('client_token') || localStorage.getItem('token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Response interceptor to handle errors
api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            // Check if it's a client request
            const isClientRequest = window.location.pathname.startsWith('/client');
            
            if (isClientRequest) {
                localStorage.removeItem('client_token');
                localStorage.removeItem('client_user');
                if (!window.location.pathname.includes('/client/login')) {
                    window.location.href = '/client/login';
                }
            } else {
                localStorage.removeItem('token');
                localStorage.removeItem('user');
                if (window.location.pathname.startsWith('/app')) {
                    window.location.href = '/app/login';
                }
            }
        }
        return Promise.reject(error);
    }
);

export default api;
