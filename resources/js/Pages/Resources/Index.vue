<template>
    <Layout>
        <v-breadcrumbs class="my-5" :items="headers"></v-breadcrumbs>
        <div class="container h5">
            Resources
        </div>
        <div class="container rounded-lg pt-3" style="height:90vh">
            <div class="row">
                <div class="col-lg-3 col-md-4 col-sm-6 col-6 ">
                    <Link :href="route('main-categories.get')">
                    <div class="border bg-blur shadow-sm p-2 rounded-lg cursor-pointer">
                        <img src="https://i0.wp.com/www.isewthereforeiam.com/wp-content/uploads/2018/01/a095dc97-e232-4cb5-bf8e-60eaeda233eb.jpg?ssl=1"
                            style="width:100%;height:120px;object-fit:cover;object-position:center">
                        <div class="fw-bold pt-2 ps-1">Main Categories</div>
                    </div>
                    </Link>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6 col-6 ">
                    <div class="border bg-blur shadow-sm p-2 rounded-lg cursor-pointer">
                        <img src="https://api.photon.aremedia.net.au/wp-content/uploads/sites/6/2024/10/off-the-rack-wedding-dress.png?resize=1200%2C630"
                            style="width:100%;height:120px;object-fit:cover;object-position:center">
                        <div class="fw-bold pt-2 ps-1">Sub Categories</div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import Layout from '../Layouts/Layout.vue';
import Button from '../Components/Button.vue';
import { ref } from 'vue';

const headers = ['Resources'];

</script>

<style scoped>
.bg-blur{
    background-color: rgba(255, 255, 255, 0.236);
    backdrop-filter: blur(10px);
}
</style>
