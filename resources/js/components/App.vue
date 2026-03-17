<template>
    <div class="min-h-screen flex bg-slate-50" dir="rtl">
        <!-- Sidebar (only when authenticated) -->
        <aside
            v-if="auth.isAuthenticated"
            class="w-64 min-h-screen bg-slate-900 text-white flex flex-col shrink-0"
        >
            <div class="p-5 border-b border-slate-700">
                <h1 class="text-lg font-bold text-white">مركز الاتصال</h1>
                <p class="text-xs text-slate-400 mt-0.5">لوحة التحكم</p>
            </div>
            <nav class="flex-1 p-3 space-y-2">
                <router-link
                    to="/"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-left transition-colors"
                    :class="
                        $route.path === '/'
                            ? 'bg-sky-600 text-white'
                            : 'text-slate-300 hover:bg-slate-800 hover:text-white'
                    "
                >
                    <ChartBarSquareIcon class="w-5 h-5" />
                    <span>التقرير الحي</span>
                </router-link>
                <router-link
                    to="/employees"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-left transition-colors"
                    :class="
                        $route.path === '/employees'
                            ? 'bg-sky-600 text-white'
                            : 'text-slate-300 hover:bg-slate-800 hover:text-white'
                    "
                >
                    <UsersIcon class="w-5 h-5" />
                    <span>الموظفون</span>
                </router-link>
                <router-link
                    to="/supervisors"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-left transition-colors"
                    :class="
                        $route.path === '/supervisors'
                            ? 'bg-sky-600 text-white'
                            : 'text-slate-300 hover:bg-slate-800 hover:text-white'
                    "
                >
                    <EyeIcon class="w-5 h-5" />
                    <span>المشرفون</span>
                </router-link>
                <router-link
                    to="/phone-lines"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-left transition-colors"
                    :class="
                        $route.path === '/phone-lines'
                            ? 'bg-sky-600 text-white'
                            : 'text-slate-300 hover:bg-slate-800 hover:text-white'
                    "
                >
                    <PhoneIcon class="w-5 h-5" />
                    <span>خطوط الهاتف</span>
                </router-link>
                <router-link
                    to="/reports"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-left transition-colors"
                    :class="
                        $route.path === '/reports'
                            ? 'bg-sky-600 text-white'
                            : 'text-slate-300 hover:bg-slate-800 hover:text-white'
                    "
                >
                    <ChartPieIcon class="w-5 h-5" />
                    <span>التقارير</span>
                </router-link>
                <router-link
                    to="/pbx"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-left transition-colors"
                    :class="
                        $route.path === '/pbx'
                            ? 'bg-sky-600 text-white'
                            : 'text-slate-300 hover:bg-slate-800 hover:text-white'
                    "
                >
                    <BoltIcon class="w-5 h-5" />
                    <span>السنترال</span>
                </router-link>
                <router-link
                    v-if="auth.user?.role === 'admin'"
                    to="/users"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-left transition-colors"
                    :class="
                        $route.path === '/users'
                            ? 'bg-sky-600 text-white'
                            : 'text-slate-300 hover:bg-slate-800 hover:text-white'
                    "
                >
                    <Cog6ToothIcon class="w-5 h-5" />
                    <span>إدارة المستخدمين</span>
                </router-link>
            </nav>
            <div class="p-3 border-t border-slate-700">
                <div class="flex items-center gap-3 px-4 py-2">
                    <div class="w-9 h-9 rounded-full bg-sky-600 flex items-center justify-center text-sm font-semibold">
                        {{ auth.user?.name?.charAt(0) ?? '?' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">{{ auth.user?.name }}</p>
                        <p class="text-xs text-slate-400">
                            {{ auth.user?.role === 'admin' ? 'مدير' : 'مشرف' }}
                        </p>
                    </div>
                </div>
                <button
                    class="w-full mt-2 flex items-center gap-2 px-4 py-2 rounded-lg text-slate-400 hover:bg-slate-800 hover:text-red-400 transition-colors text-sm"
                    @click="handleLogout"
                >
                    <ArrowRightStartOnRectangleIcon class="w-4 h-4" />
                    <span>تسجيل الخروج</span>
                </button>
            </div>
        </aside>

        <!-- Main content -->
        <div class="flex-1 flex flex-col min-w-0">
            <header
                v-if="auth.isAuthenticated"
                class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-6 shrink-0"
            >
                <div class="flex items-center gap-4">
                    <h2 class="text-slate-800 font-semibold">{{ pageTitle }}</h2>
                    <span class="text-xs text-slate-500">{{ now }}</span>
                </div>
            </header>

            <main
                class="flex-1 overflow-auto"
                :class="auth.isAuthenticated ? 'p-6' : 'p-0'"
            >
                <Transition
                    name="page-fade"
                    mode="out-in"
                >
                    <router-view :key="route.fullPath" />
                </Transition>
            </main>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { ChartBarSquareIcon, UsersIcon, EyeIcon, PhoneIcon, ChartPieIcon } from '@heroicons/vue/24/outline';
import { BoltIcon, Cog6ToothIcon, ArrowRightStartOnRectangleIcon } from '@heroicons/vue/24/solid';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const route = useRoute();
const auth = useAuthStore();

const pageTitle = computed(() => {
    const titles = {
        '/': 'التقرير الحي',
        '/employees': 'الموظفون',
        '/supervisors': 'المشرفون',
        '/phone-lines': 'خطوط الهاتف',
        '/reports': 'التقارير',
        '/pbx': 'السنترال',
        '/users': 'إدارة المستخدمين',
    };
    return titles[route.path] ?? 'لوحة التحكم';
});

const now = computed(() => new Date().toLocaleDateString('ar-EG', { dateStyle: 'long' }));

const handleLogout = async () => {
    await auth.logout();
    router.push({ name: 'login' });
};
</script>
