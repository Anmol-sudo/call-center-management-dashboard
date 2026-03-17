import { createRouter, createWebHistory } from 'vue-router';
import LoginView from './views/LoginView.vue';
import DashboardView from './views/DashboardView.vue';
import EmployeesView from './views/EmployeesView.vue';
import SupervisorsView from './views/SupervisorsView.vue';
import PhoneLinesView from './views/PhoneLinesView.vue';
import ReportsView from './views/ReportsView.vue';
import PbxView from './views/PbxView.vue';
import UsersView from './views/UsersView.vue';
import { useAuthStore } from './stores/auth';

const routes = [
    { path: '/login', name: 'login', component: LoginView, meta: { guest: true } },
    { path: '/', name: 'dashboard', component: DashboardView, meta: { requiresAuth: true } },
    { path: '/employees', name: 'employees', component: EmployeesView, meta: { requiresAuth: true } },
    { path: '/supervisors', name: 'supervisors', component: SupervisorsView, meta: { requiresAuth: true } },
    { path: '/phone-lines', name: 'phone-lines', component: PhoneLinesView, meta: { requiresAuth: true } },
    { path: '/reports', name: 'reports', component: ReportsView, meta: { requiresAuth: true } },
    { path: '/pbx', name: 'pbx', component: PbxView, meta: { requiresAuth: true } },
    { path: '/users', name: 'users', component: UsersView, meta: { requiresAuth: true, role: 'admin' } },
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

    if (to.meta.role === 'admin' && auth.user?.role !== 'admin') {
        next({ name: 'dashboard' });
        return;
    }

    next();
});

