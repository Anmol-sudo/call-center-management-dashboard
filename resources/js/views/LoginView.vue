<template>
    <div
        class="min-h-screen flex items-center justify-center px-4 bg-gradient-to-br from-slate-900 via-slate-800 to-sky-900"
    >
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%239C92AC\' fill-opacity=\'0.05\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-40" />
        <div class="relative w-full max-w-md">
            <div class="bg-white/95 backdrop-blur rounded-2xl shadow-2xl p-8 border border-white/20">
                <div class="text-center mb-8">
                    <div
                        class="inline-flex items-center justify-center w-16 h-16 rounded-xl bg-sky-600 text-white text-2xl mb-4"
                    >
                        📞
                    </div>
                    <h1 class="text-2xl font-bold text-slate-800">مركز الاتصال</h1>
                    <p class="text-slate-500 text-sm mt-1">لوحة تحكم المشرفين</p>
                </div>

                <h2 class="text-lg font-semibold text-slate-800 mb-6">تسجيل الدخول</h2>

                <form @submit.prevent="onSubmit" class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            اسم المستخدم
                        </label>
                        <input
                            v-model="username"
                            type="text"
                            required
                            placeholder="أدخل اسم المستخدم"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-shadow"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            كلمة المرور
                        </label>
                        <input
                            v-model="password"
                            type="password"
                            required
                            placeholder="أدخل كلمة المرور"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-shadow"
                        />
                    </div>

                    <div
                        v-if="auth.error"
                        class="flex items-center gap-2 px-4 py-3 rounded-xl bg-red-50 text-red-700 text-sm"
                    >
                        <span>⚠️</span>
                        <span>{{ auth.error }}</span>
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-sky-600 hover:bg-sky-700 text-white font-medium py-3 rounded-xl transition-colors disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                        :disabled="auth.loading"
                    >
                        <span v-if="auth.loading" class="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                        <span>{{ auth.loading ? 'جاري تسجيل الدخول...' : 'تسجيل الدخول' }}</span>
                    </button>
                </form>

                <p class="text-xs text-slate-400 text-center mt-6">
                    استخدم بيانات الدخول المقدمة من مدير النظام
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const auth = useAuthStore();

const username = ref('admin');
const password = ref('password123');

const onSubmit = async () => {
    try {
        await auth.login(username.value, password.value);
        router.push({ name: 'dashboard' });
    } catch {
        // error handled in store
    }
};
</script>
