import axios from "axios";

const axiosInstance = axios.create({
    baseURL: 'http://127.0.0.1:8000/api',
    headers: {
        'Content-Type': "application/json",
        'Accept': 'application/json'
    },
    withCredentials: true
});

axiosInstance.interceptors.request.use(config =>{
    const token = localStorage.getItem('access_token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
},error =>{
    return Promise.reject(error);
});

axiosInstance.interceptors.response.use(response => response, error => {
    if (error.response && error.response.status === 401) {
        window.location.href = '/login';
    }
    return Promise.reject(error);
});

export default axiosInstance;
