<template>
    <div class="container flex justify-center items-center" style="height:100vh">
        <div style="position:absolute;top:20px;right:20px">
            <ErrorToast class="my-1" v-for="toast in errorToasts" :key="toast" :header="toast" />
        </div>
        <form class="form border shadow">
            <div class="h4 mb-4 fw-bold text-center" :style="{ color: $themeColor }">
                Lovecar Business Dashboard
            </div>
            <div class="flex-column">
                <label :style="{ color: $themeColor }">Phone </label>
            </div>
            <div class="inputForm" style="position:relative">
                <font-awesome-icon icon="fa-solid fa-phone" :style="{ color: $themeColor }"
                    style="position:absolute;left:10px" />
                <input type="text" v-model="form.phone" class="input" placeholder="Enter your Phone" @keyup.enter="login">
            </div>

            <div class="flex-column">
                <label :style="{ color: $themeColor }">Password </label>
            </div>
            <div class="inputForm" style="position:relative">
                <font-awesome-icon icon="fa-solid fa-lock" :style="{ color: $themeColor }"
                    style="position:absolute;left:10px" />
                <input :type="showPw ? 'text' : 'password'" v-model="form.password" class="input"
                    placeholder="Enter your Password" @keyup.enter="login">
                <svg style="cursor:pointer" @click="showPw = !showPw" viewBox="0 0 576 512" height="1em"
                    xmlns="http://www.w3.org/2000/svg">
                    <path :style="{ fill: $themeColor }"
                        d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z">
                    </path>
                </svg>
            </div>

            <div class="flex-row">
                <!-- <div>
                    <input type="checkbox">
                    <label @click="rememberMe = !rememberMe" class="ms-1">Remember me </label>
                </div> -->
                <span class="span">Forgot password?</span>
            </div>
            <button type="button" :style="{ backgroundColor: $themeColor }" @click="login" class="button-submit">
                <div v-if="loading" style="width:25px;height:25px" class="spinner-border text-white" role="status">
                </div>
                <span v-if="!loading" style="transition:0.5">
                    Log in
                </span>
            </button>
        </form>
    </div>
</template>

<script setup>
import ErrorToast from './Components/ErrorToast.vue';

import axios from 'axios';
import { inject, ref, onMounted } from "vue";
import { useForm, router } from '@inertiajs/vue3';

const showPw = ref(false);
const loading = ref(false);
const baseUrl = inject('baseUrl');
const errorToasts = ref([]);

const form = useForm({
    phone: "",
    password: ""
});

onMounted(() => {
    let token = localStorage.getItem('token');
    if (token) {
        router.post('/setToken',{token: token});
        router.get('/dashboard');
    }
})

const login = () => {

    loading.value = true;

    axios.post(`${baseUrl}/loginBA`, {
        phone: form.phone,
        password: form.password,
    }).then((response) => {
        loading.value = false;
        console.log(response);

        if (response.data.data == null) {
            errorToasts.value.push(response.data.message);
            setTimeout(() => {
                errorToasts.value.shift()
            }, 2000)
        }

        if (response.data.user) {
            localStorage.setItem('token', response.data.token);
            localStorage.setItem('user', response.data.user);
            router.post('/setToken',{token: response.data.token});
            router.get('/dashboard');
        }


    }).catch((error) => {
        console.log(error);

        loading.value = false;
        if (error.response.data.errors.phone) {
            errorToasts.value.push(error.response.data.errors.phone[0]);
            setTimeout(() => {
                errorToasts.value.shift()
            }, 2000)
        }

        if (error.response.data.errors.password) {
            errorToasts.value.push(error.response.data.errors.password[0]);
            setTimeout(() => {
                errorToasts.value.shift()
            }, 2000)
        }

    })
};

</script>

<style scoped>
.form {
    display: flex;
    flex-direction: column;
    gap: 10px;
    background-color: #ffffff;
    padding: 30px;
    width: 450px;
    border-radius: 20px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
}

::placeholder {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
}

.form button {
    align-self: flex-end;
}

.flex-column>label {
    color: #151717;
    font-weight: 600;
}

.inputForm {
    border: 1.5px solid #ecedec;
    border-radius: 10px;
    height: 50px;
    display: flex;
    align-items: center;
    padding-left: 30px;
    transition: 0.2s ease-in-out;
}

.input {
    margin-left: 10px;
    border-radius: 10px;
    border: none;
    width: 85%;
    height: 100%;
}

.input:focus {
    outline: none;
}

.inputForm:focus-within {
    border: 1.5px solid #2d79f3;
}

.flex-row {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 10px;
    justify-content: space-between;
}

.flex-row>div>label {
    font-size: 14px;
    color: black;
    font-weight: 400;
}

.span {
    font-size: 14px;
    margin-left: 5px;
    color: #2d79f3;
    font-weight: 500;
    cursor: pointer;
}

.button-submit {
    margin: 20px 0 10px 0;
    border: none;
    color: white;
    font-size: 15px;
    font-weight: 500;
    border-radius: 10px;
    height: 50px;
    width: 100%;
    cursor: pointer;
}

.button-submit:hover {
    background-color: #252727;
}

.p {
    text-align: center;
    color: black;
    font-size: 14px;
    margin: 5px 0;
}

.btn {
    margin-top: 10px;
    width: 100%;
    height: 50px;
    border-radius: 10px;
    display: flex;
    justify-content: center;
    align-items: center;
    font-weight: 500;
    gap: 10px;
    border: 1px solid #ededef;
    background-color: white;
    cursor: pointer;
    transition: 0.2s ease-in-out;
}

.btn:hover {
    border: 1px solid #2d79f3;
    ;
}
</style>
