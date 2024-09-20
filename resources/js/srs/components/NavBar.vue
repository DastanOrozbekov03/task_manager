<script setup>
import {computed} from "vue";
import {useRouter} from "vue-router";
import {useStore} from "vuex";

const store = useStore();
const router = useRouter();

const isAuthenticated = computed(() => store.getters.isAuthenticated);

const logout = async () => {
    try {
        await store.dispatch('logout');
        router.push('/login');
    } catch (error) {
        console.error(error);
        alert('Logout failed!');
    }
};

</script>

<template>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <router-link class="navbar-brand" to="/">Task Manager</router-link>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item" v-if="!isAuthenticated">
                    <router-link class="nav-link" to="/login">Login</router-link>
                </li>
                <li class="nav-item" v-if="!isAuthenticated">
                    <router-link class="nav-item" to="/register">Register</router-link>
                </li>
                <li class="nav-item" v-if="isAuthenticated">
                    <router-link class="nav-link" to="/dashboard">Dashboard</router-link>
                </li>
                <li class="nav-item" v-if="isAuthenticated">
                <a class="nav-item" href="#" @click.prevent="logout">Logout</a>

                </li>
            </ul>

        </div>
    </div>
</nav>
</template>

<style scoped>

</style>
