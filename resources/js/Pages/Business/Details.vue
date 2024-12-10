<template>
    <Layout>
        <div class="container flex justify-center items-center" style="position:absolute;top:20px;height:120vh"
            v-if="loading">
            <Carloader style="margin-bottom: 100px;" />
        </div>
        <div class="container mt-8" v-else style="min-height:120vh">
            <!-- <div class="d-lg-block d-md-block d-sm-none d-none"
                style="width:100%;height:200px;background-color:#ECECEE"></div>
            <div class="d-lg-none d-md-none d-sm-block d-block"
                style="width:100%;height:130px;background-color:#ECECEE"></div>
            <div
                class="flex justify-content-lg-between justify-content-md-between justify-content-sm-center justify-content-center">
                <div class="flex">
                    <div style="position: relative">
                        <img v-if="business?.logo" :src="business.logo"
                            class="rounded-circle ms-lg-4 ms-md-4 ms-sm-0 ms-0"
                            style="border:solid 7px #FFFFFF;width:200px;height:200px;object-fit:cover;object-position:center;border-radius:10px;margin-top:-50px">
                        <img v-else src="../images/no_image.png"
                            class="rounded-circle ms-lg-4 ms-md-4 ms-sm-0 ms-0"
                            style="padding:14px;background-color:white;border:solid 7px #FFFFFF;width:200px;height:200px;object-fit:cover;object-position:center;border-radius:10px;margin-top:-50px">
                        <img src="../images/approved.png" style="width:40px;position:absolute;right:10px;bottom:3px">
                    </div>
                    <div class="ms-4 mt-3 d-lg-block d-md-none d-sm-none d-none">
                        <div class="h4 fw-bold">{{ business.name }}</div>
                        <div class="text-muted">
                            {{ business.address }}
                        </div>
                        <div class="text-muted mt-2">
                            {{ business.phone }}
                        </div>
                    </div>
                </div>
                <div class="d-lg-block d-md-block d-sm-none d-none">
                    <button class="btn mt-6 rounded" :style="`border:solid 1px ${$themeColor};color:${$themeColor}`">
                        Edit business
                    </button>
                </div>
            </div> -->
        </div>
    </Layout>
</template>

<script setup>
import Layout from '../Layouts/Layout.vue';
import axios from 'axios';
import Carloader from '../Components/Carloader.vue';
import { onMounted, ref, inject } from 'vue';

const baseUrl = inject('baseUrl');

const loading = ref(false);
const business = ref(null);

const props = defineProps({
    shop_id: Number
})

onMounted(() => {

    let token = localStorage.getItem('token');
    if (!token) {
        router.get('/login');
    }

    loading.value = true;
    axios.get(`${baseUrl}/shops/show?shop_id=${props.shop_id}`, {
        headers: {
            Authorization: `Bearer ${token}`
        }
    })
        .then((response) => {
            console.log(response);
            business.value = response.data.data;
            loading.value = false;
        }).catch((error) => {
            console.log(error);
            loading.value = false;

        })
})

</script>

<style></style>
