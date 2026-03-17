<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-lg font-semibold text-slate-800">التقارير</h2>
                <p class="text-sm text-slate-500 mt-1">ملخص أداء المكالمات خلال الفترة المحددة</p>
            </div>
            <div class="flex items-center gap-2">
                <select
                    v-model="period"
                    class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                    @change="fetchReports"
                >
                    <option value="today">اليوم</option>
                    <option value="week">هذا الأسبوع</option>
                    <option value="month">هذا الشهر</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500 mb-2">إجمالي المكالمات</p>
                <p class="text-2xl font-bold text-slate-800">
                    {{ totalCalls }}
                </p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500 mb-2">نسبة المكالمات المجابة</p>
                <p class="text-2xl font-bold text-emerald-600">
                    {{ answeredRate }}%
                </p>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500 mb-2">المكالمات الفائتة</p>
                <p class="text-2xl font-bold text-red-600">
                    {{ summary.call_type_distribution.missed ?? 0 }}
                </p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 bg-slate-50/50">
                <h3 class="font-semibold text-slate-800">أفضل الموظفين</h3>
                <p class="text-xs text-slate-500 mt-0.5">ترتيب حسب نسبة الاستجابة</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="py-3 px-4 text-right font-medium text-slate-600">الموظف</th>
                            <th class="py-3 px-4 text-right font-medium text-slate-600">المجابهة</th>
                            <th class="py-3 px-4 text-right font-medium text-slate-600">الفائتة</th>
                            <th class="py-3 px-4 text-right font-medium text-slate-600">الإجمالي</th>
                            <th class="py-3 px-4 text-right font-medium text-slate-600">% الاستجابة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr
                            v-for="row in summary.top_performers"
                            :key="row.employee_id ?? row.name"
                            class="hover:bg-slate-50/50 transition-colors"
                        >
                            <td class="py-3 px-4 font-medium text-slate-800">
                                {{ row.name ?? '—' }}
                                <span v-if="row.extension" class="text-xs text-slate-500 mr-1">
                                    ({{ row.extension }})
                                </span>
                            </td>
                            <td class="py-3 px-4 text-emerald-600 font-medium">
                                {{ row.answered }}
                            </td>
                            <td class="py-3 px-4 text-red-600 font-medium">
                                {{ row.missed }}
                            </td>
                            <td class="py-3 px-4 text-slate-700 font-medium">
                                {{ row.total }}
                            </td>
                            <td class="py-3 px-4 text-slate-700 font-medium">
                                {{ row.response_rate }}%
                            </td>
                        </tr>
                        <tr v-if="!summary.top_performers.length && !loading">
                            <td colspan="5" class="py-8 text-center text-slate-400">
                                لا توجد بيانات متاحة
                            </td>
                        </tr>
                    </tbody>
                </table>
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
const period = ref('today');
const summary = ref({
    calls_per_employee: [],
    call_type_distribution: { incoming: 0, outgoing: 0, missed: 0 },
    hourly_traffic: [],
    top_performers: [],
});

const applyAuth = () => {
    const token = localStorage.getItem('token');
    if (token) {
        api.defaults.headers.common.Authorization = `Bearer ${token}`;
    }
};

const fetchReports = async () => {
    loading.value = true;
    applyAuth();
    try {
        const { data } = await api.get('/reports/summary', {
            params: { period: period.value },
        });
        summary.value = data;
    } finally {
        loading.value = false;
    }
};

const totalCalls = computed(
    () =>
        (summary.value.call_type_distribution.incoming ?? 0) +
        (summary.value.call_type_distribution.outgoing ?? 0),
);

const answeredRate = computed(() => {
    const ans = summary.value.calls_per_employee.reduce((acc, row) => acc + (row.answered ?? 0), 0);
    const tot = summary.value.calls_per_employee.reduce((acc, row) => acc + (row.total ?? 0), 0);
    if (!tot) return 0;
    return Math.round((ans * 100) / tot);
});

onMounted(fetchReports);
</script>

