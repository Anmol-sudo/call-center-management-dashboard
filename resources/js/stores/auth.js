import { defineStore } from 'pinia';
import axios from 'axios';

const api = axios.create({
    baseURL: 'http://127.0.0.1:8000/api',
});

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem('token'),
        loading: false,
        error: null,
    }),
    getters: {
        isAuthenticated: (state) => !!state.token && !!state.user,
    },
    actions: {
        async initFromStorage() {
            if (!this.token) return;
            api.defaults.headers.common.Authorization = `Bearer ${this.token}`;
            try {
                const { data } = await api.get('/auth/me');
                this.user = data;
            } catch {
                this.logout();
            }
        },
        async login(username, password) {
            this.loading = true;
            this.error = null;
            try {
                const { data } = await api.post('/auth/login', { username, password });
                this.token = data.token;
                this.user = data.user;
                localStorage.setItem('token', this.token);
                api.defaults.headers.common.Authorization = `Bearer ${this.token}`;
            } catch (e) {
                this.error = e?.response?.data?.message ?? 'خطأ في تسجيل الدخول';
                throw e;
            } finally {
                this.loading = false;
            }
        },
        async logout() {
            try {
                if (this.token) {
                    await api.post('/auth/logout');
                }
            } catch {
                // ignore
            } finally {
                this.token = null;
                this.user = null;
                this.error = null;
                localStorage.removeItem('token');
                delete api.defaults.headers.common.Authorization;
            }
        },
    },
});

