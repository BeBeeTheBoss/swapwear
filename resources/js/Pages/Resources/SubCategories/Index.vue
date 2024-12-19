<template>
    <Layout>
        <v-breadcrumbs class="my-5" :items="headers"></v-breadcrumbs>
        <div class="container">
            <Link :href="route('resources')">
            <span class="h5 cursor-pointer">
                <font-awesome-icon icon="fa-solid fa-chevron-left" /> Back
            </span>
            </Link>
        </div>
        <div class="container mt-8 flex justify-between items-center">
            <div class="h5">
                Sub Categories
            </div>
            <div>
                <Link :href="route('sub-categories.create')">
                <Button text="Create" icon="fa-solid fa-plus" />
                </Link>
            </div>
        </div>
        <div class="container mb-20">
            <div class="row mt-3">
                <div class="my-2" v-for="item in data.data" :key="item.id">
                    <div class="border shadow-sm p-3 bg-blur rounded-lg">
                        <div class="flex items-center">
                            <img :src="item.icon"
                                style="width:30px;height:30px;object-fit:cover;object-position:center">
                            <span class="fw-bold ms-2">
                                {{ item.name }}
                            </span>
                        </div>
                        <div class="row mt-4">
                            <div class="col-lg-2 col-md-4 col-sm-6 col-6 cursor-pointer my-2"
                                v-for="sub_category in item.sub_categories" :key="sub_category">
                                <Link :href="route('sub-categories.edit', { id: sub_category.id })">
                                <div class="shadow-sm" style="border: rgba(255, 255, 255, 0.429) solid 1px;padding: 10px; border-radius: 6px;">
                                    <div class="flex">
                                        <img :src="sub_category.icon" class="me-2"
                                            style="width:20px;height:20px;object-fit:cover;object-position:center">
                                        {{ sub_category.name }}
                                    </div>
                                </div>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import Layout from '../../Layouts/Layout.vue';
import Button from '../../Components/Button.vue';
import { onMounted, onUpdated, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';

const page = usePage();
const toast = useToast();

const headers = ['Resources', 'Sub Categories'];

const props = defineProps({
    data: Array
})

onUpdated(() => {

    if (page.props.flash.success) {
        toast.success(page.props.flash.success);
        page.props.flash.success = null;
    } else if (page.props.flash.error) {
        toast.error(page.props.flash.error);
        page.props.flash.error = null;
    }

})


</script>

<style scoped>
.bg-blur {
    background-color: rgba(255, 255, 255, 0.236);
    backdrop-filter: blur(10px);
}
</style>
