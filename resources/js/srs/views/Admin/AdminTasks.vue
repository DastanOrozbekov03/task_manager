<script setup>
import { ref, onMounted } from 'vue';
import axiosInstance from '../../services/axios.js';

const tasks = ref([]);
const users = ref([]);
const showCreateForm = ref(false);
const newTask = ref({
    title: '',
    description: '',
    priority: 'low',
    status: 'new',
    start_date: '',
    end_date: '',
    user_id: '',
});
const createError = ref('');

const showEditForm = ref(false);
const editTaskData = ref({});
const editError = ref('');

const fetchTasks = async () => {
    try {
        const response = await axiosInstance.get('/admin/tasks');
        tasks.value = response.data.data;
    } catch (error) {
        console.error('Ошибка при получении задач:', error);
    }
};

const fetchUsers = async () => {
    try {
        const response = await axiosInstance.get('/admin/users');
        users.value = response.data.data;
    } catch (error) {
        console.error('Ошибка при получении пользователей:', error);
    }
};

const createTask = async () => {
    createError.value = '';
    try {
        const response = await axiosInstance.post('/admin/tasks', newTask.value);
        tasks.value.push(response.data);
        newTask.value = {
            title: '',
            description: '',
            priority: 'low',
            status: 'new',
            start_date: '',
            end_date: '',
            user_id: '',
        };
        showCreateForm.value = false;
    } catch (error) {
        console.error('Ошибка при создании задачи:', error);
        createError.value = error.response?.data?.message || 'Не удалось создать задачу.';
    }
};

const editTask = (task) => {
    showEditForm.value = true;
    editTaskData.value = { ...task };
};

const updateTask = async () => {
    editError.value = '';
    try {
        const response = await axiosInstance.put(`/admin/tasks/${editTaskData.value.id}`, editTaskData.value);
        const index = tasks.value.findIndex(t => t.id === editTaskData.value.id);
        if (index !== -1) {
            tasks.value[index] = response.data;
        }
        showEditForm.value = false;
    } catch (error) {
        console.error('Ошибка при обновлении задачи:', error);
        editError.value = error.response?.data?.message || 'Не удалось обновить задачу.';
    }
};

const deleteTask = async (taskId) => {
    if (!confirm('Вы уверены, что хотите удалить эту задачу?')) return;

    try {
        await axiosInstance.delete(`/admin/tasks/${taskId}`);
        tasks.value = tasks.value.filter(task => task.id !== taskId);
    } catch (error) {
        console.error('Ошибка при удалении задачи:', error);
    }
};

const cancelEdit = () => {
    showEditForm.value = false;
    editTaskData.value = {};
};

onMounted(() => {
    fetchTasks();
    fetchUsers();
});
</script>

<template>
    <div>
        <h2>Управление Задачами</h2>
        <button @click="showCreateForm = !showCreateForm" class="btn btn-primary">
            {{ showCreateForm ? 'Скрыть форму' : 'Добавить Задачу' }}
        </button>

        <!-- Форма создания задачи -->
        <div v-if="showCreateForm">
            <h3>Создать Задачу</h3>
            <form @submit.prevent="createTask">
                <div>
                    <label for="title">Название</label>
                    <input type="text" id="title" v-model="newTask.title" required>
                </div>
                <div>
                    <label for="description">Описание</label>
                    <textarea id="description" v-model="newTask.description" required></textarea>
                </div>
                <div>
                    <label for="priority">Приоритет</label>
                    <select id="priority" v-model="newTask.priority" required>
                        <option value="low">Низкий</option>
                        <option value="medium">Средний</option>
                        <option value="high">Высокий</option>
                    </select>
                </div>
                <div>
                    <label for="status">Статус</label>
                    <select id="status" v-model="newTask.status" required>
                        <option value="new">Новая</option>
                        <option value="in_progress">В процессе</option>
                        <option value="completed">Выполнена</option>
                    </select>
                </div>
                <div>
                    <label for="start_date">Дата начала</label>
                    <input type="date" id="start_date" v-model="newTask.start_date" required>
                </div>
                <div>
                    <label for="end_date">Дата окончания</label>
                    <input type="date" id="end_date" v-model="newTask.end_date">
                </div>
                <div>
                    <label for="user_id">Назначить пользователю</label>
                    <select id="user_id" v-model="newTask.user_id" required>
                        <option v-for="user in users" :key="user.id" :value="user.id">
                            {{ user.name }} ({{ user.email }})
                        </option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Создать</button>
            </form>
            <div v-if="createError" class="error">
                {{ createError }}
            </div>
        </div>

        <!-- Список задач -->
        <div>
            <h3>Список Задач</h3>
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Описание</th>
                    <th>Приоритет</th>
                    <th>Статус</th>
                    <th>Дата начала</th>
                    <th>Дата окончания</th>
                    <th>Назначен</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="task in tasks" :key="task.id">
                    <td>{{ task.id }}</td>
                    <td>{{ task.title }}</td>
                    <td>{{ task.description }}</td>
                    <td>{{ task.priority }}</td>
                    <td>{{ task.status }}</td>
                    <td>{{ task.start_date }}</td>
                    <td>{{ task.end_date }}</td>
                    <td>{{ task.user.name }}</td>
                    <td>
                        <button @click="editTask(task)" class="btn btn-warning">Редактировать</button>
                        <button @click="deleteTask(task.id)" class="btn btn-danger">Удалить</button>
                    </td>
                </tr>
                </tbody>
            </table>
            <div v-if="tasks.length === 0">
                <p>Задачи не найдены.</p>
            </div>
        </div>

        <!-- Форма редактирования задачи -->
        <div v-if="showEditForm">
            <h3>Редактировать Задачу</h3>
            <form @submit.prevent="updateTask">
                <div>
                    <label for="edit-title">Название</label>
                    <input type="text" id="edit-title" v-model="editTaskData.title" required>
                </div>
                <div>
                    <label for="edit-description">Описание</label>
                    <textarea id="edit-description" v-model="editTaskData.description" required></textarea>
                </div>
                <div>
                    <label for="edit-priority">Приоритет</label>
                    <select id="edit-priority" v-model="editTaskData.priority" required>
                        <option value="low">Низкий</option>
                        <option value="medium">Средний</option>
                        <option value="high">Высокий</option>
                    </select>
                </div>
                <div>
                    <label for="edit-status">Статус</label>
                    <select id="edit-status" v-model="editTaskData.status" required>
                        <option value="new">Новая</option>
                        <option value="in_progress">В процессе</option>
                        <option value="completed">Выполнена</option>
                    </select>
                </div>
                <div>
                    <label for="edit-start_date">Дата начала</label>
                    <input type="date" id="edit-start_date" v-model="editTaskData.start_date" required>
                </div>
                <div>
                    <label for="edit-end_date">Дата окончания</label>
                    <input type="date" id="edit-end_date" v-model="editTaskData.end_date">
                </div>
                <div>
                    <label for="edit-user_id">Назначить пользователю</label>
                    <select id="edit-user_id" v-model="editTaskData.user_id" required>
                        <option v-for="user in users" :key="user.id" :value="user.id">
                            {{ user.name }} ({{ user.email }})
                        </option>
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

<style scoped>

</style>
