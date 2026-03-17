<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-lg font-semibold text-slate-800">السنترال</h2>
                <p class="text-sm text-slate-500 mt-1">حالة اتصال السنترال ومؤشرات الأداء الأساسية</p>
            </div>
            <button
                class="inline-flex items-center gap-2 px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-lg transition-colors disabled:opacity-60"
                @click="fetchStatus"
                :disabled="loading"
            >
                <span
                    v-if="loading"
                    class="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"
                />
                <span v-else>تحديث</span>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500 mb-2">حالة الاتصال</p>
                <p
                    class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium"
                    :class="
                        status.status === 'connected'
                            ? 'bg-emerald-100 text-emerald-700'
                            : 'bg-red-100 text-red-700'
                    "
                >
                    {{ status.status === 'connected' ? 'متصل' : 'غير متصل' }}
                </p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500 mb-2">المكالمات النشطة</p>
                <p class="text-2xl font-bold text-slate-800">
                    {{ status.active_calls ?? 0 }}
                </p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500 mb-2">التراخيص المستخدمة</p>
                <p class="text-xs text-slate-600 mb-1">
                    {{ status.active_calls ?? 0 }} / {{ status.max_concurrent_calls ?? 0 }}
                </p>
                <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                    <div
                        class="h-2 rounded-full bg-sky-500 transition-all"
                        :style="{ width: capacityPercent + '%' }"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import axios from 'axios';

const api = axios.create({
    baseURL: 'http://127.0.0.1:8000/api',
});

const loading = ref(false);
const status = ref({
    status: 'disconnected',
    active_calls: 0,
    registered_extensions: 0,
    total_extensions: 0,
    max_concurrent_calls: 0,
});

const applyAuth = () => {
    const token = localStorage.getItem('token');
    if (token) {
        api.defaults.headers.common.Authorization = `Bearer ${token}`;
    }
};

const fetchStatus = async () => {
    loading.value = true;
    applyAuth();
    try {
        const { data } = await api.get('/pbx/status');
        status.value = data;
    } finally {
        loading.value = false;
    }
};

const capacityPercent = computed(() => {
    const max = status.value.max_concurrent_calls ?? 0;
    if (!max) return 0;
    return Math.round(((status.value.active_calls ?? 0) * 100) / max);
});

onMounted(fetchStatus);
</script>

