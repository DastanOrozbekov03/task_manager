<script setup>
import axiosInstance from "../../srs/services/axios.js";
import {ref, onMounted} from "vue";
import {useRoute, useRouter} from "vue-router";

const route = useRoute();
const router = useRouter();

const task = ref({
    title: '',
    description: '',
    priority: '',
    status: '',
    start_date: '',
    end_date: ''
});
const error = ref(null);

const fetchTask = async () => {
    const taskId = route.params.id; // Получаем ID из маршрута
    if (!taskId) {
        console.error('Task ID is undefined');
        error.value = 'Task ID is missing';
        return;
    }

    try {
        const response = await axiosInstance.get(`/tasks/${taskId}`);
        task.value = response.data.data;
    } catch (err) {
        if (err.response) {
            console.error('Error fetching task:', err.response.data);
            error.value = `Error loading task: ${err.response.data.message}`;
        } else {
            console.error('Error fetching task:', err);
            error.value = 'Error loading task';
        }
    }
};


const updateTask = async () => {
    error.value = null;
    try {
        await axiosInstance.put(`/tasks/${route.params.id}`, task.value); // Используем axiosInstance
        router.push('/tasks');
    } catch (err) {
        if (err.response) {
            console.error('Error updating task:', err.response.data);
            error.value = `Error updating task: ${err.response.data.message}`;
        } else {
            console.error('Error updating task:', err);
            error.value = 'Error updating task. Try again later.';
        }
    }
};



onMounted(() => {
    fetchTask();
});
</script>

<template>
    <div class="edit-task-container">
        <h2>Edit Task</h2>
        <form @submit.prevent="updateTask" class="task-form">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" v-model="task.title" placeholder="Enter task title" required class="form-input">
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" v-model="task.description" placeholder="Enter task description" required class="form-input"></textarea>
            </div>

            <div class="form-group">
                <label for="priority">Priority</label>
                <select id="priority" v-model="task.priority" required class="form-input">
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" v-model="task.status" required class="form-input">
                    <option value="new">New</option>
                    <option value="in_progress">In progress</option>
                    <option value="completed">Completed</option>
                </select>
            </div>

            <div class="form-group">
                <label for="start_date">Start Date</label>
                <input type="date" id="start_date" v-model="task.start_date" required class="form-input">
            </div>

            <div class="form-group">
                <label for="end_date">End Date</label>
                <input type="date" id="end_date" v-model="task.end_date" class="form-input">
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>

            <div v-if="error" class="error">
                <p>{{ error }}</p>
            </div>
        </form>
    </div>
</template>
<style scoped>
.edit-task-container {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 8px;
    background-color: #f9f9f9;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.task-form {
    display: flex;
    flex-direction: column;
}

.form-group {
    margin-bottom: 15px;
}

label {
    margin-bottom: 5px;
    font-weight: bold;
}

.form-input {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
}

.btn-primary {
    padding: 10px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.error {
    color: red;
    margin-top: 10px;
}
</style>
