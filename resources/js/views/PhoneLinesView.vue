<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-lg font-semibold text-slate-800">خطوط الهاتف</h2>
                <p class="text-sm text-slate-500 mt-1">إدارة خطوط الهاتف وربطها بالموظفين</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="py-3 px-4 text-right font-medium text-slate-600">الاسم</th>
                            <th class="py-3 px-4 text-right font-medium text-slate-600">الرقم</th>
                            <th class="py-3 px-4 text-right font-medium text-slate-600">النوع</th>
                            <th class="py-3 px-4 text-right font-medium text-slate-600">الحالة</th>
                            <th class="py-3 px-4 text-right font-medium text-slate-600">عدد الموظفين</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr
                            v-for="line in lines"
                            :key="line.id"
                            class="hover:bg-slate-50/50 transition-colors"
                        >
                            <td class="py-3 px-4 font-medium text-slate-800">
                                {{ line.name }}
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                {{ line.number }}
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                {{ typeLabel(line.type) }}
                            </td>
                            <td class="py-3 px-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium"
                                    :class="line.status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600'"
                                >
                                    {{ line.status === 'active' ? 'نشط' : 'غير نشط' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                {{ line.employees?.length ?? 0 }}
                            </td>
                        </tr>
                        <tr v-if="!lines.length && !loading">
                            <td colspan="5" class="py-8 text-center text-slate-400">
                                لا توجد خطوط هاتف
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import axios from 'axios';

const api = axios.create({
    baseURL: 'http://127.0.0.1:8000/api',
});

const lines = ref([]);
const loading = ref(false);

const applyAuth = () => {
    const token = localStorage.getItem('token');
    if (token) {
        api.defaults.headers.common.Authorization = `Bearer ${token}`;
    }
};

const fetchLines = async () => {
    loading.value = true;
    applyAuth();
    try {
        const { data } = await api.get('/phone-lines');
        lines.value = data.data ?? data;
    } finally {
        loading.value = false;
    }
};

const typeLabel = (type) => {
    switch (type) {
        case 'incoming':
            return 'واردة';
        case 'outgoing':
            return 'صادرة';
        case 'both':
        default:
            return 'واردة / صادرة';
    }
};

onMounted(fetchLines);
</script>

