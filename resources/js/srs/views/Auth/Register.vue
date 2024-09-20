<script setup>
import {ref} from "vue";
import {useRouter} from "vue-router";
import {useStore} from "vuex";


const registerData = ref({
    name: '',
    email: '',
    password: '',
    password_confirm: '',
})
const errors = ref({
    email: '',
    password: '',
})

const store = useStore();
const router = useRouter()

const register = async () => {
    if (registerData.value.password !== registerData.value.password_confirm ) {
        alert("Passwords do not match!");
        return;
    }
    try {
        await store.dispatch('register', {
            name: registerData.value.name,
            email: registerData.value.email,
            password: registerData.value.password
        });
        router.push('/dashboard');
    } catch (error) {
            console.error(error);
            alert(error.response.data.message || "Registration failed");
    }
};
</script>

<template>
    <div class="container mt-5">
        <h2>Register</h2>
        <form @submit.prevent="register">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input v-model="registerData.name" type="text" class="form-control" id="name" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input v-model="registerData.email" type="email" class="form-control" id="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input v-model="registerData.password" type="password" class="form-control" id="password" required>
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input v-model="registerData.password_confirm" type="password" class="form-control" id="password_confirmation" required>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
        <p class="mt-3">Already have an account? <router-link to="/login">Login here</router-link></p>
    </div>
</template>

<style scoped>

</style>
