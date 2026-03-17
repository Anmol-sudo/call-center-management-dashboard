<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-lg font-semibold text-slate-800">المشرفون</h2>
                <p class="text-sm text-slate-500 mt-1">عرض المشرفين والموظفين التابعين لكل مشرف</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="py-3 px-4 text-right font-medium text-slate-600">المشرف</th>
                            <th class="py-3 px-4 text-right font-medium text-slate-600">الرقم الداخلي</th>
                            <th class="py-3 px-4 text-right font-medium text-slate-600">عدد الموظفين</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr
                            v-for="sup in supervisors"
                            :key="sup.id"
                            class="hover:bg-slate-50/50 transition-colors"
                        >
                            <td class="py-3 px-4 font-medium text-slate-800">
                                {{ sup.user?.name ?? '—' }}
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                {{ sup.extension ?? '—' }}
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                {{ sup.employees?.length ?? 0 }}
                            </td>
                        </tr>
                        <tr v-if="!supervisors.length && !loading">
                            <td colspan="3" class="py-8 text-center text-slate-400">
                                لا يوجد مشرفون
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

const supervisors = ref([]);
const loading = ref(false);

const applyAuth = () => {
    const token = localStorage.getItem('token');
    if (token) {
        api.defaults.headers.common.Authorization = `Bearer ${token}`;
    }
};

const fetchSupervisors = async () => {
    loading.value = true;
    applyAuth();
    try {
        const { data } = await api.get('/supervisors');
        supervisors.value = data.data ?? data;
    } finally {
        loading.value = false;
    }
};

onMounted(fetchSupervisors);
</script>

