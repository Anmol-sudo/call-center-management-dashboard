import { createRouter, createWebHistory } from 'vue-router';
import LoginView from './views/LoginView.vue';
import DashboardView from './views/DashboardView.vue';
import PlaceholderView from './views/PlaceholderView.vue';
import { useAuthStore } from './stores/auth';

const routes = [
    { path: '/login', name: 'login', component: LoginView, meta: { guest: true } },
    { path: '/', name: 'dashboard', component: DashboardView, meta: { requiresAuth: true } },
    { path: '/employees', name: 'employees', component: PlaceholderView, meta: { requiresAuth: true } },
    { path: '/supervisors', name: 'supervisors', component: PlaceholderView, meta: { requiresAuth: true } },
    { path: '/phone-lines', name: 'phone-lines', component: PlaceholderView, meta: { requiresAuth: true } },
    { path: '/reports', name: 'reports', component: PlaceholderView, meta: { requiresAuth: true } },
    { path: '/pbx', name: 'pbx', component: PlaceholderView, meta: { requiresAuth: true } },
    { path: '/users', name: 'users', component: PlaceholderView, meta: { requiresAuth: true } },
];

export const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const auth = useAuthStore();

    if (to.meta.requiresAuth && !auth.isAuthenticated) {
        next({ name: 'login' });
        return;
    }

    if (to.meta.guest && auth.isAuthenticated) {
        next({ name: 'dashboard' });
        return;
    }

    next();
});

