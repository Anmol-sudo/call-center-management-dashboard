<template>
    <div class="space-y-6">
        <!-- Stats cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div
                class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">الوكلاء المتصلون</p>
                        <p class="text-2xl font-bold text-slate-800 mt-1">
                            {{ data?.online_agents ?? '—' }}
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-700"
                    >
                        <SignalIcon class="w-6 h-6" />
                    </div>
                </div>
            </div>
            <div
                class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">إجمالي المكالمات</p>
                        <p class="text-2xl font-bold text-slate-800 mt-1">
                            {{ data?.normal_calls ?? '—' }}
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 rounded-xl bg-sky-100 flex items-center justify-center text-sky-700"
                    >
                        <PhoneArrowDownLeftIcon class="w-6 h-6" />
                    </div>
                </div>
            </div>
            <div
                class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">مكالمات مجابة</p>
                        <p class="text-2xl font-bold text-emerald-600 mt-1">
                            {{ data?.answered_calls ?? '—' }}
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-700"
                    >
                        <CheckCircleIcon class="w-6 h-6" />
                    </div>
                </div>
            </div>
            <div
                class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">مكالمات فائتة</p>
                        <p class="text-2xl font-bold text-red-600 mt-1">
                            {{ data?.missed_calls ?? '—' }}
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center text-red-700"
                    >
                        <XCircleIcon class="w-6 h-6" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="flex items-center justify-between flex-wrap gap-4">
            <p class="text-sm text-slate-500">
                آخر تحديث:
                <span v-if="data?.last_updated" class="font-medium text-slate-700">
                    {{ new Date(data.last_updated).toLocaleString('ar-EG') }}
                </span>
                <span v-else>—</span>
            </p>
            <button
                class="inline-flex items-center gap-2 px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-lg transition-colors disabled:opacity-60"
                @click="fetchData"
                :disabled="loading"
            >
                <span
                    v-if="loading"
                    class="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"
                />
                <ArrowPathIcon v-else class="w-4 h-4" />
                <span>تحديث</span>
            </button>
        </div>

        <!-- Tables -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <!-- Employee status -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 bg-slate-50/50">
                    <h3 class="font-semibold text-slate-800">حالة الموظفين</h3>
                    <p class="text-xs text-slate-500 mt-0.5">الحالة الحالية لجميع الوكلاء</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="py-3 px-4 text-right font-medium text-slate-600">الاسم</th>
                                <th class="py-3 px-4 text-right font-medium text-slate-600">
                                    الرقم الداخلي
                                </th>
                                <th class="py-3 px-4 text-right font-medium text-slate-600">القسم</th>
                                <th class="py-3 px-4 text-right font-medium text-slate-600">الحالة</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr
                                v-for="emp in data?.employees ?? []"
                                :key="emp.id"
                                class="hover:bg-slate-50/50 transition-colors"
                            >
                                <td class="py-3 px-4 font-medium text-slate-800">{{ emp.name }}</td>
                                <td class="py-3 px-4 text-slate-600">{{ emp.extension }}</td>
                                <td class="py-3 px-4 text-slate-600">{{ emp.department ?? '—' }}</td>
                                <td class="py-3 px-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium"
                                        :class="statusClass(emp.status)"
                                    >
                                        <span
                                            class="w-1.5 h-1.5 rounded-full mr-1.5"
                                            :class="statusDotClass(emp.status)"
                                        />
                                        {{ statusLabel(emp.status) }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="!data?.employees?.length && !loading">
                                <td colspan="4" class="py-8 text-center text-slate-400">
                                    لا يوجد موظفون
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent calls -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 bg-slate-50/50">
                    <h3 class="font-semibold text-slate-800">آخر المكالمات</h3>
                    <p class="text-xs text-slate-500 mt-0.5">سجل المكالمات الأخيرة</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="py-3 px-4 text-right font-medium text-slate-600">
                                    الموظف
                                </th>
                                <th class="py-3 px-4 text-right font-medium text-slate-600">النوع</th>
                                <th class="py-3 px-4 text-right font-medium text-slate-600">الحالة</th>
                                <th class="py-3 px-4 text-right font-medium text-slate-600">الوقت</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr
                                v-for="call in data?.recent_calls ?? []"
                                :key="call.id"
                                class="hover:bg-slate-50/50 transition-colors"
                            >
                                <td class="py-3 px-4">
                                    <span class="font-medium text-slate-800">{{ call.employee }}</span>
                                    <span v-if="call.extension" class="text-slate-500 text-xs mr-1">
                                        ({{ call.extension }})
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium"
                                        :class="directionClass(call.direction)"
                                    >
                                        {{ directionLabel(call.direction) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-slate-600">{{ call.status }}</td>
                                <td class="py-3 px-4 text-slate-600">
                                    {{
                                        call.started_at
                                            ? new Date(call.started_at).toLocaleTimeString('ar-EG')
                                            : '—'
                                    }}
                                </td>
                            </tr>
                            <tr v-if="!data?.recent_calls?.length && !loading">
                                <td colspan="4" class="py-8 text-center text-slate-400">
                                    لا توجد مكالمات
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import {
    SignalIcon,
    PhoneArrowDownLeftIcon,
    CheckCircleIcon,
    XCircleIcon,
    ArrowPathIcon,
} from '@heroicons/vue/24/outline';

const loading = ref(false);
const data = ref(null);
const intervalId = ref(null);

const api = axios.create({
    baseURL: 'http://127.0.0.1:8000/api',
});

const fetchData = async () => {
    loading.value = true;
    try {
        const token = localStorage.getItem('token');
        if (token) {
            api.defaults.headers.common.Authorization = `Bearer ${token}`;
        }
        const response = await api.get('/dashboard/live');
        data.value = response.data;
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchData();
    intervalId.value = setInterval(fetchData, 30000);
});

onUnmounted(() => {
    if (intervalId.value) {
        clearInterval(intervalId.value);
    }
});

const statusLabel = (status) => {
    switch (status) {
        case 'online':
            return 'متصل';
        case 'on-call':
            return 'على مكالمة';
        case 'idle':
            return 'خامل';
        case 'break':
            return 'استراحة';
        case 'late':
            return 'متأخر';
        case 'offline':
        default:
            return 'غير متصل';
    }
};

const statusClass = (status) => {
    switch (status) {
        case 'online':
            return 'bg-emerald-100 text-emerald-700';
        case 'on-call':
            return 'bg-blue-100 text-blue-700';
        case 'idle':
            return 'bg-amber-100 text-amber-700';
        case 'break':
            return 'bg-slate-100 text-slate-600';
        case 'late':
            return 'bg-orange-100 text-orange-700';
        case 'offline':
        default:
            return 'bg-red-100 text-red-700';
    }
};

const statusDotClass = (status) => {
    switch (status) {
        case 'online':
            return 'bg-emerald-500';
        case 'on-call':
            return 'bg-blue-500';
        case 'idle':
            return 'bg-amber-500';
        case 'break':
            return 'bg-slate-400';
        case 'late':
            return 'bg-orange-500';
        case 'offline':
        default:
            return 'bg-red-500';
    }
};

const directionLabel = (direction) => (direction === 'incoming' ? 'واردة' : 'صادرة');

const directionClass = (direction) =>
    direction === 'incoming' ? 'bg-emerald-100 text-emerald-700' : 'bg-sky-100 text-sky-700';
</script>
