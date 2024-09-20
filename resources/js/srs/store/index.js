import { createStore } from 'vuex';
import axiosInstance from '../services/axios';

export default createStore({
    state: {
        access_token: localStorage.getItem('access_token') || '',
        user: null,
    },
    mutations: {
        setToken(state, token) {
            state.access_token = token;
            localStorage.setItem('access_token', token);
        },
        clearToken(state) {
            state.access_token = '';
            localStorage.removeItem('access_token');
        },
        setUser(state, user) {
            state.user = user;
        },
        clearUser(state) {
            state.user = null;
        },
    },
    actions: {
        async register({ commit }, userData) {
            try {
                await axiosInstance.get('/sanctum/csrf-cookie');
                const response = await axiosInstance.post('/register', userData);
                commit('setToken', response.data.access_token);
                await this.dispatch('fetchUser');
                return response;
            } catch (error) {
                throw error;
            }
        },
        async login({ commit }, credentials) {
            try {
                await axiosInstance.get('/sanctum/csrf-cookie');
                const response = await axiosInstance.post('/login', credentials);
                commit('setToken', response.data.access_token);
                await this.dispatch('fetchUser');
                return response;
            } catch (error) {
                throw error;
            }
        },
        async logout({ commit }) {
            try {
                await axiosInstance.post('/logout');
                commit('clearToken');
                commit('clearUser');
            } catch (error) {
                throw error;
            }
        },
        async fetchUser({ commit }) {
            try {
                const response = await axiosInstance.get('/user');
                commit('setUser', response.data);
            } catch (error) {
                commit('clearToken');
                commit('clearUser');
            }
        },
    },
    getters: {
        isAuthenticated: state => !!state.access_token,
        getUser: state => state.user,
    },
});
