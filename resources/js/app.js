import { createApp } from 'vue';
import App from './srs/App.vue';
import router from './srs/router/router.js';
import store from './srs/store/index.js';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import axiosInstance from "./srs/services/axios.js";

const app = createApp(App);

app.use(store);
app.use(router);

axiosInstance.get('/sanctum/csrf-cookie').then(() => {
    if (store.getters.isAuthenticated) {
        store.dispatch('fetchUser');
    }
    app.mount('#app');
});
