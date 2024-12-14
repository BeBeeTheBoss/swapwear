<template>
    <Layout>
        <div class="container mt-8 h4">
            Dashboard
        </div>
        <div class="container rounded-lg pt-3" style="height:90vh">
            <div class="row">

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



})

</script>

<style setup>
</style>
