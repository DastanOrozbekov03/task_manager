<script setup>
import axiosInstance from "../../srs/services/axios.js";
import {ref, onMounted} from "vue";
import {useRouter} from "vue-router";
import NavBar from "../../srs/components/NavBar.vue";


const task = ref(
    {
        title: '',
        description: '',
        priority: 'low',
        status: 'new',
        start_date: '',
        end_date: ''
    });
const error = ref(null);
const router = useRouter();

const createTask = async () => {
    error.value = null

    if (!task.value.title) {
        error.value = 'Title is required.';
        return;
    }
    if (!task.value.start_date) {
        error.value = 'Start date is required.';
        return;
    }
    try {
        const response = await axiosInstance.post('/tasks', task.value);
        console.log('Task created successfully:', response.data)
        router.push('/tasks');
    } catch (err) {
        console.error('Error creating Task:', err);
        if (err.response && err.response.data) {
            error.value = err.response.data.message || 'Failed to create task.';
        } else {
            error.value = 'Failed to create task.';
        }
    }
};
</script>

<template>
<NavBar/>


    <div>
        <h2>Create Task</h2>
        <form @submit.prevent="createTask">
            <div>
                <label for="TaskName">Title</label>
                <input type="text" id="TaskId" v-model="task.title" placeholder="Enter task title">
            </div>

            <div>
                <label for="TaskDescription">Description</label>
                <textarea  id="TaskDescription" v-model="task.description" placeholder="Enter task description"></textarea>
            </div>

            <div>
                <label for="priority">Priority</label>
                <select id="priority" v-model="task.priority" required>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
            </div>
            <div>
                <label for="status">Status</label>
                <select id="status" v-model="task.status" required>
                    <option value="new">New</option>
                    <option value="in_progress">In progress</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            <div>
                <label for="start_date">Start Date</label>
                <input type="date" id="start_date" v-model="task.start_date" required>
            </div>

            <div>
                <label for="end_date">End Date</label>
                <input type="date" id="end_date" v-model="task.end_date">
            </div>
            <button type="submit" class="btn btn-primary">Create Task</button>

            <div v-if="error" class="error">
                <p>{{ error }}</p>
            </div>
        </form>
    </div>

</template>

<style scoped>

</style>
