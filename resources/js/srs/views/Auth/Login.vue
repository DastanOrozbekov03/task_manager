<script setup>
import { ref } from 'vue';
import { useStore } from 'vuex';
import { useRouter } from 'vue-router';

const loginDate = ref({
  email: '',
  password: ''
})

const store = useStore();
const router = useRouter();


const login = async () => {
    try {
        await store.dispatch('login', {
            email: loginDate.value.email,
            password: loginDate.value.password
        });
        router.push('/dashboard');
    } catch (error) {
        console.error(error);
        alert(error.response.data.message || "login failed!");
    }
};
</script>

<template>
    <div class="container mt-5">
        <h2>Login</h2>
        <form @submit.prevent="login">
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input v-model="loginDate.email" type="email" class="form-control" id="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input v-model="loginDate.password" type="password" class="form-control" id="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        <p class="mt-3">Don't have an account? <router-link to="/register">Register here</router-link></p>
    </div>
</template>

<style scoped>

</style>
