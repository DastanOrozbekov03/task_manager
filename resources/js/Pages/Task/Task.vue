<script setup>
import axiosInstance from "../../srs/services/axios.js";
import {onMounted, ref, watch} from "vue";
import NavBar from "../../srs/components/NavBar.vue";
import '@fortawesome/fontawesome-free/css/all.min.css';

const currentPage = ref(1);
const totalPages = ref(0)
const selectedTask = ref(null);
const showModal = ref(false);
const showFilterPanel = ref(false);
const tasks = ref([]);

const error = ref(null);

const noTaskMessage = ref('');
const searchQuery = ref('');
const selectedPriorities = ref([]);
const selectStatus = ref([]);
const filterStartDate = ref('');
const filterEndDate = ref('');

const fetchTasks = async (page = 1) => {
    try {
        error.value = null;
        const response = await axiosInstance.get(`/tasks`, {
            params: {
                page,
                search: searchQuery.value,
                priorities: selectedPriorities.value.join(','),
                statuses: selectStatus.value.join(','),
                start_date: filterStartDate.value,
                end_date: filterEndDate.value
            }
        });

        if (response.data.data.length === 0) {
            tasks.value = [];
            noTaskMessage.value = 'No tasks found with the selected filters.';
        } else {
            tasks.value = response.data.data;
            noTaskMessage.value = '';
        }

        currentPage.value = response.data.current_page;
        totalPages.value = response.data.last_page;
    } catch (error) {
        error.value = 'An error occurred while fetching tasks. Please try again later.'
        console.error("Error fetching tasks:", error)
    }
};

watch([searchQuery, selectedPriorities, selectedPriorities, filterEndDate, filterStartDate], () => {
    currentPage.value = 1;
    fetchTasks((currentPage.value))
})

const showTasks = async (taskId) => {
    try {
        error.value = null;
        const { data } = await axiosInstance.get(`/tasks/${taskId}`);
        console.log(data)
        selectedTask.value=data.data;
        showModal.value = true;
    } catch (error) {
        error.value = 'An error occurred while fetching task details. Please try again later.';
        console.error('Error fetching task details:', error);
    }
};

const closeModal = () => {
    showModal.value = false;
}

const deleteTask = async (taskId) => {
    try {
        error.value = null;
        await axiosInstance.delete(`/tasks/${taskId}`);
    } catch (err) {
        console.error('Error deleting task:', error);
        if (err.response && err.response.data) {
            error.value =  'You can`t delete the task.'
        } else {
            alert('Failed to delete task');
        }
    }
};
const changePage = (page) => {
    if (page > 0 && page <= totalPages.value) {
        fetchTasks(page);
    }
};

const applyFilters = () => {
    currentPage.value = 1;
    fetchTasks(currentPage.value)
}

onMounted( () => {
    fetchTasks();
});
</script>
<template>
<NavBar />
<div class="container">
<h1>Task Manager</h1>

