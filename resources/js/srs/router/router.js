import { createRouter, createWebHistory } from "vue-router";
import EditTask from "../../Pages/Task/EditTask.vue";
import Register from "../views/Auth/Register.vue";
import Login from "../views/Auth/Login.vue";
import Dashboard from "../views/Auth/Dashboard.vue";
import store from "../store/index.js";
import Task from "../../Pages/Task/Task.vue";
import CreateTask from "../../Pages/Task/CreateTask.vue";
import AdminDashboard from "../views/Admin/AdminDashboard.vue";
import AdminUser from "../views/Admin/AdminUsers.vue";
import AdminTasks from "../views/Admin/AdminTasks.vue";
import AdminUsers from "../views/Admin/AdminUsers.vue";

const routes = [
    {
        path: "/",
        name: 'Home',
        redirect: '/dashboard'
    },
    {
        path: '/register',
        name: 'Register',
        component: Register,
        meta: {guest: true}
    },
    {
        path: '/login',
        name: 'Login',
        component: Login,
        meta: {guest: true}
    },
    {
        path: '/dashboard',
        name: 'Dashboard',
        component: Dashboard,
        meta: {requiresAuth: true}
    },
    {
        path: "/tasks",
        name: 'Tasks',
        component: Task,
        meta: {requiresAuth: true}
    },
    {
        path: "/tasks/:id/edit",
        name: 'task-edit',
        component: EditTask,
        meta: {requiresAuth: true}
    },
    {
        path: '/create-task',
        name: 'create-task',
        component: CreateTask,
        meta: {requiresAuth: true}
    },
    {
        path: '/admin',
        name: 'AdminDashboard',
        component: AdminDashboard,
        meta: {requiresAuth: true},
    },
    {
        path: '/admin/users',
        name: 'AdminUsers',
        component: AdminUsers,
        meta: {requiresAuth: true},
    },
    {
        path: '/admin/tasks',
        name: 'AdminTasks',
        component: AdminTasks,
        meta: {requiresAuth: true},
    },

];

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes
})

router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
        if (!store.getters.isAuthenticated) {
            next({name: 'Login'});
        } else {
            next();
        }
    } else if (to.matched.some(record => record.meta.guest)) {
        if (store.getters.isAuthenticated) {
            next({ name: 'Dashboard'});
        } else {
            next();
        }
    } else {
        next();
    }
});

export default router;
