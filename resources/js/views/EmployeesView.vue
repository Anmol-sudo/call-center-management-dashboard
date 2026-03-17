<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-lg font-semibold text-slate-800">الموظفون</h2>
                <p class="text-sm text-slate-500 mt-1">إدارة قائمة موظفي مركز الاتصال</p>
            </div>
            <button
                class="inline-flex items-center gap-2 px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-lg transition-colors"
                @click="openCreate"
            >
                <span class="text-lg leading-none">＋</span>
                <span>إضافة موظف</span>
            </button>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 bg-slate-50/50 flex flex-wrap gap-3 items-center">
                <div class="flex-1 min-w-[160px]">
                    <input
                        v-model="filters.search"
                        type="text"
                        placeholder="ابحث بالاسم أو الرقم الداخلي..."
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                    />
                </div>
                <button
                    class="px-3 py-2 text-sm rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-50"
                    @click="fetchEmployees"
                >
                    بحث
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="py-3 px-4 text-right font-medium text-slate-600">الاسم</th>
                            <th class="py-3 px-4 text-right font-medium text-slate-600">الرقم الداخلي</th>
                            <th class="py-3 px-4 text-right font-medium text-slate-600">القسم</th>
                            <th class="py-3 px-4 text-right font-medium text-slate-600">الحالة</th>
                            <th class="py-3 px-4 text-right font-medium text-slate-600">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr
                            v-for="emp in employees"
                            :key="emp.id"
                            class="hover:bg-slate-50/50 transition-colors"
                        >
                            <td class="py-3 px-4 font-medium text-slate-800">
                                {{ emp.name }}
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                {{ emp.extension }}
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                {{ emp.department ?? '—' }}
                            </td>
                            <td class="py-3 px-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium"
                                    :class="statusClass(emp.status)"
                                >
                                    {{ statusLabel(emp.status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2 justify-end">
                                    <button
                                        class="px-2 py-1 text-xs rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50"
                                        @click="openEdit(emp)"
                                    >
                                        تعديل
                                    </button>
                                    <button
                                        class="px-2 py-1 text-xs rounded-lg border border-red-200 text-red-600 hover:bg-red-50"
                                        @click="confirmDelete(emp)"
                                    >
                                        حذف
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!employees.length && !loading">
                            <td colspan="5" class="py-8 text-center text-slate-400">
                                لا يوجد موظفون
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal -->
        <div
            v-if="showModal"
            class="fixed inset-0 z-40 flex items-center justify-center bg-slate-900/50"
        >
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">
                    {{ editingEmployee ? 'تعديل موظف' : 'إضافة موظف' }}
                </h3>
                <form @submit.prevent="saveEmployee" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">الاسم</label>
                        <input
                            v-model="form.name"
                            type="text"
                            required
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">الرقم الداخلي</label>
                        <input
                            v-model="form.extension"
                            type="text"
                            required
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">القسم</label>
                        <input
                            v-model="form.department"
                            type="text"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">الحالة</label>
                        <select
                            v-model="form.status"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                        >
                            <option value="online">متصل</option>
                            <option value="on-call">على مكالمة</option>
                            <option value="idle">خامل</option>
                            <option value="break">استراحة</option>
                            <option value="late">متأخر</option>
                            <option value="offline">غير متصل</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-2 mt-4">
                        <button
                            type="button"
                            class="px-4 py-2 text-sm rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50"
                            @click="closeModal"
                        >
                            إلغاء
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 text-sm rounded-lg bg-sky-600 hover:bg-sky-700 text-white font-medium disabled:opacity-60"
                            :disabled="saving"
                        >
                            {{ saving ? 'جارٍ الحفظ...' : 'حفظ' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import axios from 'axios';

const api = axios.create({
    baseURL: 'http://127.0.0.1:8000/api',
});

const employees = ref([]);
const loading = ref(false);
const saving = ref(false);

const filters = reactive({
    search: '',
});

const showModal = ref(false);
const editingEmployee = ref(null);

const form = reactive({
    name: '',
    extension: '',
    department: '',
    status: 'offline',
});

const applyAuth = () => {
    const token = localStorage.getItem('token');
    if (token) {
        api.defaults.headers.common.Authorization = `Bearer ${token}`;
    }
};

const fetchEmployees = async () => {
    loading.value = true;
    applyAuth();
    try {
        const params = {};
        if (filters.search) {
            params.name = filters.search;
        }
        const { data } = await api.get('/employees', { params });
        employees.value = data.data ?? data;
    } finally {
        loading.value = false;
    }
};

const openCreate = () => {
    editingEmployee.value = null;
    form.name = '';
    form.extension = '';
    form.department = '';
    form.status = 'offline';
    showModal.value = true;
};

const openEdit = (emp) => {
    editingEmployee.value = emp;
    form.name = emp.name;
    form.extension = emp.extension;
    form.department = emp.department;
    form.status = emp.status;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

const saveEmployee = async () => {
    saving.value = true;
    applyAuth();
    try {
        if (editingEmployee.value) {
            await api.put(`/employees/${editingEmployee.value.id}`, form);
        } else {
            await api.post('/employees', form);
        }
        await fetchEmployees();
        showModal.value = false;
    } finally {
        saving.value = false;
    }
};

const confirmDelete = async (emp) => {
    if (!confirm(`هل أنت متأكد من حذف الموظف ${emp.name}؟`)) return;
    applyAuth();
    await api.delete(`/employees/${emp.id}`);
    await fetchEmployees();
};

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

onMounted(fetchEmployees);
</script>