<!-- Сообщение об ошибке -->

    <div class="top-actions">
        <router-link :to="{ name: 'create-task' }" class="create-task-btn">
        <i class="fas fa-plus"></i> Create Task
    </router-link>

        <button @click="showFilterPanel = !showFilterPanel" class="btn btn-filter">
        <i class="fas" :class="showFilterPanel ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
        {{ showFilterPanel ? 'Hide Filters' : 'Show Filters' }}
    </button>
    </div>

    <div v-if="showFilterPanel" class="filter-panel">
        <h2>Search and Filter</h2>
        <form @submit.prevent="applyFilters">
            <div class="filter-group">
                <label for="search">Search</label>
                <input type="text" id="search" v-model="searchQuery" placeholder="Search task">
            </div>

            <div class="filter-group">
                <label for="filterPriority">Priority</label>
                <div class="checkbox-group">
                    <label><input type="checkbox" value="low" v-model="selectedPriorities">Low</label>
                    <label><input type="checkbox" value="medium" v-model="selectedPriorities">Medium</label>
                    <label><input type="checkbox" value="high" v-model="selectedPriorities">High</label>
                </div>
            </div>

            <div class="filter-group">
                <label for="filterStatus">Status</label>
                <div class="checkbox-group">
                    <label><input type="checkbox" value="new" v-model="selectStatus">New</label>
                    <label><input type="checkbox" value="in_progress" v-model="selectStatus">In Progress</label>
                    <label><input type="checkbox" value="completed" v-model="selectStatus">Completed</label>
                </div>
            </div>

            <div class="filter-group">
                <label for="startDate">Start Date</label>
                <input type="date" id="startDate" v-model="filterStartDate">
            </div>
            <div class="filter-group">
                <label for="endDate">End Date</label>
                <input type="date" id="endDate" v-model="filterEndDate">
            </div>

            <button type="submit" class="btn btn-primary">Apply Filters</button>
        </form>
    </div>

    <div class="tasks-section">
        <div v-if="tasks.length > 0" class="tasks-grid">
            <div v-for="taskItem in tasks" :key="taskItem.id" class="task-card">
                <div class="task-header">
                    <h3>{{ taskItem.title }}</h3>
                    <span :class="['priority', taskItem.priority]">{{ taskItem.priority }}</span>
                </div>
                <p class="task-description">{{ taskItem.description }}</p>
                <div class="task-footer">
                    <span :class="['status', taskItem.status]">{{ taskItem.status }}</span>
                    <div class="task-actions">
                        <button @click="showTasks(taskItem.id)" class="btn btn-detail" title="Detail">
                        <i class="fas fa-info-circle"></i>
                    </button>
                        <router-link :to="{ name: 'task-edit', params: { id: taskItem.id } }">
                        <button class="btn btn-edit" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                    </router-link>
                        <button @click="deleteTask(taskItem.id)" class="btn btn-delete" title="Delete">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                    </div>
                </div>
            </div>
        </div>
        <div v-else-if="noTaskMessage" class="no-tasks-message">
            <p>{{ noTaskMessage }}</p>
        </div>
    </div>

    <div v-if="error" class="error-banner">
        <p>{{ error }}</p>
    </div>
    <!-- Модальное окно -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
        <div class="modal-content">
            <h2>Task Detail</h2>
        <div v-if="selectedTask">
            <p><strong>Title:</strong> {{ selectedTask.title }}</p>
            <p><strong>Description:</strong> {{ selectedTask.description }}</p>
            <p><strong>Priority:</strong> {{ selectedTask.priority }}</p>
            <p><strong>Status:</strong> {{ selectedTask.status }}</p>
            <p><strong>Start Date:</strong> {{ selectedTask.start_date }}</p>
            <p><strong>End Date:</strong> {{ selectedTask.end_date }}</p>
        </div>
        <button @click="closeModal" class="btn btn-secondary">Close</button>
    </div>
</div>

<!-- Подвал и пагинация -->
<footer>
    <div class="pagination">
        <button @click.prevent="changePage(currentPage - 1)" :disabled="currentPage === 1" class="btn btn-pagination">
            <i class="fas fa-arrow-left"></i> Previous
        </button>
        <button v-for="page in totalPages" :key="page" @click.prevent="changePage(page)" :class="['btn btn-pagination', { active: page === currentPage }]" >
            {{ page }}
        </button>
        <button @click.prevent="changePage(currentPage + 1)" :disabled="currentPage === totalPages" class="btn btn-pagination">
            Next <i class="fas fa-arrow-right"></i>
        </button>
    </div>
</footer>
</div>
</template>

<style scoped>
/* Подключение иконок Font Awesome (добавьте это в index.html вашего проекта) */
/* <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-p+zVGYHoRg0hT+V+bZJh3v5vq7MxYFFibg4nIxTnlGY24RrTf1nI+h/KVh8E3wVrm9EeOaxf7Nz0SLgM8KGgKg==" crossorigin="anonymous" referrerpolicy="no-referrer" /> */

/* Общие стили */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

h1 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
}

/* Сообщение об ошибке */
.error-banner {
    background-color: #dc3545;
    color: white;
    padding: 15px;
    text-align: center;
    border-radius: 5px;
    margin-bottom: 20px;
    animation: fadeIn 0.5s ease-in-out;
}

/* Анимация появления */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Верхние действия */
.top-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.create-task-btn {
    background-color: #28a745;
    color: white;
    padding: 10px 15px;
    border-radius: 5px;
    text-decoration: none;
    display: flex;
    align-items: center;
    transition: background-color 0.3s ease;
}

