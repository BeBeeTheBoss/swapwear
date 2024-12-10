<template>
    <Layout>
        <div class="container mt-8 h4">
            My Businesses
        </div>
        <div class="container flex justify-center items-center" style="position:absolute;top:20px;height:120vh" v-if="loading">
            <Carloader style="margin-bottom: 100px;"/>
        </div>
        <div v-else class="container rounded-lg pt-3" style="height:90vh">
            <div class="row">
                <Card v-for="business in businesses" :key="business" :business="business"/>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import Card from './Components/Card.vue';
import Carloader from './Components/Carloader.vue';
import { onMounted,inject,ref } from 'vue';
import { router } from '@inertiajs/vue3';
import Layout from './Layouts/Layout.vue';
import axios from 'axios';

const loading = ref(false);
const baseUrl = inject('baseUrl');
const businesses = ref(null);

onMounted(() => {
    let token = localStorage.getItem('token');
    if(!token){
        router.get('/login');
    }

    loading.value = true;

    axios.get(`${baseUrl}/shops/getShops`,{
        headers: {
            Authorization : `Bearer ${token}`
        }
    }).then((response) => {
        console.log(response);
        businesses.value = response.data.data;
        loading.value = false;

    }).catch((error) => {

    })

})

</script>

<style setup>
</style>
