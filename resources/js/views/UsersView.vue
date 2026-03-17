<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-lg font-semibold text-slate-800">إدارة المستخدمين</h2>
                <p class="text-sm text-slate-500 mt-1">إضافة وتعديل وحذف حسابات الدخول للنظام</p>
            </div>
            <button
                class="inline-flex items-center gap-2 px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-lg transition-colors"
                @click="openCreate"
            >
                <span class="text-lg leading-none">＋</span>
                <span>إضافة مستخدم</span>
            </button>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="py-3 px-4 text-right font-medium text-slate-600">اسم المستخدم</th>
                            <th class="py-3 px-4 text-right font-medium text-slate-600">الاسم</th>
                            <th class="py-3 px-4 text-right font-medium text-slate-600">الدور</th>
                            <th class="py-3 px-4 text-right font-medium text-slate-600">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr
                            v-for="user in users"
                            :key="user.id"
                            class="hover:bg-slate-50/50 transition-colors"
                        >
                            <td class="py-3 px-4 text-slate-700">
                                {{ user.username }}
                            </td>
                            <td class="py-3 px-4 font-medium text-slate-800">
                                {{ user.name }}
                            </td>
                            <td class="py-3 px-4 text-slate-700">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium"
                                    :class="user.role === 'admin' ? 'bg-emerald-100 text-emerald-700' : 'bg-sky-100 text-sky-700'"
                                >
                                    {{ user.role === 'admin' ? 'مدير' : 'مشرف' }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2 justify-end">
                                    <button
                                        class="px-2 py-1 text-xs rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50"
                                        @click="openEdit(user)"
                                    >
                                        تعديل
                                    </button>
                                    <button
                                        class="px-2 py-1 text-xs rounded-lg border border-red-200 text-red-600 hover:bg-red-50"
                                        @click="confirmDelete(user)"
                                    >
                                        حذف
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!users.length && !loading">
                            <td colspan="4" class="py-8 text-center text-slate-400">
                                لا توجد حسابات مستخدمين
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
                    {{ editingUser ? 'تعديل مستخدم' : 'إضافة مستخدم' }}
                </h3>
                <form @submit.prevent="saveUser" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">اسم المستخدم</label>
                        <input
                            v-model="form.username"
                            type="text"
                            required
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                        />
                    </div>
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
                        <label class="block text-sm font-medium text-slate-700 mb-1">الدور</label>
                        <select
                            v-model="form.role"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                        >
                            <option value="admin">مدير</option>
                            <option value="supervisor">مشرف</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            كلمة المرور
                            <span v-if="editingUser" class="text-xs text-slate-400">(اتركها فارغة للإبقاء عليها)</span>
                        </label>
                        <input
                            v-model="form.password"
                            type="password"
                            :required="!editingUser"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                        />
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

const users = ref([]);
const loading = ref(false);
const saving = ref(false);

const showModal = ref(false);
const editingUser = ref(null);

const form = reactive({
    username: '',
    name: '',
    role: 'supervisor',
    password: '',
});

const applyAuth = () => {
    const token = localStorage.getItem('token');
    if (token) {
        api.defaults.headers.common.Authorization = `Bearer ${token}`;
    }
};

const fetchUsers = async () => {
    loading.value = true;
    applyAuth();
    try {
        const { data } = await api.get('/users');
        users.value = data.data ?? data;
    } finally {
        loading.value = false;
    }
};

const openCreate = () => {
    editingUser.value = null;
    form.username = '';
    form.name = '';
    form.role = 'supervisor';
    form.password = '';
    showModal.value = true;
};

const openEdit = (user) => {
    editingUser.value = user;
    form.username = user.username;
    form.name = user.name;
    form.role = user.role;
    form.password = '';
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

const saveUser = async () => {
    saving.value = true;
    applyAuth();
    try {
        const payload = {
            username: form.username,
            name: form.name,
            role: form.role,
        };
        if (form.password) {
            payload.password = form.password;
        }

        if (editingUser.value) {
            await api.put(`/users/${editingUser.value.id}`, payload);
        } else {
            await api.post('/users', payload);
        }
        await fetchUsers();
        showModal.value = false;
    } finally {
        saving.value = false;
    }
};

const confirmDelete = async (user) => {
    if (!confirm(`هل أنت متأكد من حذف المستخدم ${user.username}؟`)) return;
    applyAuth();
    await api.delete(`/users/${user.id}`);
    await fetchUsers();
};

onMounted(fetchUsers);
</script>