.create-task-btn i {
    margin-right: 5px;
}

.create-task-btn:hover {
    background-color: #218838;
}

.btn-filter {
    background-color: #007bff;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    display: flex;
    align-items: center;
    transition: background-color 0.3s ease;
}

.btn-filter i {
    margin-right: 5px;
}

.btn-filter:hover {
    background-color: #0056b3;
}

/* Панель фильтров */
.filter-panel {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    animation: slideDown 0.5s ease-in-out;
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

.filter-panel h2 {
    margin-top: 0;
    color: #333;
    margin-bottom: 15px;
}

.filter-group {
    margin-bottom: 15px;
}

.filter-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #555;
}

.checkbox-group {
    display: flex;
    gap: 10px;
}

.checkbox-group label {
    font-weight: normal;
    color: #555;
}

.filter-panel input[type="text"],
.filter-panel input[type="date"] {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    transition: border-color 0.3s ease;
}

.filter-panel input[type="text"]:focus,
.filter-panel input[type="date"]:focus {
    border-color: #007bff;
}

.btn-primary {
    background-color: #007bff;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-primary:hover {
    background-color: #0056b3;
}

/* Секция задач */
.tasks-section {
    margin-bottom: 30px;
}

.tasks-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
}

.task-card {
    background-color: #fff;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.task-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
}

.task-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.task-header h3 {
    margin: 0;
    font-size: 1.2em;
    color: #333;
}

.priority {
    padding: 5px 10px;
    border-radius: 12px;
    color: white;
    font-size: 0.8em;
    text-transform: capitalize;
}

.priority.low {
    background-color: #17a2b8;
}

.priority.medium {
    background-color: #ffc107;
}

.priority.high {
    background-color: #dc3545;
}

.task-description {
    flex-grow: 1;
    color: #555;
    margin-bottom: 15px;
}

.task-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.status {
    padding: 5px 10px;
    border-radius: 12px;
    color: white;
    font-size: 0.8em;
    text-transform: capitalize;
}

.status.new {
    background-color: #28a745;
}

.status.in_progress {
    background-color: #17a2b8;
}

.status.completed {
    background-color: #6c757d;
}

.task-actions {
    display: flex;
    gap: 10px;
}

.task-actions .btn {
    padding: 8px 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    color: white;
    transition: background-color 0.3s ease;
}

.btn-detail {
    background-color: #17a2b8;
}

.btn-detail:hover {
    background-color: #138496;
}

.btn-edit {
    background-color: #ffc107;
    color: #212529;
}

.btn-edit:hover {
    background-color: #e0a800;
}

.btn-delete {
    background-color: #dc3545;
}

.btn-delete:hover {
    background-color: #c82333;
}

/* Сообщение об отсутствии задач */
.no-tasks-message {
    text-align: center;
    color: #dc3545;
    font-weight: bold;
    margin-top: 20px;
}

/* Модальное окно */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeInModal 0.3s ease-in-out;
    z-index: 1000;
}

@keyframes fadeInModal {
    from { opacity: 0; }
    to { opacity: 1; }
}

.modal-content {
    background: white;
    padding: 30px;
    border-radius: 8px;
    width: 90%;
    max-width: 600px;
    position: relative;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    animation: slideDownModal 0.3s ease-in-out;
}

@keyframes slideDownModal {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

.modal-content h2 {
    margin-top: 0;
    color: #333;
    margin-bottom: 15px;
}

.modal-content p {
    margin: 10px 0;
    color: #555;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-secondary:hover {
    background-color: #5a6268;
}

/* Подвал и пагинация */
footer {
    text-align: center;
    margin-top: 30px;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 10px;
}

.btn-pagination {
    background-color: #007bff;
    color: white;
    padding: 8px 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-pagination:hover:not(.active) {
    background-color: #0056b3;
}

.btn-pagination.active {
    background-color: #0056b3;
}

.btn-pagination:disabled {
    background-color: #ccc;
    cursor: not-allowed;
}

/* Адаптивность */
@media (max-width: 768px) {
    .top-actions {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }

    .filter-panel form {
        flex-direction: column;
    }

    .checkbox-group {
        flex-direction: column;
    }

    .tasks-grid {
        grid-template-columns: 1fr;
    }
}
</style>
