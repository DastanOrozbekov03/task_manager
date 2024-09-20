<script setup>
import {computed, onMounted} from "vue";
import {useStore} from "vuex";
import NavBar from "../../components/NavBar.vue";

const store = useStore();
const user = computed(() => store.getters.getUser);

onMounted(() => {
    if (!user.value) {
        store.dispatch('fetchUser');
    }
});

</script>

<template>
    <div>
        <NavBar />
        <div class="container mt-5">
            <h2>Dashboard</h2>
            <p v-if="user">Welcome, {{ user.name }}!</p>
            <p v-else>Loading user information...</p>
        </div>
    </div>
</template>

<style scoped>

</style>
