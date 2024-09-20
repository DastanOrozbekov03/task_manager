<template>
    <div>
        <h2>Управление Пользователями</h2>
        <button @click="showCreateForm = !showCreateForm" class="btn btn-primary">
            {{ showCreateForm ? 'Скрыть форму' : 'Добавить Пользователя' }}
        </button>

        <!-- Форма создания пользователя -->
        <div v-if="showCreateForm">
            <h3>Создать Пользователя</h3>
            <form @submit.prevent="createUser">
                <div>
                    <label for="name">Имя</label>
                    <input type="text" id="name" v-model="newUser.name" required>
                </div>
                <div>
                    <label for="email">Email</label>
                    <input type="email" id="email" v-model="newUser.email" required>
                </div>
                <div>
                    <label for="password">Пароль</label>
                    <input type="password" id="password" v-model="newUser.password" required>
                </div>
                <div>
                    <label for="role">Роль</label>
                    <select id="role" v-model="newUser.role" required>
                        <option value="user">Пользователь</option>
                        <option value="admin">Администратор</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Создать</button>
            </form>
            <div v-if="createError" class="error">
                {{ createError }}
            </div>
        </div>

        <!-- Список пользователей -->
        <div>
            <h3>Список Пользователей</h3>
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Имя</th>
                    <th>Email</th>
                    <th>Роль</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="user in users" :key="user.id">
                    <td>{{ user.id }}</td>
                    <td>{{ user.name }}</td>
                    <td>{{ user.email }}</td>
                    <td>{{ user.role }}</td>
                    <td>
                        <button @click="editUser(user)" class="btn btn-warning">Редактировать</button>
                        <button @click="deleteUser(user.id)" class="btn btn-danger">Удалить</button>
                    </td>
                </tr>
                </tbody>
            </table>
            <div v-if="users.length === 0">
                <p>Пользователи не найдены.</p>
            </div>
        </div>

        <!-- Форма редактирования пользователя -->
        <div v-if="showEditForm">
            <h3>Редактировать Пользователя</h3>
            <form @submit.prevent="updateUser">
                <div>
                    <label for="edit-name">Имя</label>
                    <input type="text" id="edit-name" v-model="editUserData.name" required>
                </div>
                <div>
                    <label for="edit-email">Email</label>
                    <input type="email" id="edit-email" v-model="editUserData.email" required>
                </div>
                <div>
                    <label for="edit-role">Роль</label>
                    <select id="edit-role" v-model="editUserData.role" required>
                        <option value="user">Пользователь</option>
                        <option value="admin">Администратор</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Сохранить</button>
                <button @click="cancelEdit" class="btn btn-secondary">Отмена</button>
            </form>
            <div v-if="editError" class="error">
                {{ editError }}
            </div>
        </div>
    </div>
</template>

<script setup>
import {ref, onMounted} from "vue";
import axiosInstance from "../../services/axios.js";

const users = ref([]);
const showCreateForm = ref(false);
const newUser = ref({
    name: '',
    email: '',
    password: '',
    role: 'user',
});
const createError = ref('');

const showEditForm = ref(false);
const editUserData = ref({});
const editError = ref('');

const fetchUsers = async () => {
    try {
        const response = await axiosInstance.get('/admin/users');
        users.value = response.data.data;
    } catch (error) {
        console.error('Error on fetching users:', error);
    }
};

const createUser = async () => {
    createError.value = '';
    try {
        const response = await axiosInstance.post('/admin/users', newUser.value);
        users.value.push(response.data);
        newUser.value = {
            name: '',
            email: '',
            password: '',
            role: 'user',
        };
        showCreateForm.value = false;
    } catch (error) {
        console.error('Error on create user:', error);
        createError.value = error.response?.data?.message || "Can't create user."
    }
};

const editUser = (user) => {
    showEditForm.value = true;
    editUserData.value = {...user};
}

const updateUser = async () => {
    editError.value = '';
    try {
        // Исправлено: добавлен id и передача данных
        const response = await axiosInstance.put(`/admin/users/${editUserData.value.id}`, editUserData.value);
        const index = users.value.findIndex(u => u.id === editUserData.value.id);
        if (index !== -1) {
            users.value[index] = response.data;
        }
        showEditForm.value = false;
    } catch (error) {
        console.error('Ошибка при обновлении пользователя:', error);
        editError.value = error.response?.data?.message || 'Не удалось обновить пользователя.';
    }
};

const deleteUser = async (userId) => {
    if (!confirm('Вы уверены, что хотите удалить этого пользователя?')) return;

    try {
        await axiosInstance.delete(`/admin/users/${userId}`);
        users.value = users.value.filter(user => user.id !== userId);
    } catch (error) {
        console.error('Ошибка при удалении пользователя:', error);
    }
};

const cancelEdit = () => {
    showEditForm.value = false;
    editUserData.value = {};
};

onMounted(() => {
    fetchUsers();
});
</script>

<style scoped>
nav {
    margin-bottom: 20px;
}

nav a {
    margin-right: 10px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th, td {
    border: 1px solid #ddd;
    padding: 8px;
}

th {
    background-color: #f2f2f2;
}

.btn {
    margin-right: 5px;
}

.error {
    color: red;
    margin-top: 10px;
}
</style>
